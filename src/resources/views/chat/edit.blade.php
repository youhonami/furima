@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<main class="chat">
    <div class="chat__container">
        <h1 class="chat__title">メッセージ編集</h1>

        <div class="chat__item-info">
            <div class="chat__item-image">
                <img src="{{ asset('storage/' . $chat->item->img) }}" alt="{{ $chat->item->name }}">
            </div>
            <div class="chat__item-details">
                <h2>{{ $chat->item->name }}</h2>
                <p>¥{{ number_format($chat->item->price) }}</p>
            </div>
        </div>

        <form action="{{ route('chat.message.update', [$chat->id, $message->id]) }}" method="POST" class="chat__form">
            @csrf
            @method('PUT')
            <textarea name="message" class="chat__textarea" rows="5" placeholder="メッセージを編集してください">{{ old('message', $message->message) }}</textarea>
            <div class="chat__form-actions">
                <button type="submit" class="chat__send-btn">更新</button>
                <a href="{{ route('chat.show', $chat->id) }}" class="chat__cancel-btn">キャンセル</a>
            </div>
        </form>
    </div>
</main>
@endsection