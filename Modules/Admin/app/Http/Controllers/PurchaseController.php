<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{

    /**
     * Mostra a lista de compras.
     */
    public function index()
    {
        // CORREÇÃO: Buscando os dados necessários para o modal de recebimento
        // que existe na página de listagem (index).
        $products = DB::table('products')->orderBy('name')->get(['id', 'name', 'product_type_id']);
        $productTypes = DB::table('product_types')->orderBy('name')->get(['id', 'name']);
        $miscItems = DB::table('misc_items')
            ->join('misc_categories', 'misc_items.misc_category_id', '=', 'misc_categories.id')
            ->orderBy('misc_categories.name')->orderBy('misc_items.name')
            ->get(['misc_items.id', 'misc_items.name as item_name', 'misc_items.misc_category_id', 'misc_categories.name as category_name']);
        $miscCategories = DB::table('misc_categories')->orderBy('name')->get(['id', 'name']);

        // Enviando as variáveis para a view com o `compact()`
        return view('admin::purchases.index', compact('products', 'productTypes', 'miscItems', 'miscCategories'));
    }
    /**
     * Fornece dados para a DataTable da página index.
     */
    public function data()
    {
        $purchases = DB::table('purchases')
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'purchases.id',
                'purchases.purchase_date',
                'purchases.due_date',
                'purchases.total_amount',
                'purchases.status',
                'purchases.received_at',
                'suppliers.name as supplier_name'
            )
            ->orderBy('purchases.purchase_date', 'desc')
            ->get();

        return response()->json(['data' => $purchases]);
    }

    /**
     * Mostra o formulário de criação de uma nova compra.
     */
    public function create()
    {
        $suppliers = DB::table('suppliers')->orderBy('name')->get(['id', 'name']);
        $products = DB::table('products')->orderBy('name')->get(['id', 'name', 'product_type_id']);
        $productTypes = DB::table('product_types')->orderBy('name')->get(['id', 'name']);
        $miscItems = DB::table('misc_items')
            ->join('misc_categories', 'misc_items.misc_category_id', '=', 'misc_categories.id')
            ->orderBy('misc_categories.name')->orderBy('misc_items.name')
            ->get(['misc_items.id', 'misc_items.name as item_name', 'misc_items.misc_category_id', 'misc_categories.name as category_name']);
        $miscCategories = DB::table('misc_categories')->orderBy('name')->get(['id', 'name']);

        return view('admin::purchases.create', compact('suppliers', 'products', 'miscItems', 'miscCategories', 'productTypes'));
    }

    /**
     * Salva uma nova compra no banco de dados.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'due_date'      => 'nullable|date|after_or_equal:purchase_date',
            'notes'         => 'nullable|string',
            'status'        => 'required|in:Pendente,Pago,Atrasado',
            'items'         => 'required|array|min:1',
            'items.*.id'    => 'required|integer',
            'items.*.type'  => 'required|string|in:Product,MiscItem',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchaseId = DB::table('purchases')->insertGetId([
                'supplier_id'   => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'due_date'      => $request->due_date,
                'notes'         => $request->notes,
                'status'        => $request->status,
                'total_amount'  => $totalAmount, // Valor total calculado
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            foreach ($request->items as $item) {
                DB::table('purchase_items')->insert([
                    'purchase_id'      => $purchaseId,
                    'purchasable_id'   => $item['id'],
                    'purchasable_type' => $item['type'],
                    'quantity'         => $item['quantity'],
                    'unit_cost'        => $item['unit_cost'],
                    'total_cost'       => $item['quantity'] * $item['unit_cost'],
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => 'Compra registrada com sucesso!',
                'redirect_url' => route('admin.purchases.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao salvar a compra: ' . $e->getMessage()], 500);
        }
    }

    public function details($id)
    {
        $purchase = DB::table('purchases')
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchases.id', $id)
            ->select('purchases.*', 'suppliers.name as supplier_name')
            ->first();

        if (!$purchase) {
            return response()->json(['error' => 'Compra não encontrada'], 404);
        }

        $items = DB::table('purchase_items')
            ->where('purchase_id', $id)
            ->get();

        // Para cada item, precisamos descobrir o nome real (seja de Product ou MiscItem)
        foreach ($items as $item) {
            if ($item->purchasable_type === 'Product') {
                $product = DB::table('products')->where('id', $item->purchasable_id)->first();
                $item->name = $product ? $product->name : 'Produto não encontrado';
            } else if ($item->purchasable_type === 'MiscItem') {
                $miscItem = DB::table('misc_items')->where('id', $item->purchasable_id)->first();
                $item->name = $miscItem ? $miscItem->name : 'Item diverso não encontrado';
            }
        }

        return response()->json([
            'purchase' => $purchase,
            'items' => $items
        ]);
    }

    /**
     * Atualiza APENAS o status de pagamento de uma compra.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pendente,Pago,Atrasado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('purchases')->where('id', $id)->update(['status' => $request->status]);
        return response()->json(['success' => 'Status de pagamento atualizado!']);
    }

    /**
     * Processa o recebimento de itens, atualiza estoque e recalcula custos.
     */
    public function receiveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                     => 'required|array',
            'items.*.item_db_id'        => 'required|integer',
            'items.*.purchasable_id'    => 'required|integer',
            'items.*.purchasable_type'  => 'required|string|in:Product,MiscItem',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.notes'             => 'nullable|string',
            'items.*.unit_cost'         => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $totalReceivedValue = 0;

            foreach ($request->items as $receivedItem) {
                $unitCost = 0;

                // Atualiza ou cria o registro na tabela de itens da compra
                if ($receivedItem['item_db_id'] != 0) {
                    $originalItem = DB::table('purchase_items')->where('id', $receivedItem['item_db_id'])->first();
                    if ($originalItem) {
                        $unitCost = $originalItem->unit_cost;
                        DB::table('purchase_items')->where('id', $originalItem->id)->update([
                            'quantity_received' => $receivedItem['quantity_received'],
                            'notes' => $receivedItem['notes']
                        ]);
                    }
                } else {
                    $unitCost = $receivedItem['unit_cost'];
                    DB::table('purchase_items')->insert([
                        'purchase_id' => $id,
                        'purchasable_id' => $receivedItem['purchasable_id'],
                        'purchasable_type' => $receivedItem['purchasable_type'],
                        'quantity' => 0,
                        'quantity_received' => $receivedItem['quantity_received'],
                        'unit_cost' => $unitCost,
                        'total_cost' => $receivedItem['quantity_received'] * $unitCost,
                        'notes' => 'Item adicionado no recebimento. ' . $receivedItem['notes'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $totalReceivedValue += $receivedItem['quantity_received'] * $unitCost;

                // =================================================================
                // NOVA LÓGICA UNIVERSAL DE ESTOQUE E CUSTO MÉDIO
                // =================================================================
                $qtyReceived = $receivedItem['quantity_received'];
                if ($qtyReceived > 0) {
                    $stockableId = $receivedItem['purchasable_id'];
                    $stockableType = $receivedItem['purchasable_type'];
                    $parentTable = ''; // Tabela do item (products ou misc_items)

                    if ($stockableType === 'Product') {
                        $parentTable = 'products';
                    } else if ($stockableType === 'MiscItem') {
                        $parentTable = 'misc_items';
                    }

                    if ($parentTable) {
                        // Trava as linhas para segurança
                        $itemRecord = DB::table($parentTable)->where('id', $stockableId)->lockForUpdate()->first();
                        $stock = DB::table('inventory')->where('stockable_id', $stockableId)->where('stockable_type', $stockableType)->lockForUpdate()->first();

                        if (!$stock) {
                            DB::table('inventory')->insert(['stockable_id' => $stockableId, 'stockable_type' => $stockableType, 'quantity_on_hand' => 0]);
                            $stock = DB::table('inventory')->where('stockable_id', $stockableId)->where('stockable_type', $stockableType)->first();
                        }

                        $oldQty = $stock->quantity_on_hand;
                        $oldAvgCost = $itemRecord->average_cost;

                        $newTotalQty = $oldQty + $qtyReceived;
                        $newAvgCost = $oldAvgCost;
                        if ($newTotalQty > 0) {
                            $newAvgCost = (($oldAvgCost * $oldQty) + ($unitCost * $qtyReceived)) / $newTotalQty;
                        }

                        // Atualiza o estoque na nova tabela 'inventory'
                        DB::table('inventory')->where('id', $stock->id)->update(['quantity_on_hand' => $newTotalQty]);
                        // Atualiza o custo médio na tabela pai (products ou misc_items)
                        DB::table($parentTable)->where('id', $stockableId)->update(['average_cost' => $newAvgCost]);
                    }
                }
            }

            // Atualiza o registro principal da compra
            DB::table('purchases')->where('id', $id)->update([
                'received_amount' => $totalReceivedValue,
                'received_at'     => now()
            ]);

            DB::commit();

            return response()->json(['success' => 'Recebimento registrado com sucesso! Estoque e custos atualizados.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao processar recebimento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna os itens de uma compra específica para a sub-tabela (child row).
     */
    public function showItems($id)
    {
        $items = DB::table('purchase_items')
            ->where('purchase_id', $id)
            ->get();

        // Para cada item, precisamos buscar o nome real
        foreach ($items as $item) {
            if ($item->purchasable_type === 'Product') {
                $product = DB::table('products')->where('id', $item->purchasable_id)->value('name');
                $item->name = $product ?: 'Produto não encontrado';
            } else if ($item->purchasable_type === 'MiscItem') {
                $miscItem = DB::table('misc_items')->where('id', $item->purchasable_id)->value('name');
                $item->name = $miscItem ?: 'Item diverso não encontrado';
            }
        }

        return response()->json(['data' => $items]);
    }

    /**
     * Exclui uma compra.
     */
    public function destroy($id)
    {
        // Graças ao 'ON DELETE CASCADE', os itens da compra serão excluídos automaticamente.
        DB::table('purchases')->where('id', $id)->delete();
        return response()->json(['success' => 'Compra removida com sucesso!']);
    }
}
