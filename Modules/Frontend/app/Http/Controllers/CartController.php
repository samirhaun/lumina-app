<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Cart;

class CartController extends Controller
{
    /**
     * Exibe a página do carrinho de compras.
     */
    public function index()
    {
        $cartItems = Cart::content();
        $subtotal = Cart::subtotal(2, ',', '.'); // Formata para o padrão brasileiro

        return view('frontend::cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Adiciona um item ao carrinho.
     */
    /**
     * Adiciona um item ao carrinho.
     */
    public function add(Request $request)
    {
        // 1. Validação: Adicionamos a validação para a quantidade
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1' // Garante que a quantidade é um número válido >= 1
        ]);

        $product = DB::table('products')->find($request->product_id);

        Cart::add([
            'id'      => $product->id,
            'name'    => $product->name,
            'qty'     => $request->quantity, // 2. Usamos a quantidade que veio do formulário
            'price'   => $product->sale_price,
            'weight'  => 0,
            'options' => ['code' => $product->code ?? '']
        ]);

        return response()->json([
            'success'   => "Produto adicionado ao carrinho!",
            'cartCount' => Cart::count()
        ]);
    }
    /**
     * Atualiza a quantidade de um item no carrinho.
     */
    public function update(Request $request, $rowId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        Cart::update($rowId, $request->quantity);

        return response()->json([
            'success' => 'Quantidade atualizada!',
            'cartCount' => Cart::count(),
            'subtotal' => Cart::subtotal(2, ',', '.'),
            'itemSubtotal' => Cart::get($rowId)->subtotal(2, ',', '.')
        ]);
    }

    /**
     * Remove um item do carrinho.
     */
    public function remove($rowId)
    {
        Cart::remove($rowId);

        return response()->json([
            'success' => 'Item removido do carrinho.',
            'cartCount' => Cart::count(),
            'subtotal' => Cart::subtotal(2, ',', '.')
        ]);
    }
}
