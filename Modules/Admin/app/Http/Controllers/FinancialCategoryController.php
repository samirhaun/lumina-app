<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FinancialCategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('financial_categories')->orderBy('type')->orderBy('name')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        // 1. Definimos as regras de validação
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('financial_categories')],
            'type' => ['required', 'in:credit,debit'],
        ];

        // 2. Definimos as mensagens em português para cada regra
        $messages = [
            'name.required' => 'O campo "Nome da Categoria" é obrigatório.',
            'name.unique'   => 'Já existe uma categoria com este nome.',
            'type.required' => 'O campo "Tipo" é obrigatório.',
        ];
        
        // 3. Criamos o validador com as regras E as mensagens customizadas
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = DB::table('financial_categories')->insertGetId($request->only(['name', 'type']));
        $newCategory = DB::table('financial_categories')->find($id);

        return response()->json([
            'success' => 'Categoria adicionada com sucesso!',
            'category' => $newCategory
        ]);
    }

    public function destroy($id)
    {
        $isInUse = DB::table('financial_transactions')->where('category_id', $id)->exists();
        if ($isInUse) {
            return response()->json(['error' => 'Não é possível excluir. A categoria já está sendo usada em um ou mais lançamentos.'], 409);
        }
        
        DB::table('financial_categories')->where('id', $id)->delete();
        return response()->json(['success' => 'Categoria removida com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        // Lógica de atualização aqui, também usaria o array $messages
    }
}