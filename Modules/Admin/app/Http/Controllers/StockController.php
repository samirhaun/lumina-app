<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request; // <-- Adicione este import
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; // <-- Adicione este import

class StockController extends Controller
{
    // Dentro da classe StockController

    public function index()
    {
        // Busca as listas de categorias para poder gerar as abas dinamicamente na view
        $productTypes = DB::table('product_types')->orderBy('name')->get();
        $miscCategories = DB::table('misc_categories')->orderBy('name')->get();

        return view('admin::stock.index', compact('productTypes', 'miscCategories'));
    }
    
    /**
     * Fornece os dados de estoque APENAS para produtos.
     */
    public function productStockData()
    {
        $products = DB::table('inventory')
            ->where('stockable_type', 'Product')
            ->join('products', 'products.id', '=', 'inventory.stockable_id')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->select(
                'products.id', // <-- CORREÇÃO: Adicionada esta linha
                'products.name',
                'product_types.name as category',
                'inventory.quantity_on_hand',
                'products.average_cost',
                'products.minimum_stock'
            )->get();

        return response()->json(['data' => $products]);
    }

    /**
     * Fornece os dados de estoque APENAS para itens diversos.
     */
    public function miscItemStockData()
    {
        $miscItems = DB::table('inventory')
            ->where('stockable_type', 'MiscItem')
            ->join('misc_items', 'misc_items.id', '=', 'inventory.stockable_id')
            ->join('misc_categories', 'misc_items.misc_category_id', '=', 'misc_categories.id')
            ->select(
                'misc_items.id', // <-- CORREÇÃO: Adicionada esta linha
                'misc_items.name',
                'misc_categories.name as category',
                'inventory.quantity_on_hand',
                'misc_items.average_cost',
                'misc_items.minimum_stock'
            )->get();

        return response()->json(['data' => $miscItems]);
    }

    /**
     * Atualiza o estoque mínimo de um item (produto ou item diverso).
     */
    public function updateMinimumStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'       => 'required|integer',
            'item_type'     => 'required|string|in:Product,MiscItem',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $table = $request->item_type === 'Product' ? 'products' : 'misc_items';

        DB::table($table)
            ->where('id', $request->item_id)
            ->update(['minimum_stock' => $request->minimum_stock]);

        return response()->json(['success' => 'Estoque mínimo atualizado com sucesso!']);
    }
}
