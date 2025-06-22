<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LinktreeController extends Controller
{
    public function show()
    {
        $settings = DB::table('settings')
            ->whereIn('setting_key', ['linktree_handle', 'linktree_bio'])
            ->pluck('setting_value', 'setting_key');

        $links = DB::table('linktree_links')
            ->where('is_active', true)
            ->orderBy('display_order', 'asc')   // <— aqui, certifica-se de ordenar
            ->get();

        $socials = DB::table('linktree_socials')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
            
return view('frontend::linktree.show', compact('settings','links','socials'));
    }
}
