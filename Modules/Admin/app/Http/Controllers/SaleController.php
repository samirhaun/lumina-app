<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $clients = DB::table('clients')
            ->orderBy('name')
            ->get(['id', 'name']);

        $miscCategories = DB::table('misc_categories')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin::sales.index', compact('clients', 'miscCategories'));
    }

    /**
     * Fornece dados para a DataTable da página index.
     * VERSÃO CORRIGIDA E MAIS ROBUSTA
     */
    public function data()
    {
        $orders = DB::table('orders')
            ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
            ->select(
                'orders.id',
                'clients.name as client_name', // O leftJoin garante que mesmo sem cliente, a venda apareça
                'orders.order_date',
                'orders.status',
                'orders.grand_total'
            )
            ->orderBy('orders.id', 'desc')
            ->get();

        return response()->json(['data' => $orders]);
    }
    /**
     * Mostra o formulário para criar uma nova venda.
     */
    public function create()
    {
        $clients = DB::table('clients')->orderBy('name')->get(['id', 'name']);
        $miscCategories = DB::table('misc_categories')->orderBy('name')->get(['id', 'name']);

        return view('admin::sales.create', compact('clients', 'miscCategories'));
    }
    /**
     * Busca produtos para o Select2 com AJAX, agora por NOME ou ID.
     */
    // Dentro da classe SaleController

    public function searchProducts(Request $request)
    {
        $searchTerm = $request->input('q', '');

        $query = DB::table('products')
            ->leftJoin('inventory', function ($join) {
                $join->on('products.id', '=', 'inventory.stockable_id')
                    ->where('inventory.stockable_type', '=', 'Product');
            })
            ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->select(
                'products.id',
                // --- LINHA ALTERADA AQUI ---
                // Adicionamos a quantidade em estoque ao texto que será exibido no Select2.
                DB::raw("CONCAT(IFNULL(products.code, 'S/C'), ' - ', products.name, ' (Estoque: ', IFNULL(inventory.quantity_on_hand, 0), ')') as text"),
                'products.sale_price',
                'products.average_cost',
                DB::raw('IFNULL(inventory.quantity_on_hand, 0) as quantity_on_hand'),
                'product_types.name as category'
            );

        if ($searchTerm) {
            $query->where(function ($sub) use ($searchTerm) {
                $sub->where('products.name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('products.code', 'LIKE', "%{$searchTerm}%");
                if (is_numeric($searchTerm)) {
                    $sub->orWhere('products.id', (int)$searchTerm);
                }
            });
        }

        $items = $query->limit(100)->get();

        $grouped = $items
            ->groupBy('category')
            ->map(function ($group, $categoryName) {
                return [
                    'text'     => $categoryName ?? 'Sem categoria',
                    'children' => $group->map(function ($item) {
                        return [
                            'id'               => $item->id,
                            'text'             => $item->text,
                            'sale_price'       => $item->sale_price,
                            'average_cost'     => $item->average_cost,
                            'quantity_on_hand' => $item->quantity_on_hand,
                        ];
                    })->values()->all(),
                ];
            })->values();

        return response()->json(['results' => $grouped]);
    }
    /**
     * Busca Itens Diversos para o Select2 com AJAX.
     * VERSÃO CORRIGIDA com LEFT JOIN para incluir itens com estoque zero.
     */
    public function searchMiscItems(Request $request)
    {
        $searchTerm = $request->input('q', '');
        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return response()->json(['results' => []]);
        }

        $query = DB::table('misc_items')
            ->leftJoin('inventory', function ($join) {
                $join->on('misc_items.id', '=', 'inventory.stockable_id')
                    ->where('inventory.stockable_type', '=', 'MiscItem');
            })
            ->where('misc_items.misc_category_id', $categoryId)
            ->select(
                'misc_items.id',
                'misc_items.name as text',
                'misc_items.average_cost',
                // Usa IFNULL para garantir que estoque seja 0 se não houver registro
                DB::raw('IFNULL(inventory.quantity_on_hand, 0) as quantity_on_hand')
            );

        if ($searchTerm) {
            $query->where('misc_items.name', 'LIKE', '%' . $searchTerm . '%');
        }

        return response()->json(['results' => $query->limit(20)->get()]);
    }
    /**
     * Salva a nova venda, seus itens e dá baixa no estoque.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'order_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'status' => 'required|string|in:Pendente,Concluído,Cancelado',
            // Validação para os arrays de itens
            'products' => 'required_without:misc_costs|array',
            'misc_costs' => 'required_without:products|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price_per_unit' => 'required|numeric|min:0',
            'products.*.cost_per_unit' => 'required|numeric|min:0',  // <– nova regra
            'misc_costs.*.misc_item_id' => 'required|integer|exists:misc_items,id',
            'misc_costs.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $products = $request->input('products', []);
            $miscCosts = $request->input('misc_costs', []);

            // 1. Calcula os totais
            $itemsSubtotal = collect($products)->sum(function ($item) {
                return $item['quantity'] * $item['price_per_unit'];
            });
            $grandTotal = $itemsSubtotal + $request->input('shipping_cost', 0) + $request->input('adjustment_amount', 0) - $request->input('discount_amount', 0);

            // 2. Insere o registro principal na tabela 'orders'
            $orderId = DB::table('orders')->insertGetId([
                'client_id' => $request->input('client_id'),
                'order_date' => $request->input('order_date'),
                'status' => $request->input('status'),
                'items_subtotal' => $itemsSubtotal,
                'shipping_cost' => $request->input('shipping_cost', 0),
                'discount_amount' => $request->input('discount_amount', 0),
                'adjustment_amount' => $request->input('adjustment_amount', 0),
                'adjustment_notes' => $request->input('adjustment_notes'),
                'grand_total' => $grandTotal,
                'payment_method' => $request->input('payment_method'),
                'notes' => $request->input('notes'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Itera sobre os PRODUTOS vendidos
            foreach ($products as $item) {
                DB::table('order_items')->insert([
                    'order_id'       => $orderId,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'price_per_unit' => $item['price_per_unit'],
                    'cost_per_unit'  => $item['cost_per_unit'],       // <– passe o custo do payload
                    'total_price'    => $item['quantity'] * $item['price_per_unit'],
                ]);

                // baixa no estoque se for 'Concluído'
                if ($request->input('status') === 'Concluído') {
                    DB::table('inventory')
                        ->where('stockable_id', $item['product_id'])
                        ->where('stockable_type', 'Product')
                        ->decrement('quantity_on_hand', $item['quantity']);
                }
            }

            // 5. Itera sobre os CUSTOS DIVERSOS utilizados
            foreach ($miscCosts as $item) {
                $miscItem = DB::table('misc_items')->where('id', $item['misc_item_id'])->first();
                DB::table('order_misc_costs')->insert([
                    'order_id' => $orderId,
                    'misc_item_id' => $item['misc_item_id'],
                    'quantity' => $item['quantity'],
                    'cost_per_unit' => $miscItem->average_cost,
                    'total_cost' => $item['quantity'] * $miscItem->average_cost,
                ]);

                // 6. Se o status for 'Concluído', dá baixa no estoque de ITENS DIVERSOS
                if ($request->input('status') === 'Concluído') {
                    DB::table('inventory')->where('stockable_id', $item['misc_item_id'])->where('stockable_type', 'MiscItem')->decrement('quantity_on_hand', $item['quantity']);
                }
            }

            DB::commit();
            return response()->json(['success' => 'Venda registrada com sucesso!', 'redirect_url' => route('admin.sales.index')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao salvar a venda: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostra os detalhes de uma venda específica (para o modal).
     * VERSÃO CORRIGIDA PARA INCLUIR CUSTOS DIVERSOS
     */
    public function show($id)
    {
        $order = DB::table('orders')
            ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
            ->where('orders.id', $id)
            ->select('orders.*', 'clients.name as client_name')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Venda não encontrada'], 404);
        }

        // Busca os PRODUTOS vendidos
        $items = DB::table('order_items')
            ->where('order_id', $id)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.*', 'products.name as product_name')
            ->get();

        // --- NOVA CONSULTA ADICIONADA AQUI ---
        // Busca os CUSTOS DIVERSOS associados à venda
        $miscCosts = DB::table('order_misc_costs')
            ->where('order_id', $id)
            ->join('misc_items', 'order_misc_costs.misc_item_id', '=', 'misc_items.id')
            ->select('order_misc_costs.*', 'misc_items.name as misc_item_name')
            ->get();

        // --- RESPOSTA JSON ATUALIZADA ---
        return response()->json([
            'order' => $order,
            'items' => $items,
            'misc_costs' => $miscCosts // Adiciona a nova lista à resposta
        ]);
    }

    /**
     * Atualiza o status de uma venda e ajusta o estoque se necessário.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Pendente,Concluído,Cancelado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $order = DB::table('orders')->where('id', $id)->first();
            $oldStatus = $order->status;
            $newStatus = $request->input('status');

            // Se o status não mudou, não faz nada
            if ($oldStatus === $newStatus) {
                DB::rollBack();
                return response()->json(['success' => 'O status já era o selecionado.']);
            }

            // Pega todos os itens do pedido
            $orderItems = DB::table('order_items')->where('order_id', $id)->get();

            // LÓGICA DE ESTOQUE
            // Caso 1: A venda foi CONCLUÍDA (sai do estoque)
            if ($newStatus === 'Concluído' && $oldStatus !== 'Concluído') {
                foreach ($orderItems as $item) {
                    DB::table('inventory')
                        ->where('stockable_id', $item->product_id)
                        ->where('stockable_type', 'Product')
                        ->decrement('quantity_on_hand', $item->quantity);
                }
            }
            // Caso 2: Uma venda que estava CONCLUÍDA foi CANCELADA/REVERTIDA (volta para o estoque)
            else if ($oldStatus === 'Concluído' && $newStatus !== 'Concluído') {
                foreach ($orderItems as $item) {
                    DB::table('inventory')
                        ->where('stockable_id', $item->product_id)
                        ->where('stockable_type', 'Product')
                        ->increment('quantity_on_hand', $item->quantity);
                }
            }

            // Finalmente, atualiza o status do pedido
            DB::table('orders')->where('id', $id)->update(['status' => $newStatus, 'updated_at' => now()]);

            DB::commit();
            return response()->json(['success' => 'Status da venda atualizado com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao atualizar status: ' . $e->getMessage()], 500);
        }
    }
}
