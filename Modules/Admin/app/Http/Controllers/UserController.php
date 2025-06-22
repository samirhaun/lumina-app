<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        // Precisamos buscar o username também para exibir na edição
        $users = DB::table('users')
            ->select('id', 'name', 'email', 'username', 'created_at') // Adicionado username
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin::users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // ADICIONADA VALIDAÇÃO PARA USERNAME
            'username' => 'required|string|max:255|unique:users,username',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('users')->insert([
            // ADICIONADO CAMPO USERNAME NO INSERT
            'username'   => $request->username,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => 'Usuário criado com sucesso!']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // ADICIONADA VALIDAÇÃO PARA USERNAME (ignora o usuário atual)
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $update = [
             // ADICIONADO CAMPO USERNAME NO UPDATE
            'username'   => $request->username,
            'name'       => $request->name,
            'email'      => $request->email,
            'updated_at' => now(),
        ];

        if (!empty($request->password)) {
            $update['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($update);

        return response()->json(['success' => 'Usuário atualizado com sucesso!']);
    }

    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return response()->json(['success' => 'Usuário removido com sucesso!']);
    }
}