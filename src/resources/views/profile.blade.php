@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<main class="profile">
    <h1 class="profile__title">プロフィール設定</h1>
    <div class="profile__card">
        <form action="{{ route('profile.update') }}" method="POST" class="profile__form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile__icon">
                <img src="{{ $user->profile && $user->profile->img ? asset('storage/' . $user->profile->img) : asset('storage/images/default-user-icon.png') }}" alt="ユーザーアイコン">
                <label for="profile_image" class="profile__icon-btn">画像を選択する</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                @error('profile_image')
                <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="profile__group">
                <label for="name" class="profile__label">ユーザー名</label>
                <input type="text" id="name" name="name" class="profile__input" value="{{ old('name', $user->name) }}">
                @error('name')
                <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="profile__group">
                <label for="postal_code" class="profile__label">郵便番号</label>
                <input type="text" id="postal_code" name="postal_code" class="profile__input" value="{{ old('postal_code', $user->profile->postal_code) }}">
                @error('postal_code')
                <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="profile__group">
                <label for="address" class="profile__label">住所</label>
                <input type="text" id="address" name="address" class="profile__input" value="{{ old('address', $user->profile->address) }}">
                @error('address')
                <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="profile__group">
                <label for="building" class="profile__label">建物名</label>
                <input type="text" id="building" name="building" class="profile__input" value="{{ old('building', $user->profile->building) }}">
                @error('building')
                <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="profile__button">更新する</button>
        </form>
    </div>
</main>
@endsection