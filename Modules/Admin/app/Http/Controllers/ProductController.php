<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Importe a classe Rule
use Illuminate\Database\QueryException; // << import

class ProductController extends Controller
{
    public function index()
    {
        $productTypes = DB::table('product_types')->orderBy('name')->get();
        return view('admin::products.index', compact('productTypes'));
    }

    public function data()
    {
        $products = DB::table('products')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->select('products.id', 'products.name', 'products.code', 'products.product_type_id', 'product_types.name as type_name') // Adicionado 'products.code'
            ->orderBy('products.created_at', 'desc')
            ->get();

        return response()->json(['data' => $products]);
    }

    /**
     * Armazena um novo produto
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:100|unique:products,code',
            'product_type_id' => 'required|integer|exists:product_types,id',
        ], [
            // mensagem customizada para SKU único
            'code.unique' => 'Este código (SKU) já está em uso.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('products')->insert([
            'name'            => $request->name,
            'code'            => $request->code,
            'product_type_id' => $request->product_type_id,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return response()->json([
            'success' => 'Produto criado com sucesso!'
        ]);
    }

    /**
     * Atualiza um produto existente
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'code'            => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'code')->ignore($id)
            ],
            'product_type_id' => 'required|integer|exists:product_types,id',
        ], [
            // mesma mensagem customizada para o update
            'code.unique' => 'Este código (SKU) já está em uso.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('products')
            ->where('id', $id)
            ->update([
                'name'            => $request->name,
                'code'            => $request->code,
                'product_type_id' => $request->product_type_id,
                'updated_at'      => now(),
            ]);

        return response()->json([
            'success' => 'Produto atualizado com sucesso!'
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::table('products')->where('id', $id)->delete();

            return response()->json([
                'success' => 'Produto removido com sucesso!'
            ]);
        } catch (QueryException $e) {
            // 23000 é código genérico de integridade; 1451 = tem child rows
            if ($e->getCode() == '23000' && str_contains($e->getMessage(), '1451')) {
                return response()->json([
                    'error' => 'Não foi possível excluir este produto pois existem itens de pedido associados a ele.'
                ], 409);
            }
            // para outras QueryExceptions, relança
            throw $e;
        }
    }
}
