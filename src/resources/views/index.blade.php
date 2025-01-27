@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<main>


    <!-- タブメニュー -->
    <div class="tab-menu">
        <a href="{{ route('item.index', ['filter' => 'recommended', 'search' => request('search')]) }}"
            class="tab {{ request('filter') !== 'mylist' ? 'active' : '' }}">おすすめ</a>

        <a href="{{ route('item.index', ['filter' => 'mylist', 'search' => request('search')]) }}"
            class="tab {{ request('filter') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>



    <!-- 商品一覧 -->
    <div class="item-grid">
        @foreach($items as $item)
        @if($item->isSold())
        <!-- 購入済み商品 -->
        <div class="item-card sold">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="item-image">
            <h2 class="item-name">{{ $item->name }}</h2>
            <p class="sold-label">Sold</p>
        </div>
        @else
        <!-- 購入可能商品 -->
        <a href="{{ route('item.show', $item->id) }}" class="item-card">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="item-image">
            <h2 class="item-name">{{ $item->name }}</h2>
        </a>
        @endif
        @endforeach
    </div>

    <!-- 検索結果がない場合のメッセージ -->
    @if($items->isEmpty())
    <p>検索結果が見つかりませんでした。</p>
    @endif
</main>

<footer class="footer">
    © 2025 COACHTECH
</footer>
</body>

</html>
@endsection