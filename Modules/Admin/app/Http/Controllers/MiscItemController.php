<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MiscItemController extends Controller
{
    public function index()
    {
        $miscCategories = DB::table('misc_categories')->orderBy('name')->get();
        return view('admin::misc.index', compact('miscCategories'));
    }

    public function data()
    {
        $miscItems = DB::table('misc_items')
            ->join('misc_categories', 'misc_items.misc_category_id', '=', 'misc_categories.id')
            ->select('misc_items.id', 'misc_items.name', 'misc_items.misc_category_id', 'misc_categories.name as category_name')
            ->orderBy('misc_items.created_at', 'desc')
            ->get();
        return response()->json(['data' => $miscItems]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'misc_category_id' => 'required|integer|exists:misc_categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::table('misc_items')->insert([
            'name'             => $request->name,
            'misc_category_id' => $request->misc_category_id,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        return response()->json(['success' => 'Item criado com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'misc_category_id' => 'required|integer|exists:misc_categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::table('misc_items')->where('id', $id)->update([
            'name'             => $request->name,
            'misc_category_id' => $request->misc_category_id,
            'updated_at'       => now(),
        ]);
        return response()->json(['success' => 'Item atualizado com sucesso!']);
    }

    public function destroy($id)
    {
        DB::table('misc_items')->where('id', $id)->delete();
        return response()->json(['success' => 'Item removido com sucesso!']);
    }
}