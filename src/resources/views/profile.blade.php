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
                <!--  デフォルト画像の設定 -->
                @php
                $defaultImage = asset('storage/images/default-user-icon.png');
                $userImage = $user->profile && $user->profile->img ? asset('storage/' . $user->profile->img) : $defaultImage;
                @endphp

                <!-- プレビュー用の画像タグ -->
                <img id="profile-preview" src="{{ $userImage }}" alt="ユーザーアイコン" style="width: 120px; height: 120px; border-radius: 50%; border: 1px solid #ccc;">

                <label for="profile_image" class="profile__icon-btn">画像を選択する</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)">

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

<!-- プレビュー用JavaScript -->
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('profile-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection