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
                <form>
                    <select name="payment_method" class="payment-method" required>
                        <option value="" selected disabled>選択してください</option>
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
            </div>
        </div>

        <!-- 右側：商品情報 -->
        <div class="product-summary">
            <h1 class="product-name">{{ $item->name }}</h1>
            <p class="product-price">¥{{ number_format($item->price) }}</p>

            <!-- 商品IDをセッションに保存 -->
            @php
            session(['current_item_id' => $item->id]);
            @endphp

            <!-- 小計画面 -->
            <div class="subtotal-box">
                <h2>小計</h2>
                <p>商品代金: <span class="subtotal-price">¥{{ number_format($item->price) }}</span></p>
                <p>支払い方法: <span id="selected-payment">未選択</span></p>
            </div>

            <form action="{{ route('purchase.store') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method">
                <button type="submit" class="purchase-button" onclick="setPaymentMethod()">購入する</button>
            </form>
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

    function setPaymentMethod() {
        const selectedMethod = document.querySelector('.payment-method').value;
        document.getElementById('payment-method').value = selectedMethod;
    }
</script>
@endsection