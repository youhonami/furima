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

        <!-- 商品IDをセッションに保存 -->
        @php
        session(['current_item_id' => $item->id]);
        @endphp


        <div class="payment-details">
            <h2>商品代金</h2>
            <p class="price">¥{{ number_format($item->price) }}</p>

            <h2>支払い方法</h2>
            <form action="{{ route('purchase.store') }}" method="POST">
                @csrf
                <select name="payment_method" class="payment-method" required>
                    <option value="" selected>選択してください</option>
                    <option value="convenience_store">コンビニ払い</option>
                    <option value="credit_card">クレジットカード</option>
                </select>

            </form>

            <h2>配送先</h2>
            @if (session('temp_address'))
            <p class="address">
                〒{{ session('temp_address.postal_code') }}<br>
                {{ session('temp_address.address') }}<br>
                {{ session('temp_address.building') }}
            </p>
            @elseif ($profile)
            <p class="address">
                〒{{ $profile->postal_code }}<br>
                {{ $profile->address }}<br>
                {{ $profile->building }}
            </p>
            @else
            <p class="address">配送先が登録されていません。</p>
            @endif
            <a href="{{ route('address.edit') }}" class="change-address">変更する</a>


            <form action="{{ route('purchase.store') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method">
                <button type="submit" class="purchase-button" onclick="setPaymentMethod()">購入する</button>
            </form>

            <script>
                function setPaymentMethod() {
                    const selectedMethod = document.querySelector('.payment-method').value;
                    document.getElementById('payment-method').value = selectedMethod;
                }
            </script>
        </div>
    </div>
</main>

<footer class="footer">
    © 2025 COACHTECH
</footer>
@endsection