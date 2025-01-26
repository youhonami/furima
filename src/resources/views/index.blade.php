@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<main>

    <!-- タブメニュー -->
    <div class="tab-menu">
        <a href="{{ route('item.index', ['filter' => 'recommended']) }}"
            class="tab {{ request('filter') !== 'mylist' ? 'active' : '' }}">おすすめ</a>

        <!-- ログインしている場合は通常のリンク -->
        <a href="{{ route('item.index', ['filter' => 'mylist']) }}"
            class="tab {{ request('filter') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    <!-- 商品一覧 -->
    <div class="item-grid">
        @foreach($items as $item)
        <a href="{{ route('item.show', $item->id) }}" class="item-card">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="item-image">
            <h2 class="item-name">{{ $item->name }}</h2>
        </a>
        @endforeach
    </div>

    <!-- 検索結果がない場合のメッセージ -->
    @if($items->isEmpty())
    <p>マイリストが見つかりません。</p>
    @endif
</main>

<footer class="footer">
    © 2025 COACHTECH
</footer>
</body>

</html>
@endsection