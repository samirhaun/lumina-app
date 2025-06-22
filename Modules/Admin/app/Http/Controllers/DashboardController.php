<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Apenas carrega a view principal
    public function index()
    {
        return view('admin::dashboard');
    }

    public function getDashboardData(Request $request)
    {
        $period = $request->input('period', 'month');
        [$currentStartDate, $currentEndDate] = $this->getDateRange($period);
        [$previousStartDate, $previousEndDate] = $this->getDateRange($period, true);

        // --- DADOS DO PERÍODO ATUAL ---
        $currentSalesQuery = DB::table('orders')->where('status', 'Concluído')->whereBetween('order_date', [$currentStartDate, $currentEndDate]);
        $currentSalesData = (clone $currentSalesQuery)->select(DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(id) as count'))->first();
        $currentGrossProfit = DB::table('order_items')->join('orders', 'order_items.order_id', '=', 'orders.id')->where('orders.status', 'Concluído')->whereBetween('orders.order_date', [$currentStartDate, $currentEndDate])->sum(DB::raw('quantity * (price_per_unit - cost_per_unit)'));

        // --- DADOS DO PERÍODO ANTERIOR PARA COMPARAÇÃO ---
        $previousSalesQuery = DB::table('orders')->where('status', 'Concluído')->whereBetween('order_date', [$previousStartDate, $previousEndDate]);
        $previousSalesData = (clone $previousSalesQuery)->select(DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(id) as count'))->first();
        $previousGrossProfit = DB::table('order_items')->join('orders', 'order_items.order_id', '=', 'orders.id')->where('orders.status', 'Concluído')->whereBetween('orders.order_date', [$previousStartDate, $previousEndDate])->sum(DB::raw('quantity * (price_per_unit - cost_per_unit)'));



        // total e contagem de Compras Pagas (Saídas)
        $currentPurchasesQuery = DB::table('purchases')
            ->where('status', 'Pago')
            ->whereBetween('purchase_date', [$currentStartDate, $currentEndDate]);
        $currentPurchasesData = (clone $currentPurchasesQuery)
            ->select(DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(id) as count'))
            ->first();

        $previousPurchasesQuery = DB::table('purchases')
            ->where('status', 'Pago')
            ->whereBetween('purchase_date', [$previousStartDate, $previousEndDate]);
        $previousPurchasesData = (clone $previousPurchasesQuery)
            ->select(DB::raw('SUM(total_amount) as total'))
            ->first();

        // na montagem do JSON, dentro de 'kpis':
        $response['kpis']['expenses'] = [
            'value'      => $currentPurchasesData->total ?? 0,
            'comparison' => $this->calculatePercentageChange($currentPurchasesData->total, $previousPurchasesData->total ?? 0),
        ];

        // --- DADOS PARA OS GRÁFICOS (BASEADO NO PERÍODO ATUAL) ---
        // Gráfico de Fluxo de Caixa
        $salesFlowData = (clone $currentSalesQuery)->select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(grand_total) as total'))->groupBy('date')->pluck('total', 'date');
        $purchasesFlowData = DB::table('purchases')->where('status', 'Pago')->whereBetween('purchase_date', [$currentStartDate, $currentEndDate])->select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(total_amount) as total'))->groupBy('date')->pluck('total', 'date');

        $flowChartLabels = [];
        $flowChartSales = [];
        $flowChartPurchases = [];
        for ($date = $currentStartDate->copy(); $date <= $currentEndDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $flowChartLabels[] = $date->format('d/m');
            $flowChartSales[] = $salesFlowData[$formattedDate] ?? 0;
            $flowChartPurchases[] = $purchasesFlowData[$formattedDate] ?? 0;
        }

        // Gráfico de Produtos Rentáveis
        $topProducts = DB::table('order_items')->join('products', 'order_items.product_id', '=', 'products.id')->join('orders', 'order_items.order_id', '=', 'orders.id')->where('orders.status', 'Concluído')->whereBetween('orders.order_date', [$currentStartDate, $currentEndDate])->select('products.name', DB::raw('SUM(order_items.quantity * (order_items.price_per_unit - order_items.cost_per_unit)) as total_profit'), DB::raw('SUM(order_items.quantity) as total_quantity_sold'))->groupBy('products.name')->orderBy('total_profit', 'desc')->limit(5)->get();

        // --- MONTA A RESPOSTA JSON ---
        $response = [
            'kpis' => [
                'sales'   => [
                    'value'      => $currentSalesData->total   ?? 0,
                    'comparison' => $this->calculatePercentageChange($currentSalesData->total,   $previousSalesData->total)
                ],
                'expenses' => [
                    'value'      => $currentPurchasesData->total ?? 0,
                    'comparison' => $this->calculatePercentageChange($currentPurchasesData->total, $previousPurchasesData->total ?? 0)
                ],
                'sales_count' => [
                    'value'      => $currentSalesData->count   ?? 0,
                    'comparison' => $this->calculatePercentageChange($currentSalesData->count,   $previousSalesData->count)
                ],
                'profit'  => [
                    'value'      => $currentGrossProfit        ?? 0,
                    'comparison' => $this->calculatePercentageChange($currentGrossProfit,        $previousGrossProfit)
                ],
            ],
            'charts' => [
                'flow' => [
                    'labels'    => $flowChartLabels,
                    'sales'     => $flowChartSales,
                    'purchases' => $flowChartPurchases,
                ],
                'top_products' => [
                    'labels'        => $topProducts->pluck('name'),
                    'profit_data'   => $topProducts->pluck('total_profit'),
                    'quantity_data' => $topProducts->pluck('total_quantity_sold'),
                ],
            ],
        ];

        return response()->json($response);
    }
    // Helper para calcular o range de datas
    private function getDateRange(string $period, bool $previous = false): array
    {
        $today = Carbon::now();
        $subUnit = $previous ? 2 : 1;

        if ($period === 'today') {
            $start = $today->copy()->subDays($previous ? 1 : 0)->startOfDay();
            $end = $today->copy()->subDays($previous ? 1 : 0)->endOfDay();
        } elseif ($period === 'week') {
            $start = $today->copy()->subWeeks($previous ? 1 : 0)->startOfWeek();
            $end = $today->copy()->subWeeks($previous ? 1 : 0)->endOfWeek();
        } elseif ($period === 'fortnight') {
            $start = $today->copy()->subWeeks($previous ? 2 : 0)->startOfDay();
            $end = $today->copy()->subDays($previous ? 15 : 0)->endOfDay();
        } else { // month (padrão)
            $start = $today->copy()->subMonths($previous ? 1 : 0)->startOfMonth();
            $end = $today->copy()->subMonths($previous ? 1 : 0)->endOfMonth();
        }
        return [$start, $end];
    }

    // Helper para calcular a mudança percentual
    private function calculatePercentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}
