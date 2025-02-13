@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address__container">
    <h1 class="address__title">住所の変更</h1>
    <form action="{{ route('address.update') }}" method="POST" class="address__form">
        @csrf
        <div class="address__group">
            <label for="postal_code" class="address__label">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="address__input" value="{{ old('postal_code', session('temp_address.postal_code') ?? $profile->postal_code ?? '') }}" placeholder="郵便番号を入力してください">
            @error('postal_code')
            <p class="address__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="address__group">
            <label for="address" class="address__label">住所</label>
            <input type="text" id="address" name="address" class="address__input" value="{{ old('address', session('temp_address.address') ?? $profile->address ?? '') }}" placeholder="住所を入力してください">
            @error('address')
            <p class="address__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="address__group">
            <label for="building" class="address__label">建物名</label>
            <input type="text" id="building" name="building" class="address__input" value="{{ old('building', session('temp_address.building') ?? $profile->building ?? '') }}" placeholder="建物名を入力してください">
            @error('building')
            <p class="address__error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="address__submit">更新する</button>
    </form>
</div>
@endsection