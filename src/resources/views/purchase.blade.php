@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

<main class="main">
    <div class="purchase-container">
        <div class="product-details">
            <!-- 商品画像 -->
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="product-image">
            <!-- 商品名 -->
            <h1 class="product-name">{{ $item->name }}</h1>
            <!-- 商品価格 -->
            <p class="product-price">¥{{ number_format($item->price) }}</p>
        </div>

        <div class="payment-details">
            <h2>商品代金</h2>
            <p class="price">¥{{ number_format($item->price) }}</p>

            <h2>支払い方法</h2>
            <select class="payment-method">
                <option value="" selected>選択してください</option>
                <option value="convenience_store">コンビニ払い</option>
                <option value="credit_card">クレジットカード</option>
            </select>

            <h2>配送先</h2>
            @if ($profile)
            <p class="address">
                〒{{ $profile->postal_code }}<br>
                {{ $profile->address }}<br>
                {{ $profile->building }}
            </p>
            @else
            <p class="address">配送先が登録されていません。</p>
            @endif
            <a href="{{ route('address.edit') }}" class="change-address">変更する</a>

            <button class="purchase-button">購入する</button>
        </div>
    </div>
</main>

<footer class="footer">
    © 2025 COACHTECH
</footer>
@endsection