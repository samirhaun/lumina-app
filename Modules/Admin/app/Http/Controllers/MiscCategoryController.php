<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MiscCategoryController extends Controller
{
    public function index()
    {
        // A view será unificada, então podemos redirecionar ou criar uma view dedicada
        // Por simplicidade, vamos assumir que o acesso será por uma view unificada 'misc.index'
        return view('admin::misc.index');
    }

    public function data()
    {
        $miscCategories = DB::table('misc_categories')->orderBy('name')->get();
        return response()->json(['data' => $miscCategories]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|string|max:255|unique:misc_categories,name']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::table('misc_categories')->insert(['name' => $request->name]);
        return response()->json(['success' => 'Categoria criada com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|string|max:255|unique:misc_categories,name,' . $id]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::table('misc_categories')->where('id', $id)->update(['name' => $request->name]);
        return response()->json(['success' => 'Categoria atualizada com sucesso!']);
    }

    public function destroy($id)
    {
        if (DB::table('misc_items')->where('misc_category_id', $id)->exists()) {
            return response()->json(['error' => 'Esta categoria não pode ser excluída pois está em uso.'], 409);
        }
        DB::table('misc_categories')->where('id', $id)->delete();
        return response()->json(['success' => 'Categoria removida com sucesso!']);
    }
    public function list()
    {
        $miscCategories = DB::table('misc_categories')->orderBy('name')->get(['id', 'name']);
        return response()->json($miscCategories);
    }
}
