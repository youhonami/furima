@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

<main class="main">
    <div class="purchase-container">
        <!-- 左側：商品画像 -->
        <div class="product-details">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="product-image">

            <!-- 支払い方法と配送先 -->
            <div class="purchase-info">
                <h2>支払い方法</h2>
                <form action="{{ route('purchase.store') }}" method="POST">
                    @csrf
                    <select name="payment_method" class="payment-method">
                        <option value="" selected disabled>選択してください</option>
                        <option value="convenience_store">コンビニ払い</option>
                        <option value="credit_card">クレジットカード</option>
                    </select>

                    <!-- 支払い方法のバリデーションエラー -->
                    @error('payment_method')
                    <p class="error-message">{{ $message }}</p>
                    @enderror

                    <h2>配送先</h2>
                    <p class="address">
                        @if (session('temp_address'))
                        〒{{ session('temp_address.postal_code') }}<br>
                        {{ session('temp_address.address') }}<br>
                        {{ session('temp_address.building') }}
                        @elseif ($profile)
                        〒{{ $profile->postal_code }}<br>
                        {{ $profile->address }}<br>
                        {{ $profile->building }}
                        @else
                        配送先が登録されていません。
                        @endif
                    </p>
                    <a href="{{ route('address.edit') }}" class="change-address">変更する</a>

                    <button type="submit" class="purchase-button">購入する</button>
                </form>
            </div>
        </div>

        <!-- 右側：商品情報 -->
        <div class="product-summary">
            <h1 class="product-name">{{ $item->name }}</h1>
            <p class="product-price">¥{{ number_format($item->price) }}</p>

            <!-- 小計画面 -->
            <div class="subtotal-box">
                <h2>小計</h2>
                <p>商品代金: <span class="subtotal-price">¥{{ number_format($item->price) }}</span></p>
                <p>支払い方法: <span id="selected-payment">未選択</span></p>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    © 2025 COACHTECH
</footer>

<script>
    document.querySelector('.payment-method').addEventListener('change', function() {
        document.getElementById('selected-payment').innerText = this.options[this.selectedIndex].text;
    });
</script>

@endsection