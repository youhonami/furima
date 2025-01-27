@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')

<main class="mypage-container">
    <section class="profile">
        <div class="user-icon">
            <!-- プロフィール画像を表示 -->
            <img src="{{ $user->profile && $user->profile->img ? asset('storage/' . $user->profile->img) : asset('storage/images/default-user-icon.png') }}" alt="ユーザーアイコン">
        </div>
        <div class="user-info">
            <h1>{{ $user->name }}</h1>
            <a href="{{ route('profile.edit') }}" class="edit-profile">プロフィールを編集</a>
        </div>
    </section>

    <!-- タブメニュー -->
    <div class="tab-menu">
        <a href="{{ route('mypage', ['tab' => 'listed']) }}" class="tab {{ $tab === 'listed' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['tab' => 'purchased']) }}" class="tab {{ $tab === 'purchased' ? 'active' : '' }}">購入した商品</a>
    </div>

    <!-- タブ内容 -->
    <div class="tab-content">
        @if ($tab === 'listed')
        <!-- 出品した商品 -->
        <div class="item-grid">
            @if ($listedItems->isNotEmpty())
            @foreach ($listedItems as $item)
            <a href="{{ route('item.show', $item->id) }}" class="item-card {{ $item->isSold() ? 'sold' : '' }}">
                <img src="{{ $item->img ? asset('storage/' . $item->img) : asset('storage/images/product-placeholder.png') }}" alt="{{ $item->name }}" class="item-image">
                <h2 class="item-name">{{ $item->name }}</h2>
                @if ($item->isSold())
                <p class="sold-label">Sold</p>
                @endif
            </a>
            @endforeach
            @else
            <p>出品した商品はありません。</p>
            @endif
        </div>
        @elseif ($tab === 'purchased')
        <!-- 購入した商品 -->
        <div class="item-grid">
            @if ($purchasedItems->isNotEmpty())
            @foreach ($purchasedItems as $purchase)
            <div class="item-card sold">
                <img src="{{ $purchase->item->img ? asset('storage/' . $purchase->item->img) : asset('storage/images/product-placeholder.png') }}" alt="{{ $purchase->item->name }}" class="item-image">
                <h2 class="item-name">{{ $purchase->item->name }}</h2>
                <p class="sold-label">Sold</p>
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