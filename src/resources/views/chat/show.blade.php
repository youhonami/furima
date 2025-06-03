@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<main class="chat">
    <div class="chat__layout">
        <!-- サイドバー -->
        <aside class="chat__sidebar">
            <h2 class="chat__sidebar-title">その他の取引</h2>
            <ul class="chat__sidebar-list">
                @foreach($otherChats as $otherChat)
                <li>
                    <a href="{{ route('chat.show', $otherChat->id) }}" class="chat__sidebar-item">
                        <p class="chat__sidebar-name">{{ Str::limit($otherChat->item->name, 20, '...') }}</p>
                    </a>
                </li>
                @endforeach
            </ul>
        </aside>

        <!-- チャットメインエリア -->
        <div class="chat__container">
            <div class="chat__header">
                <h1 class="chat__title">
                    「{{ $partner->name }}」さんとの取引画面
                </h1>
                @if(Auth::id() === $chat->buyer_id)
                <button type="button" class="chat__complete-btn">
                    <i class="fas fa-check-circle"></i> 取引を完了する
                </button>
                @endif
            </div>

            <div class="chat__item-info">
                <div class="chat__item-image">
                    <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}">
                </div>
                <div class="chat__item-details">
                    <h2>{{ $item->name }}</h2>
                    <p>¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <div class="chat__messages">
                @foreach($messages as $message)
                <div class="chat__message {{ $message->user_id === Auth::id() ? 'chat__message--own' : '' }}">
                    <div class="chat__message-content">
                        <div class="chat__message-user">{{ $message->user->name }}</div>
                        <div>{{ $message->message }}</div>
                        @if($message->image_path)
                        <div class="chat__message-image">
                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像">
                        </div>
                        @endif
                    </div>
                    @if($message->user_id === Auth::id())
                    <div class="chat__message-actions">
                        <a href="{{ route('chat.message.edit', [$chat->id, $message->id]) }}" class="chat__edit-btn">編集</a>
                        <form action="{{ route('chat.message.destroy', [$chat->id, $message->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="chat__delete-btn">削除</button>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <form action="{{ route('chat.message.store', $chat->id) }}" method="POST" enctype="multipart/form-data" class="chat__form">
                @csrf

                {{-- テキストエリアの上にエラーメッセージ --}}
                @if ($errors->any())
                <div class="chat__error-messages">
                    @foreach ($errors->all() as $error)
                    <div class="chat__error">{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <div class="chat__input-wrapper">
                    <textarea id="chatMessage" name="message" class="chat__textarea" rows="1" placeholder="取引メッセージを入力してください">{{ old('message') }}</textarea>
                    <label class="chat__image-btn">
                        <i class="fas fa-image"></i> 画像を追加
                        <input type="file" name="image" accept="image/*" style="display: none;">
                    </label>
                </div>
                <button type="submit" class="chat__send-btn">送信</button>
            </form>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('chatMessage');
        const chatId = '{{ $chat->id }}';
        const storageKey = 'chat_message_draft_' + chatId;

        // LocalStorageから下書きを読み込み（old()がなければ）
        if (!textarea.value) {
            const savedDraft = localStorage.getItem(storageKey);
            if (savedDraft) {
                textarea.value = savedDraft;
            }
        }

        // 入力イベントで保存
        textarea.addEventListener('input', function() {
            localStorage.setItem(storageKey, textarea.value);
        });

        // 送信時に下書きを削除
        const form = textarea.closest('form');
        form.addEventListener('submit', function() {
            localStorage.removeItem(storageKey);
        });
    });
</script>
@endsection