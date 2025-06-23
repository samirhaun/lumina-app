<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PricingController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->whereIn('setting_key', ['global_default_misc_costs', 'global_profit_margin'])->pluck('setting_value', 'setting_key');

        // Buscando os tipos de produto para as abas
        $productTypes = DB::table('product_types')->orderBy('name')->get();

        return view('admin::pricing.index', compact('settings', 'productTypes'));
    }

    public function data()
    {
        // Adicionamos o JOIN para buscar o nome do tipo de produto
        $products = DB::table('products')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->select(
                'products.id',
                'products.name',
                'products.average_cost',
                'products.suggested_price',
                'products.sale_price',
                'product_types.name as type_name' // Nova coluna para o filtro
            )
            ->orderBy('products.name')->get();

        return response()->json(['data' => $products]);
    }

    /**
     * Normaliza strings monetárias em pt-BR ou en-US para float
     */
    private function cleanNumber(string $value): float
    {
        $value = trim($value);
        // Se tiver vírgula, assumimos pt-BR: 1.234,56
        if (strpos($value, ',') !== false) {
            // remove pontos de milhar e transforma vírgula em ponto
            $value = str_replace(['.'], [''], $value);
            $value = str_replace(',', '.', $value);
        }
        // Senão, assumimos que já veio com ponto decimal: “45.01”
        // Removemos tudo que não seja dígito ou ponto
        $value = preg_replace('/[^\d\.]/', '', $value);

        return (float) $value;
    }
    public function updateSettings(Request $request)
    {
        // limpa e valida exatamente como antes...
        $data = $request->all();
        $data['global_default_misc_costs'] = $this->cleanNumber($data['global_default_misc_costs'] ?? '0');
        $data['global_profit_margin']      = $this->cleanNumber($data['global_profit_margin'] ?? '0');

        $validator = Validator::make($data, [
            'global_default_misc_costs' => 'required|numeric|min:0',
            'global_profit_margin'      => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // aqui usamos updateOrInsert em vez de update
        DB::table('settings')
            ->updateOrInsert(
                ['setting_key'   => 'global_default_misc_costs'],
                ['setting_value' => $data['global_default_misc_costs'], 'updated_at' => now()]
            );

        DB::table('settings')
            ->updateOrInsert(
                ['setting_key'   => 'global_profit_margin'],
                ['setting_value' => $data['global_profit_margin'],      'updated_at' => now()]
            );

        return response()->json(['success' => 'Configurações globais salvas com sucesso!']);
    }

    public function updateSalePrice(Request $request, $id)
    {
        $data = $request->all();
        $data['sale_price'] = $this->cleanNumber($data['sale_price'] ?? '0');

        $validator = Validator::make($data, [
            'sale_price' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        // recalcula suggested_price
        $settings = DB::table('settings')
            ->whereIn('setting_key', ['global_default_misc_costs', 'global_profit_margin'])
            ->pluck('setting_value', 'setting_key');
        $totalCost = $product->average_cost + ($settings['global_default_misc_costs'] ?? 0);
        $suggestedPrice = $totalCost * (1 + (($settings['global_profit_margin'] ?? 0) / 100));

        DB::table('products')->where('id', $id)->update([
            'sale_price'      => $data['sale_price'],
            'suggested_price' => $suggestedPrice,
            'updated_at'      => now(),
        ]);

        return response()->json(['success' => "Preço do produto '{$product->name}' atualizado!"]);
    }
}
