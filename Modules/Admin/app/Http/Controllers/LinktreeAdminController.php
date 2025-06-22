<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LinktreeAdminController extends Controller
{
    /**
     * Exibe a página de gerenciamento, buscando as configurações.
     */
    public function index()
    {
        $settings = DB::table('settings')->where('setting_key', 'LIKE', 'linktree_%')
            ->pluck('setting_value', 'setting_key');

        return view('admin::linktree.index', compact('settings'));
    }

    /**
     * Fornece dados para a DataTable.
     */
    public function data()
    {
        $links = DB::table('linktree_links')->orderBy('display_order', 'asc')->get();
        return response()->json(['data' => $links]);
    }

    /**
     * Atualiza as configurações da página (handle e bio).
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'linktree_handle' => 'required|string|max:255',
            'linktree_bio'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('settings')->updateOrInsert(
            ['setting_key' => 'linktree_handle'],
            ['setting_value' => $request->linktree_handle]
        );
        DB::table('settings')->updateOrInsert(
            ['setting_key' => 'linktree_bio'],
            ['setting_value' => $request->linktree_bio]
        );

        return response()->json(['success' => 'Configurações da página atualizadas!']);
    }

    /**
     * Salva um novo link.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'icon_class' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $maxOrder = DB::table('linktree_links')->max('display_order');

        DB::table('linktree_links')->insert([
            'title' => $request->title,
            'url' => $request->url,
            'icon_class' => $request->icon_class,
            'is_active' => $request->has('is_active'),
            'display_order' => $maxOrder + 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['success' => 'Link criado com sucesso!']);
    }

    /**
     * Atualiza um link existente.
     */
    public function update(Request $request, $link)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'icon_class' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        DB::table('linktree_links')->where('id', $link)->update([
            'title' => $request->title,
            'url' => $request->url,
            'icon_class' => $request->icon_class,
            'is_active' => $request->has('is_active'),
            'updated_at' => now()
        ]);
        return response()->json(['success' => 'Link atualizado com sucesso!']);
    }

    /**
     * Remove um link.
     */
    public function destroy($link)
    {
        DB::table('linktree_links')->where('id', $link)->delete();
        return response()->json(['success' => 'Link removido com sucesso!']);
    }

    /**
     * Atualiza a ordem dos links após arrastar e soltar.
     */
    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $id) {
            DB::table('linktree_links')->where('id', $id)->update(['display_order' => $index]);
        }
        return response()->json(['success' => 'Ordem dos links atualizada!']);
    }

    public function socialData()
    {
        $socials = DB::table('linktree_socials')->orderBy('display_order')->get();
        return response()->json(['data' => $socials]);
    }

    public function storeSocial(Request $request)
    {
        $v = Validator::make($request->all(), [
            'url' => 'required|url|max:2048',
            'icon_class' => 'required|string|max:100',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $max = DB::table('linktree_socials')->max('display_order');
        DB::table('linktree_socials')->insert([
            'url' => $request->url,
            'icon_class' => $request->icon_class,
            'is_active' => $request->has('is_active'),
            'display_order' => $max + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => 'Rede social criada!']);
    }

    public function updateSocial(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'url' => 'required|url|max:2048',
            'icon_class' => 'required|string|max:100',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        DB::table('linktree_socials')->where('id', $id)->update([
            'url' => $request->url,
            'icon_class' => $request->icon_class,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => 'Rede social atualizada!']);
    }

    public function destroySocial($id)
    {
        DB::table('linktree_socials')->where('id', $id)->delete();
        return response()->json(['success' => 'Rede social removida!']);
    }

    public function updateSocialOrder(Request $request)
    {
        foreach ($request->order as $idx => $id) {
            DB::table('linktree_socials')->where('id', $id)->update(['display_order' => $idx]);
        }
        return response()->json(['success' => 'Ordem atualizada!']);
    }
}
