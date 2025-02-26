<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ExhibitionRequest;

class SellController extends Controller
{
    public function index()
    {
        // カテゴリーと商品の状態を取得
        $categories = \App\Models\Category::all();
        $conditions = \App\Models\Condition::all();

        return view('sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        // バリデーション済みデータを取得
        $validated = $request->validated();

        // 画像保存
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/items-img');
            $imagePath = str_replace('public/', '', $imagePath);
        }

        // データ保存
        $item = Item::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'img' => $imagePath,
            'condition_id' => $validated['condition'],
            'user_id' => auth()->id(),
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
