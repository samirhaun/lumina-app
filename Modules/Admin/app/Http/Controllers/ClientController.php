<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index()
    {
        return view('admin::clients.index');
    }

    /**
     * Fornece dados para a DataTable, AGORA COM TOTAIS DE VENDAS.
     * ESTA É A VERSÃO CORRETA QUE DEVE ESTAR NO SEU ARQUIVO.
     */
    public function data()
    {
        $clients = DB::table('clients')
            ->leftJoin('orders', 'clients.id', '=', 'orders.client_id')
            ->select(
                'clients.id',
                'clients.name',
                'clients.notes',
                // A função COUNT() do SQL cria o campo 'sales_count'
                DB::raw('COUNT(orders.id) as sales_count'),
                // A função SUM() do SQL cria o campo 'total_spent'
                DB::raw('IFNULL(SUM(orders.grand_total), 0) as total_spent')
            )
            ->groupBy('clients.id', 'clients.name', 'clients.notes')
            ->orderBy('clients.name')
            ->get();

        return response()->json(['data' => $clients]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('clients')->insert($request->only(['name', 'notes']));
        return response()->json(['success' => 'Cliente criado com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('clients')->where('id', $id)->update($request->only(['name', 'notes']));
        return response()->json(['success' => 'Cliente atualizado com sucesso!']);
    }

    public function destroy($id)
    {
        // Opcional: Adicionar verificação se o cliente tem vendas associadas antes de deletar.
        DB::table('clients')->where('id', $id)->delete();
        return response()->json(['success' => 'Cliente removido com sucesso!']);
    }

    public function getSalesHistory($id)
    {
        $client = DB::table('clients')->where('id', $id)->first(['id', 'name']);

        if (!$client) {
            return response()->json(['error' => 'Cliente não encontrado.'], 404);
        }

        $sales = DB::table('orders')
            ->where('client_id', $id)
            ->select('id', 'order_date', 'status', 'grand_total')
            ->orderBy('order_date', 'desc')
            ->get();

        // Loop para "enriquecer" cada venda com seus itens
        foreach ($sales as $sale) {
            $sale->items = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('order_id', $sale->id)
                ->select(
                    'products.name as product_name',
                    'order_items.quantity',
                    'order_items.price_per_unit'
                )
                ->get();
        }

        return response()->json([
            'client' => $client,
            'sales' => $sales,
        ]);
    }
}
