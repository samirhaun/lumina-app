<?php

// Modules/Frontend/Http/Controllers/HomeController.php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function index()
    {
        $productTypes = DB::table('product_types')->orderBy('name')->get();

        $products = DB::table('products')
            ->where('show_in_store', 1) // <-- ADICIONADO AQUI
            ->orderBy('created_at', 'desc')
            ->get();

        $images = DB::table('product_images')
            ->whereIn('product_id', $products->pluck('id'))
            ->orderBy('sort_order', 'asc')
            ->get()
            ->groupBy('product_id');


        $heroes = DB::table('hero_banners')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('frontend::home', [
            'productTypes'      => $productTypes,
            'products'          => $products,
            'imagesByProduct'   => $images,
            'heroes'          => $heroes,
        ]);
    }
}
