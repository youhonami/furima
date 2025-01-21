@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login__content">
    <div class="login-form__heading">
        <h1 class="login__title">ログイン</h1>
    </div>
    <form class="form" action="/login" method="post">
        @csrf
        <div class="form__group">
            <label for="email" class="form__label">ユーザー名 / メールアドレス</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required />
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <label for="password" class="form__label">パスワード</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="password" type="password" name="password" required />
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
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