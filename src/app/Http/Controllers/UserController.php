<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // クエリパラメータでタブを切り替える
        $tab = request('tab', 'listed');
        $listedItems = collect();
        $purchasedItems = collect();

        if ($tab === 'listed') {
            $listedItems = Item::where('user_id', $user->id)->get();
        } elseif ($tab === 'purchased') {
            $purchasedItems = Purchase::where('user_id', $user->id)->with('item')->get();
        }

        return view('mypage', compact('user', 'tab', 'listedItems', 'purchasedItems'));
    }
}
