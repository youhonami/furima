@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register__content">
    <div class="register-form__heading">
        <h1 class="register__title">会員登録</h1>
    </div>
    <form class="form" action="/register" method="post">
        @csrf
        <!-- 名前入力 -->
        <div class="form__group">
            <label for="name" class="form__label">ユーザー名</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="name" type="text" name="name" value="{{ old('name') }}" />
                </div>
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- メールアドレス入力 -->
        <div class="form__group">
            <label for="email" class="form__label">メールアドレス</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="email" type="text" name="email" value="{{ old('email') }}" />

                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }} <!-- エラーメッセージを表示 -->
                    @enderror
                </div>
            </div>
        </div>


        <!-- パスワード入力 -->
        <div class="form__group">
            <label for="password" class="form__label">パスワード</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="password" type="password" name="password" />
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 確認用パスワード入力 -->
        <div class="form__group">
            <label for="password_confirmation" class="form__label">確認用パスワード</label>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input id="password_confirmation" type="password" name="password_confirmation" />
                </div>
                <div class="form__error">
                    @error('password_confirmation')
                    {{ $message }} <!-- エラーメッセージを表示 -->
                    @enderror
                </div>
            </div>
        </div>


        <!-- 登録ボタン -->
        <div class="form__button">
            <button class="form__button-submit" type="submit">登録する</button>
        </div>
    </form>
    <div class="login__link">
        <a class="login__button-submit" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection