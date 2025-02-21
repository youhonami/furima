@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')

<main class="mypage__container">
    <section class="mypage__profile">
        <div class="mypage__icon">
            <img src="{{ $user->profile && $user->profile->img ? asset('storage/' . $user->profile->img) : asset('storage/images/default-user-icon.png') }}" alt="ユーザーアイコン">
        </div>
        <div class="mypage__info">
            <h1 class="mypage__username">{{ $user->name }}</h1>
            <a href="{{ route('profile.edit') }}" class="mypage__edit-btn">プロフィールを編集</a>
        </div>
    </section>

    <nav class="mypage__tab-menu">
        <a href="{{ route('mypage', ['tab' => 'listed']) }}" class="mypage__tab {{ $tab === 'listed' ? 'mypage__tab--active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['tab' => 'purchased']) }}" class="mypage__tab {{ $tab === 'purchased' ? 'mypage__tab--active' : '' }}">購入した商品</a>
    </nav>

    <div class="mypage__tab-content">
        @if ($tab === 'listed')
        <div class="mypage__item-grid">
            @if ($listedItems->isNotEmpty())
            @foreach ($listedItems as $item)
            <a href="{{ route('item.show', $item->id) }}" class="mypage__item-card {{ $item->isSold() ? 'mypage__item-card--sold' : '' }}">
                <img src="{{ $item->img ? asset('storage/' . $item->img) : asset('storage/images/product-placeholder.png') }}" alt="{{ $item->name }}" class="mypage__item-image">
                <h2 class="mypage__item-name">{{ Str::limit($item->name, 20, '...') }}</h2>
                @if ($item->isSold())
                <p class="mypage__sold-label">Sold</p>
                @endif
            </a>
            @endforeach
            @else
            <p>出品した商品はありません。</p>
            @endif
        </div>
        @elseif ($tab === 'purchased')
        <div class="mypage__item-grid">
            @if ($purchasedItems->isNotEmpty())
            @foreach ($purchasedItems as $purchase)
            <div class="mypage__item-card mypage__item-card--sold">
                <img src="{{ $purchase->item->img ? asset('storage/' . $purchase->item->img) : asset('storage/images/product-placeholder.png') }}" alt="{{ $purchase->item->name }}" class="mypage__item-image">
                <h2 class="mypage__item-name">{{ Str::limit($item->name,20, '...') }}</h2>
                <p class="mypage__sold-label">Sold</p>
            </div>
            @endforeach
            @else
            <p>購入した商品はありません。</p>
            @endif
        </div>
        @endif
    </div>
</main>

@endsection