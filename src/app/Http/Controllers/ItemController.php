<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'recommended');
        $search = $request->input('search');

        if ($filter === 'mylist') {
            if (Auth::check()) {
                // ユーザーが「いいね」した商品のみ取得
                $query = Item::whereIn('id', Auth::user()->likedItems->pluck('id'));
            } else {
                $query = Item::whereRaw('1 = 0'); // ログインしていない場合は空
            }
        } else {
            // ログインユーザーが出品した商品を除外
            $query = Item::query();
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }

        // 検索処理（商品名のみを対象）
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->get();

        return view('index', compact('items', 'search', 'filter'));
    }


    // 商品詳細ページ
    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('item', compact('item'));
    }

    public function store(Request $request)
    {
        $item = Item::create($request->only(['name', 'price', 'description', 'img', 'condition_id']));

        // 選択されたカテゴリを関連付ける
        $item->categories()->sync($request->input('category_ids'));
    }
}
