<?php
namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin::auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validar 'username' em vez de 'email'
        $credentials = $request->validate([
            'username' => 'required|string', // <-- ALTERADO
            'password' => 'required|string',
        ]);

        // 2. Buscar o usuário pelo 'username' no banco de dados
        // ATENÇÃO: Verifique se 'username' é o nome correto da coluna na sua tabela 'users'!
        $user = DB::table('users')
            ->where('username', $credentials['username']) // <-- ALTERADO
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::guard('admin')->loginUsingId($user->id, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // 3. Retornar o erro associado ao campo 'username'
        return back()
            ->withErrors(['username' => 'Credenciais inválidas. Verifique seu nome de usuário e senha.']) // <-- ALTERADO
            ->withInput(); // withInput() mantém o 'username' que o usuário digitou
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}