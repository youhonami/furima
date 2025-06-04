<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>furima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__logo">
            @unless (request()->is('email/verify'))
            <a href="/">
                <img src="{{ asset('storage/images/logo.svg') }}" alt="COACHTECH Logo">
            </a>
            @else
            <img src="{{ asset('storage/images/logo.svg') }}" alt="COACHTECH Logo">
            @endunless
        </div>

        @unless (request()->is('login') || request()->is('register') || request()->is('email/verify'))
        <div class="header__search-bar">
            <form method="GET" action="{{ route('items.index') }}" id="searchForm">
                <input type="hidden" name="filter" value="{{ request('filter', 'recommended') }}">
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    placeholder="なにをお探しですか？"
                    value="{{ request('search') }}">
            </form>
        </div>

        <nav class="header__nav-links">
            @auth
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
            <a href="{{ route('mypage') }}">マイページ</a>
            <a href="{{ route('sell.index') }}">出品</a>
            @else
            <a href="{{ route('login') }}">ログイン</a>
            <a href="{{ route('login') }}">マイページ</a>
            <a href="{{ route('login') }}">出品</a>
            @endauth
        </nav>
        @endunless
    </header>

    <main class="main">
        @yield('content')
    </main>

    <footer class="footer">
        &copy; 2025 COACHTECH
    </footer>

    @yield('scripts') {{-- ← これを追加 --}}
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);

            // 入力後500ms待ってから検索を実行
            timeout = setTimeout(() => {
                searchForm.submit();
            }, 500);
        });
    });
</script>

</html>