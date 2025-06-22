<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Listagem de produtos (opcional, você já tem em HomeController).
     */
    public function index()
    {
        $products = DB::table('products')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend::products.index', compact('products'));
    }

    /**
     * Exibe a página de detalhes de um produto.
     */
    public function show($id)
    {
        // Pega os dados principais do produto
        $product = DB::table('products')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->select('products.*', 'product_types.name as type_name')
            ->where('products.id', $id)
            ->first();

        if (!$product) {
            abort(404);
        }

        // Pega todas as imagens da galeria para este produto
        $productImages = DB::table('product_images')
            ->where('product_id', $id)
            ->orderBy('sort_order')
            ->get();

        // Pega produtos relacionados (lógica anterior)
        $relatedProducts = DB::table('products')
            ->where('product_type_id', $product->product_type_id)
            ->where('products.id', '!=', $id)
            ->limit(4)
            ->get();

        // Envia tudo para a view
        return view('frontend::products.show', compact('product', 'productImages', 'relatedProducts'));
    }
}
