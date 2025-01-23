<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>furima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    </head>

    @yield('css')
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="/">
                <img src="{{ asset('storage/images/logo.svg') }}" alt="COACHTECH Logo">
            </a>
        </div>

        <div class="search-bar">
            <input type="text" placeholder="なにをお探しですか？">
        </div>

        <nav class="nav-links">
            @auth
            <!-- ログインしている場合 -->
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
            <a href="{{ route('mypage') }}">マイページ</a>
            <a href="{{ route('sell.index') }}">出品</a> <!-- 出品リンク -->
            @else
            <!-- ログインしていない場合 -->
            <a href="/login">ログイン</a>
            <a href="#" style="pointer-events: none; color: gray;">マイページ</a> <!-- リンクを無効にする -->
            <a href="#" style="pointer-events: none; color: gray;">出品</a> <!-- 出品リンクを無効にする -->
            @endauth
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>