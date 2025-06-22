<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
        return view('admin::suppliers.index');
    }

    public function data()
    {
        $suppliers = DB::table('suppliers')->orderBy('name')->get();
        return response()->json(['data' => $suppliers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:suppliers,name',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('suppliers')->insert($request->only(['name', 'contact_person', 'phone', 'email']));
        return response()->json(['success' => 'Fornecedor criado com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:suppliers,name,' . $id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('suppliers')->where('id', $id)->update($request->only(['name', 'contact_person', 'phone', 'email']));
        return response()->json(['success' => 'Fornecedor atualizado com sucesso!']);
    }

    public function destroy($id)
    {
        // Opcional: impedir exclusão se o fornecedor tiver compras associadas
        if (DB::table('purchases')->where('supplier_id', $id)->exists()) {
             return response()->json(['error' => 'Este fornecedor não pode ser excluído pois possui compras associadas.'], 409);
        }
        DB::table('suppliers')->where('id', $id)->delete();
        return response()->json(['success' => 'Fornecedor removido com sucesso!']);
    }
}