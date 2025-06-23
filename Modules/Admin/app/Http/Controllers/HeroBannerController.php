<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HeroBannerController extends Controller
{
    public function index()
    {
        return view('admin::hero-banners.index');
    }

    public function data()
    {
        $banners = DB::table('hero_banners')->select('id', 'image_path', 'link_url', 'is_active', 'sort_order')->orderBy('sort_order', 'asc')->get();

        // Adiciona a URL pública para cada banner
        foreach ($banners as $banner) {
            $banner->image_url = Storage::url($banner->image_path);
        }

        return response()->json(['data' => $banners]);
    }
    public function create()
    {
        $productTypes = DB::table('product_types')->orderBy('name')->get();
        return view('admin::hero-banners.create', compact('productTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = $request->file('image')->store('hero-banners', 'public');

        DB::table('hero_banners')->insert([
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.hero-banners.index')->with('success', 'Banner criado com sucesso!');
    }

    public function edit($id)
    {
        $banner = DB::table('hero_banners')->where('id', $id)->first();
        if (!$banner) {
            abort(404);
        }

        $productTypes = DB::table('product_types')->orderBy('name')->get();
        return view('admin::hero-banners.edit', compact('banner', 'productTypes'));
    }

    public function update(Request $request, $id)
    {
        $banner = DB::table('hero_banners')->where('id', $id)->first();
        if (!$banner) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'link_url' => $request->link_url,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            Storage::delete($banner->image_path);
            $data['image_path'] = $request->file('image')->store('hero-banners', 'public');
        }

        DB::table('hero_banners')->where('id', $id)->update($data);

        return redirect()->route('admin.hero-banners.index')->with('success', 'Banner atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $banner = DB::table('hero_banners')->where('id', $id)->first();
        if ($banner) {
            Storage::delete($banner->image_path);
            DB::table('hero_banners')->where('id', $id)->delete();
            return response()->json(['success' => 'Banner removido com sucesso!']);
        }
        return response()->json(['error' => 'Banner não encontrado.'], 404);
    }
}
