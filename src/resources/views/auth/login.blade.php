@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login">
    <h1 class="login__title">ログイン</h1>
    <form class="login__form" action="{{ route('login') }}" method="post">
        @csrf
        <div class="login__group">
            <label for="email" class="login__label">メールアドレス</label>
            <div class="login__input-wrapper">
                <input id="email" type="text" name="email" class="login__input" value="{{ old('email') }}">
                @error('email')
                <div class="login__error">{{ $message }}</div>
                @enderror
            </div>
            @if (session('status'))
            <div class="login__status">{{ session('status') }}</div>
            @endif
        </div>

        <div class="login__group">
            <label for="password" class="login__label">パスワード</label>
            <div class="login__input-wrapper">
                <input id="password" type="password" name="password" class="login__input">
                @error('password')
                <div class="login__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button class="login__button" type="submit">ログインする</button>
    </form>
    <div class="login__register-link">
        <a class="login__register-button" href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection