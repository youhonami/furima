<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * CSRF検証を除外するルート
     *
     * @var array<int, string>
     */
    protected $except = [
        '/register', // 追加（新規会員登録のエンドポイント）
    ];
}
