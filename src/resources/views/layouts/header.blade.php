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
            <form method="GET" action="{{ route('item.index') }}" class="search-form">
                <input type="hidden" name="filter" value="{{ request('filter', 'recommended') }}">
                <input type="text" name="search" placeholder="商品名を検索" value="{{ request('search') }}" class="search-input">
                <button type="submit" class="search-button">検索</button>
            </form>

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
            <a href="{{ route('login') }}">ログイン</a>
            <a href="{{ route('login') }}">マイページ</a> <!-- ログインページに遷移 -->
            <a href="{{ route('login') }}">出品</a> <!-- ログインページに遷移 -->
            @endauth
        </nav>

    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>