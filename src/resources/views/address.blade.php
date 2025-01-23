@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')

<main class="main">
    <div class="address-container">
        <h1 class="title">住所の変更</h1>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}" placeholder="郵便番号を入力してください">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $profile->address ?? '') }}" placeholder="住所を入力してください">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', $profile->building ?? '') }}" placeholder="建物名を入力してください">
        </div>

        <!-- ボタン表示のみ -->
        <button class="update-button" disabled>更新する</button>
    </div>
</main>

<footer class="footer">
    © 2025 COACHTECH
</footer>

@endsection