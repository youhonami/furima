@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

<main class="purchase__main">
    <div class="purchase__container">
        <!-- 左側：商品画像 -->
        <div class="purchase__details">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="purchase__image">

            <!-- 支払い方法と配送先 -->
            <div class="purchase__info">
                <h2 class="purchase__info-title">支払い方法</h2>
                <form action="{{ route('purchase.store') }}" method="POST">
                    @csrf
                    <select name="payment_method" class="purchase__payment-method">
                        <option value="" selected disabled>選択してください</option>
                        <option value="convenience_store">コンビニ払い</option>
                        <option value="credit_card">クレジットカード</option>
                    </select>
                    @error('payment_method')
                    <p class="purchase__error">{{ $message }}</p>
                    @enderror

                    <h2 class="purchase__info-title">配送先</h2>
                    <p class="purchase__address">
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
                    <a href="{{ route('address.edit') }}" class="purchase__change-address">変更する</a>

                    <button type="submit" class="purchase__button">購入する</button>
                </form>
            </div>
        </div>

        <!-- 右側：商品情報 -->
        <div class="purchase__summary">
            <h1 class="purchase__product-name">{{ $item->name }}</h1>
            <p class="purchase__product-price">¥{{ number_format($item->price) }}</p>

            <div class="purchase__subtotal">
                <h2 class="purchase__subtotal-title">小計</h2>
                <p>商品代金: <span class="purchase__subtotal-price">¥{{ number_format($item->price) }}</span></p>
                <p>支払い方法: <span id="selected-payment">未選択</span></p>
            </div>
        </div>
    </div>
</main>
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.querySelector('.purchase__payment-method').addEventListener('change', function() {
        document.getElementById('selected-payment').innerText = this.options[this.selectedIndex].text;
    });
</script>

@endsection