<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait; // トレイトを別名で use
use Illuminate\Auth\Notifications\VerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class); // ユーザーは1つのプロフィールを持つ
    }

    protected static function booted()
    {
        static::created(function ($user) {
            // プロフィールを初期化
            $user->profile()->create([
                'postal_code' => '',
                'address' => '',
                'building' => '',
            ]);
        });
    }

    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'item_user_likes')->withTimestamps();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * The notifications that are sent to the user.
     *
     * @return array
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);  // ここで認証メールを送信
    }
}
