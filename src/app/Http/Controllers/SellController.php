<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;

class SellController extends Controller
{
    public function index()
    {
        // カテゴリーと商品の状態を取得
        $categories = \App\Models\Category::all();
        $conditions = \App\Models\Condition::all();

        return view('sell', compact('categories', 'conditions'));
    }

    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'condition' => 'required|exists:conditions,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // 画像保存
        $imagePath = null;
        // 画像のアップロード処理
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/items-img');
            $imagePath = str_replace('public/', '', $imagePath); // パスの調整
        }

        // データ保存
        $item = Item::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'img' => $imagePath,
            'condition_id' => $validated['condition'],
            'user_id' => auth()->id(), // ログイン中のユーザーID
        ]);

        // カテゴリーと紐付け
        $item->categories()->sync($validated['categories']);

        return redirect('/')->with('success', '商品を出品しました。');
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('sell', compact('categories', 'conditions'));
    }
}
