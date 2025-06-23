<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// ==========================================================
// DEPENDÊNCIAS QUE ESTAVAM FALTANDO
// ==========================================================
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Cart;

class CheckoutController extends Controller
{
    /**
     * Mostra a página de checkout com o resumo do carrinho.
     */
    public function index()
    {
        if (Cart::count() == 0) {
            return redirect()->route('frontend.home');
        }

        $cartItems = Cart::content();

        // Busca as imagens para os itens do carrinho
        $productIds = $cartItems->pluck('id');

        $productImages = DB::table('product_images')
            ->whereIn('product_id', $productIds)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('product_id'); // <-- groupBy agrupa todos os itens

        $subtotal = Cart::subtotal(2, ',', '.');
        $total = Cart::total(2, ',', '.');

        // Pega o código de cada produto no carrinho
        $codesByProduct = DB::table('products')
            ->whereIn('id', $productIds)
            ->pluck('code', 'id'); // retorna [ id => code ]

        // Adiciona productImages aos dados enviados para a view
        return view('frontend::checkout.index', compact('cartItems', 'subtotal', 'total', 'productImages', 'codesByProduct'));
    }


    /**
     * Valida os dados do pedido e processa o checkout.
     * Processa e salva o pedido, e prepara o redirecionamento para o WhatsApp.
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => 'required|string|max:20',
            'delivery_method' => 'required|in:delivery,pickup',
            'cep'          => 'nullable|required_if:delivery_method,delivery|string',
            'street'       => 'nullable|required_if:delivery_method,delivery|string',
            'number'       => 'nullable|required_if:delivery_method,delivery|string',
            'complement'   => 'nullable|required_if:delivery_method,delivery|string',
            'neighborhood' => 'nullable|required_if:delivery_method,delivery|string',
            'city'         => 'nullable|required_if:delivery_method,delivery|string',
            'state'        => 'nullable|required_if:delivery_method,delivery|string|max:2',
            'payment_method'  => 'required|string|max:50',
            'installments'    => 'required_if:payment_method,Cartão de Crédito|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $address = null;
            if ($request->delivery_method === 'delivery') {
                $address = [
                    'cep'         => $request->input('cep'),
                    'street'      => $request->input('street'),
                    'number'      => $request->input('number'),
                    'complement'  => $request->input('complement'),    // <<— aqui!
                    'neighborhood' => $request->input('neighborhood'),
                    'city'        => $request->input('city'),
                    'state'       => $request->input('state'),
                ];
            }

            $orderId = DB::table('store_orders')->insertGetId([
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'total_amount'     => Cart::total(2, '.', ''),
                'delivery_method'  => $request->delivery_method,
                'delivery_address' => $address ? json_encode($address, JSON_UNESCAPED_UNICODE) : null,
                'payment_method'   => $request->payment_method,
                'installments'     => $request->installments,
                'status'           => 'Pendente',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Pegue o conteúdo e o total antes de limpar o carrinho
            $items = Cart::content();
            $cartTotalForMessage = Cart::total(2, ',', '.');

            // Busca código de cada produto
            $productIds = $items->pluck('id')->toArray();
            $codesById = DB::table('products')
                ->whereIn('id', $productIds)
                ->pluck('code', 'id')
                ->toArray();

            // Agora você pode destruir o carrinho
            Cart::destroy();

            foreach ($items as $item) {
                DB::table('store_order_items')->insert([
                    'order_id'   => $orderId,
                    'product_id' => $item->id,
                    'quantity'   => $item->qty,
                    'price'      => $item->price,
                ]);
            }

            DB::commit();

            Cart::destroy();

            $whatsappNumber = '5538988519293';

            // Após salvar o pedido e limpar o carrinho, monte a mensagem assim:

            $message  = "👋 Olá, *Lúmina*!\n\n";
            $message .= "🛒 *Resumo do Pedido Nº {$orderId}*\n\n";

            // Itens
            $message .= "📋 *Itens do Pedido:*\n";
            foreach ($items as $item) {
                $name     = $item->name;
                $qty      = $item->qty;
                $subtotal = number_format($item->subtotal, 2, ',', '.');
                $code     = $codesById[$item->id] ?? '—';
                $message .= "• *{$name}* (Cód.: {$code})\n";
                $message .= "  Qtd: {$qty}\n";
                $message .= "  Subtotal: R$ {$subtotal}\n\n";
            }

            // Total
            $message .= "💰 *Total a Pagar:* R$ {$cartTotalForMessage}\n\n";

            // Dados do Cliente
            $message .= "👤 *Meus Dados:*\n";
            $message .= "• Nome: *{$request->customer_name}*\n";
            $message .= "• WhatsApp: *{$request->customer_phone}*\n\n";

            // Retirada ou Entrega
            if ($request->delivery_method === 'pickup') {
                $message .= "🏬 *Retirada no Local:*\n";
                $message .= "Avenida Mestra Fininha, 3890, Ap 202\n";
                $message .= "Bairro Augusta Mota, Montes Claros - MG\n\n";
            } else {
                $addr = $address;
                $message .= "🚚 *Entrega em Casa:*\n";
                $message .= "CEP: *{$addr['cep']}*\n";
                $message .= "Rua: *{$addr['street']}, {$addr['number']}*";
                if (!empty($addr['complement'])) {
                    $message .= " (_{$addr['complement']}_)";
                }
                $message .= "\n";
                $message .= "Bairro: *{$addr['neighborhood']}*\n";
                $message .= "Cidade/UF: *{$addr['city']} - {$addr['state']}*\n\n";
            }

            // Pagamento
            $message .= "💳 *Pagamento:*\n";
            $message .= "• Método: *{$request->payment_method}*\n";
            if ($request->payment_method === 'Cartão de Crédito' && $request->installments) {
                $message .= "• Parcelas: *{$request->installments}*\n";
            }
            $message .= "\n";

            // Fechamento
            $message .= "_Fico no aguardo da confirmação e maiores detalhes. Muito obrigado!_";

            // Monta a URL
            $whatsappUrl = 'https://wa.me/' . $whatsappNumber . '?text=' . rawurlencode($message);

            return response()->json([
                'success'      => true,
                'whatsapp_url' => $whatsappUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ocorreu um erro ao finalizar o pedido: ' . $e->getMessage()], 500);
        }
    }
}
