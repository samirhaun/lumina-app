<?php

// Modules/Frontend/Http/Controllers/ProductController.php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Carrega todos os tipos (para o filtro de abas)
        $productTypes = DB::table('product_types')->orderBy('name')->get();

        // Se veio um filtro por tipo, aplica; senão traz todos
        $typeId = $request->input('type');
        $productsQuery = DB::table('products')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->select(
                'products.id',
                'products.name',
                'products.sale_price',
                'products.product_type_id',
                'product_types.name as type_name'
            )
            ->orderBy('products.created_at', 'desc');

        if ($typeId) {
            $productsQuery->where('products.product_type_id', $typeId);
        }

        $products = $productsQuery->get();

        // Pego a 1ª imagem de cada produto
        $imagesRaw = DB::table('product_images')
            ->whereIn('product_id', $products->pluck('id'))
            ->orderBy('sort_order')
            ->get()
            ->groupBy('product_id');


        return view('frontend::products.index', [
            'productTypes'     => $productTypes,
            'products'         => $products,
            'imagesByProduct'  => $imagesRaw,
            'currentTypeId'    => $typeId,

        ]);
    }

    public function show($id)
    {
        // Produto + tipo
        $product = DB::table('products')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->select('products.*', 'product_types.name as type_name')
            ->where('products.id', $id)
            ->first();
        if (!$product) abort(404);

        // Todas as imagens
        $productImages = DB::table('product_images')
            ->where('product_id', $id)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Relacionados (mesmo tipo)
        $relatedProducts = DB::table('products')
            ->where('product_type_id', $product->product_type_id)
            ->where('id', '!=', $id)
            ->limit(4)
            ->get();

        return view('frontend::products.show', compact(
            'product',
            'productImages',
            'relatedProducts'
        ));
    }
}
