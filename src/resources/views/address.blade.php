@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-container">
    <h1 class="address-title">住所の変更</h1>
    <form action="{{ route('address.update') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="form-input" value="{{ old('postal_code', session('temp_address.postal_code') ?? $profile->postal_code ?? '') }}" placeholder="郵便番号を入力してください">
        </div>

        <div class="form-group">
            <label for="address" class="form-label">住所</label>
            <input type="text" id="address" name="address" class="form-input" value="{{ old('address', session('temp_address.address') ?? $profile->address ?? '') }}" placeholder="住所を入力してください">
        </div>

        <div class="form-group">
            <label for="building" class="form-label">建物名</label>
            <input type="text" id="building" name="building" class="form-input" value="{{ old('building', session('temp_address.building') ?? $profile->building ?? '') }}" placeholder="建物名を入力してください">
        </div>

        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>
@endsection