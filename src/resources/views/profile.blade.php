@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<main class="profile-container">
    <h1 class="profile-title">プロフィール設定</h1>
    <div class="profile-card">
        <form action="{{ route('profile.update') }}" method="POST" class="profile-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="profile-icon">
                <img src="{{ $user->profile && $user->profile->img ? asset('storage/' . $user->profile->img) : asset('storage/images/default-user-icon.png') }}" alt="ユーザーアイコン">
                <label for="profile_image" class="change-icon-btn">画像を選択する</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                @error('profile_image')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                @error('name')
                <p class="error-message">{{ $message }}</p>
                @enderror

            </div>

            <div class="form-group">
                <label for="postal_code">郵便番号</label>
                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->profile->postal_code) }}">
                @error('postal_code')
                <p class="error-message">{{ $message }}</p>
                @enderror

            </div>

            <div class="form-group">
                <label for="address">住所</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->profile->address) }}">
                @error('address')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="building">建物名</label>
                <input type="text" id="building" name="building" value="{{ old('building', $user->profile->building) }}">
                @error('building')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="update-btn">更新する</button>
        </form>

    </div>
</main>
@endsection