{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.header')

@section('content')
<div class="container">
    <h1>メールアドレスの確認</h1>

    @if (session('message'))
    <div class="alert alert-success" role="alert">
        {{ session('message') }}
    </div>
    @endif

    <p>
        続行する前に、登録時に入力したメールアドレス宛に
        認証用リンクが送信されていますのでご確認ください。<br>
        もしメールを受信していない場合は、以下のボタンから再送信を行ってください。
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection