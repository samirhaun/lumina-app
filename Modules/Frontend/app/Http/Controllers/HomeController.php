<?php

// Modules/Frontend/Http/Controllers/HomeController.php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function index()
    {
        // Produtos mais recentes
        $products = DB::table('products')
            ->orderBy('created_at', 'desc')
            ->get();

        // Carrega as imagens (1ª imagem) de cada produto
        $images = DB::table('product_images')
            ->whereIn('product_id', $products->pluck('id'))
            ->orderBy('sort_order', 'asc')
            ->get()
            ->groupBy('product_id');

        return view('frontend::home', [
            'products' => $products,
            'imagesByProduct' => $images,
        ]);
    }
}
