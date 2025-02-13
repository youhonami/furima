@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register">
    <h1 class="register__title">会員登録</h1>
    <form class="register__form" action="/register" method="post">
        @csrf
        <!-- 名前入力 -->
        <div class="register__group">
            <label for="name" class="register__label">ユーザー名</label>
            <input id="name" type="text" name="name" class="register__input" value="{{ old('name') }}">
            @error('name')
            <div class="register__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- メールアドレス入力 -->
        <div class="register__group">
            <label for="email" class="register__label">メールアドレス</label>
            <input id="email" type="text" name="email" class="register__input" value="{{ old('email') }}">
            @error('email')
            <div class="register__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- パスワード入力 -->
        <div class="register__group">
            <label for="password" class="register__label">パスワード</label>
            <input id="password" type="password" name="password" class="register__input">
            @error('password')
            <div class="register__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- 確認用パスワード入力 -->
        <div class="register__group">
            <label for="password_confirmation" class="register__label">確認用パスワード</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="register__input">
            @error('password_confirmation')
            <div class="register__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- 登録ボタン -->
        <button class="register__button" type="submit">登録する</button>
    </form>

    <!-- ログインリンク -->
    <div class="register__login-link">
        <a class="register__login-button" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection