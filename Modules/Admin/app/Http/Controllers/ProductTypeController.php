<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductTypeController extends Controller
{
    public function index()
    {
        // Redireciona para a página principal de produtos, que agora contém tudo
        return redirect()->route('admin.products.index');
    }

    // NOVO MÉTODO PARA DATATABLES
    public function data()
    {
        $productTypes = DB::table('product_types')->orderBy('name')->get();
        return response()->json(['data' => $productTypes]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_types,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('product_types')->insert(['name' => $request->name]);
        return response()->json(['success' => 'Tipo de produto criado com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_types,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('product_types')->where('id', $id)->update(['name' => $request->name]);
        return response()->json(['success' => 'Tipo de produto atualizado com sucesso!']);
    }

    public function destroy($id)
    {
        // Opcional: Adicionar verificação se o tipo está em uso antes de deletar
        $isInUse = DB::table('products')->where('product_type_id', $id)->exists();
        if ($isInUse) {
            return response()->json(['error' => 'Este tipo não pode ser excluído pois está em uso por produtos.'], 409); // 409 Conflict
        }

        DB::table('product_types')->where('id', $id)->delete();
        return response()->json(['success' => 'Tipo de produto removido com sucesso!']);
    }
    
    public function list()
    {
        $productTypes = DB::table('product_types')->orderBy('name')->get(['id', 'name']);
        return response()->json($productTypes);
    }
}
