<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FinancialTransactionController extends Controller
{
    public function index()
    {
        // Busca as categorias para popular o dropdown do modal
        $categories = DB::table('financial_categories')->orderBy('name')->get();
        return view('admin::financial-transactions.index', compact('categories'));
    }

    public function data()
    {
        $transactions = DB::table('financial_transactions')
            ->leftJoin('financial_categories', 'financial_transactions.category_id', '=', 'financial_categories.id')
            ->select(
                'financial_transactions.*', // Seleciona tudo da tabela principal
                'financial_categories.name as category_name'
            )
            ->orderBy('transaction_date', 'desc')
            ->get();

        return response()->json(['data' => $transactions]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'description'      => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0.01',
            'type'             => 'required|in:credit,debit',
            'category_id'      => 'required|exists:financial_categories,id',
            'notes'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('financial_transactions')->insert($request->only([
            'transaction_date',
            'description',
            'amount',
            'type',
            'category_id',
            'notes'
        ]));

        return response()->json(['success' => 'Lançamento registrado com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        // A lógica de update seria muito similar à de store, se necessário.
    }

    public function destroy($id)
    {
        DB::table('financial_transactions')->where('id', $id)->delete();
        return response()->json(['success' => 'Lançamento removido com sucesso!']);
    }
}
