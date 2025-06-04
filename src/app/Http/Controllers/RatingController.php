<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ratee_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'role' => 'required|in:seller,buyer',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Rating::create([
            'rater_id' => auth()->id(),
            'ratee_id' => $request->ratee_id,
            'item_id' => $request->item_id,
            'role' => $request->role,
            'rating' => $request->rating,
        ]);

        return redirect()->back()->with('success', '評価を送信しました。');
    }
}
