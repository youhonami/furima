@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login__content">
    <div class="login-form__heading">
        <h1 class="login__title">ログイン</h1>
    </div>
    <form class="form" action="{{ route('login') }}" method="post">
        @csrf
        <div class="form__group">
            <label for="email" class="form__label">メールアドレス</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="email" type="text" name="email" value="{{ old('email') }}" />
                </div>
                @error('email')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form__group">
            <label for="password" class="form__label">パスワード</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="password" type="password" name="password" />
                </div>
                @error('password')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">ログインする</button>
        </div>
    </form>
    <div class="register__link">
        <a class="register__button-submit" href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection