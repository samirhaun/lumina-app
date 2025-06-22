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

    public function data()
    {
        $clients = DB::table('clients')->orderBy('name')->get();
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
}