<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashFlowController extends Controller
{
    /**
     * Apenas exibe a página principal do relatório.
     */
    public function index()
    {
        return view('admin::cash-flow.index');
    }

    /**
     * Gera o relatório de fluxo de caixa com base em um período.
     */
    public function generateReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query para buscar ENTRADAS (Vendas Concluídas)
        $inflows = DB::table('orders')
            ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
            ->where('orders.status', 'Concluído')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->select(
                'orders.order_date as date',
                DB::raw("CONCAT('Venda #', orders.id, ' para ', IFNULL(clients.name, 'Consumidor Final')) as description"),
                'orders.grand_total as credit',
                DB::raw("0 as debit")
            );

        // Query para buscar SAÍDAS (Compras Pagas)
        $outflows = DB::table('purchases')
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchases.status', 'Pago')
            ->whereBetween('purchases.purchase_date', [$startDate, $endDate])
            ->select(
                'purchases.purchase_date as date',
                DB::raw("CONCAT('Compra #', purchases.id, ' de ', IFNULL(suppliers.name, 'Fornecedor avulso')) as description"),
                DB::raw("0 as credit"),
                'purchases.total_amount as debit'
            );
        
        // ================================================================
        // NOVO: Query para buscar Lançamentos Manuais (Investimentos, etc.)
        // ================================================================
        $manualTransactions = DB::table('financial_transactions')
            ->leftJoin('financial_categories', 'financial_transactions.category_id', '=', 'financial_categories.id')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select(
                'financial_transactions.transaction_date as date',
                DB::raw("CONCAT(IFNULL(CONCAT(financial_categories.name, ': '), ''), financial_transactions.description) as description"),
                DB::raw("CASE WHEN financial_transactions.type = 'credit' THEN amount ELSE 0 END as credit"),
                DB::raw("CASE WHEN financial_transactions.type = 'debit' THEN amount ELSE 0 END as debit")
            );
        
        // Unifica os TRÊS resultados e ordena por data
        $transactions = $inflows
                        ->unionAll($outflows)
                        ->unionAll($manualTransactions) // <-- Unindo a nova consulta
                        ->orderBy('date')
                        ->get();

        // Calcula os totais do período (esta parte não muda, pois já trabalha com o resultado unificado)
        $totalInflow = $transactions->sum('credit');
        $totalOutflow = $transactions->sum('debit');
        $netBalance = $totalInflow - $totalOutflow;

        return response()->json([
            'transactions' => $transactions,
            'summary' => [
                'total_inflow' => $totalInflow,
                'total_outflow' => $totalOutflow,
                'net_balance' => $netBalance,
            ]
        ]);
    }
}