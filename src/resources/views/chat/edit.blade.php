@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat_edit.css') }}">
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

        <form action="{{ route('chat.message.update', [$chat->id, $message->id]) }}" method="POST" enctype="multipart/form-data" class="chat__form">
            @csrf
            @method('PUT')

            <!-- エラーメッセージ表示 -->
            @if ($errors->any())
            <div class="chat__error-messages">
                @foreach ($errors->all() as $error)
                <div class="chat__error">{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <label for="message" class="chat__textarea-label">メッセージ</label>
            <textarea name="message" class="chat__textarea" rows="5" placeholder="メッセージを編集してください">{{ old('message', $message->message) }}</textarea>

            <!-- 既存の画像がある場合 -->
            @if($message->image_path)
            <div class="chat__message-image">
                <p>現在の画像:</p>
                <img src="{{ asset('storage/' . $message->image_path) }}" alt="現在の画像">
            </div>
            @endif

            <!-- 新しい画像をアップロード -->
            <div class="chat__input-wrapper">
                <label class="chat__image-btn">
                    <i class="fas fa-image"></i> 画像を変更
                    <input type="file" name="image" accept="image/*" style="display: none;">
                </label>
            </div>

            <div class="chat__form-actions">
                <button type="submit" class="chat__send-btn">更新</button>
                <a href="{{ route('chat.show', $chat->id) }}" class="chat__cancel-btn">キャンセル</a>
            </div>
        </form>

    </div>
</main>
@endsection