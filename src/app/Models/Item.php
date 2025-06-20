<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'description', 'img', 'condition_id', 'category_id', 'user_id'];

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'item_user_likes')->withTimestamps();
    }

    public function isLikedBy($user)
    {
        return $this->likes->contains($user);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isSold()
    {

        return \App\Models\Purchase::where('item_id', $this->id)->exists();
    }

    // app/Models/Item.php
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }


    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
