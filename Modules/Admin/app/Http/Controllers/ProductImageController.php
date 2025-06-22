<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    public function index($productId)
    {
        $product = DB::table('products')->find($productId);
        if (!$product) {
            abort(404);
        }

        $images = DB::table('product_images')
            ->where('product_id', $productId)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('admin::products.images.index', compact('product', 'images'));
    }

    public function store(Request $request, $productId)
    {
        // DD 1: O request chegou com algum arquivo?
        // dd($request->all()); 

        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            // DD 2: Se a validação falhar, quais são os erros?
            dd($validator->errors()->all());

            // return back()->withErrors($validator)->withInput(); // Linha original comentada
        }

        if ($request->hasFile('images')) {
            // DD 3: A condição hasFile('images') é verdadeira?
            // dd('A condição hasFile() é verdadeira. Entrando no loop...');

            foreach ($request->file('images') as $file) {

                // DD 4: O arquivo individual é válido antes de salvar?
                if (!$file->isValid()) {
                    dd('Arquivo inválido encontrado no loop: ' . $file->getErrorMessage());
                }

                $path = $file->store('product_images', 'public');

                DB::table('product_images')->insert([
                    'product_id' => $productId,
                    'image_url' => Storage::url($path),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } else {
            // DD 5: Se hasFile for falso, vamos saber aqui.
            dd('A condição $request->hasFile(\'images\') retornou FALSO.');
        }

        return redirect()->route('admin.products.images.index', $productId)
            ->with('success', 'Imagens adicionadas com sucesso!');
    }

    public function destroy($imageId)
    {
        $image = DB::table('product_images')->find($imageId);
        if ($image) {
            // Converte a URL de volta para o caminho do storage para deletar
            $path = str_replace('/storage', 'public', $image->image_url);
            Storage::delete($path);
            DB::table('product_images')->where('id', $imageId)->delete();
            return response()->json(['success' => 'Imagem removida com sucesso!']);
        }
        return response()->json(['error' => 'Imagem não encontrada.'], 404);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->input('order', []) as $index => $id) {
            DB::table('product_images')->where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => 'Ordem das imagens atualizada!']);
    }
}
