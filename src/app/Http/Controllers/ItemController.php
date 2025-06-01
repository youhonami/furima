<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // ログイン済みかつ未認証のユーザーは /email/verify にリダイレクト
        if (Auth::check() && !Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $filter = $request->input('filter', 'recommended');
        $search = $request->input('search');

        if ($filter === 'mylist') {
            if (Auth::check()) {
                $query = Item::whereIn('id', Auth::user()->likedItems->pluck('id'))
                    ->where('user_id', '!=', Auth::id()); // 自分が出品した商品を除外
            } else {
                $query = Item::whereRaw('1 = 0');
            }
        } else {
            $query = Item::query();
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }

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

        $item->categories()->sync($request->input('category_ids'));
    }
}
