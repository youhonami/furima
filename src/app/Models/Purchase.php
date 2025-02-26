<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'postal_code',
        'address',
        'building',
    ];

    // 購入商品とユーザーのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 購入商品とアイテムのリレーション
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
