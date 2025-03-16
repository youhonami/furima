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
            <form method="GET" action="{{ route('item.index') }}" id="searchForm">
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
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        let timeout = null;

        searchInput.addEventListener('input', function(event) {
            clearTimeout(timeout);

            timeout = setTimeout(() => {
                performSearch();
            }, 500);
        });

        function performSearch() {
            const searchQuery = searchInput.value;
            const filterValue = document.querySelector('input[name="filter"]').value;
            const url = `{{ route('item.index') }}?search=${encodeURIComponent(searchQuery)}&filter=${encodeURIComponent(filterValue) }`;

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // メインコンテンツだけを更新
                    const newMainContent = doc.querySelector('.main');
                    if (newMainContent) {
                        document.querySelector('.main').innerHTML = newMainContent.innerHTML;
                    }

                    // 検索ボックスにフォーカスを戻す
                    searchInput.focus();
                })
                .catch(error => console.error('検索エラー:', error));
        }

        // フォームのデフォルト送信を防ぐ
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            performSearch();
        });
    });
</script>


</html>