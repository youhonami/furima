<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'img',
        'postal_code',
        'address',
        'building'
    ];

    // リレーションを定義（ユーザーとの関連）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
