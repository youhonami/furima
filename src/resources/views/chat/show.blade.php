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
                    <img src="{{ asset('storage/' . ($partner->profile->img ?? 'images/default-user-icon.png')) }}" class="chat__partner-icon">
                    「{{ $partner->name }}」さんとの取引画面
                </h1>
                @if(Auth::id() === $chat->buyer_id)
                @if(!$hasBuyerRated)
                <button type="button" class="chat__complete-btn">
                    <i class="fas fa-check-circle"></i> 取引を完了する
                </button>
                @else
                <button type="button" class="chat__complete-btn" disabled>取引完了済み</button>
                @endif
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
                        <div class="chat__message-user">
                            <img
                                src="{{ asset('storage/' . ($message->user->profile->img ?? 'images/default-user-icon.png')) }}"
                                alt="ユーザーアイコン"
                                class="chat__message-user-icon">
                            {{ $message->user->name }}
                        </div>
                        <div>{{ $message->message }}</div>
                        @if($message->image_path)
                        <div class="chat__message-image">
                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像">
                        </div>
                        @endif
                        @if(Auth::id() === $message->user_id)
                        <div class="chat__message-actions">
                            <a href="{{ route('chat.message.edit', [$chat->id, $message->id]) }}" class="chat__edit-btn">
                                <i class="fas fa-edit"></i> 編集
                            </a>
                            <form action="{{ route('chat.message.destroy', [$chat->id, $message->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="chat__delete-btn" onclick="return confirm('削除してもよろしいですか？')">
                                    <i class="fas fa-trash-alt"></i> 削除
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- チャット送信フォーム -->
            <form action="{{ route('chat.message.store', $chat->id) }}" method="POST" enctype="multipart/form-data" class="chat__form">
                @csrf

                {{-- 🆕 テキストエリアの上にバリデーションメッセージ --}}
                @if ($errors->any())
                <div class="chat__error-messages">
                    @foreach ($errors->all() as $error)
                    <div class="chat__error">{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <div class="chat__input-row">
                    <textarea id="chatMessage"
                        name="message"
                        class="chat__textarea"
                        rows="1"
                        placeholder="取引メッセージを入力してください"
                        data-chat-id="{{ $chat->id }}">{{ old('message') }}</textarea>

                    <label class="chat__image-btn">
                        <i class="fas fa-image"></i> 画像を追加
                        <input type="file" name="image" accept="image/*" style="display: none;">
                    </label>

                    <button type="submit" class="chat__send-btn">送信</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 画像拡大モーダル -->
    <div id="imageModal" class="modal">
        <span class="modal-close" id="modalClose">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>
</main>

<!-- 購入者評価モーダル -->
@if(!$hasBuyerRated && Auth::id() === $chat->buyer_id)
<div id="completeModal" class="modal">
    <div class="modal-content">
        <p>取引が完了しました。</p>
        <p>今回の取引相手はどうでしたか？</p>
        <div class="rating">
            @for ($i = 1; $i <= 5; $i++)
                <span class="star {{ $i == 1 ? 'selected' : '' }}" data-value="{{ $i }}">&#9733;</span>
                @endfor
        </div>
        <form id="ratingForm" action="{{ route('ratings.store') }}" method="POST">
            @csrf
            <input type="hidden" name="ratee_id" value="{{ $partner->id }}">
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="role" value="seller">
            <input type="hidden" name="rating" id="selectedRating" value="1">
            <button type="submit" class="modal-submit-btn">送信する</button>
        </form>
        <button id="closeModal">閉じる</button>
    </div>
</div>
@endif

<!-- 出品者評価モーダル -->
@if(!$hasSellerRated && Auth::id() === $item->user_id && $receivedCompleteMessage)
<div id="sellerRatingModal" class="modal">
    <div class="modal-content">
        <p>購入者を評価してください。</p>
        <div class="rating">
            @for ($i = 1; $i <= 5; $i++)
                <span class="seller-star {{ $i == 1 ? 'selected' : '' }}" data-value="{{ $i }}">&#9733;</span>
                @endfor
        </div>
        <form id="sellerRatingForm" action="{{ route('ratings.store') }}" method="POST">
            @csrf
            <input type="hidden" name="ratee_id" value="{{ $partner->id }}">
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="role" value="buyer">
            <input type="hidden" name="rating" id="sellerSelectedRating" value="1">
            <button type="submit" class="modal-submit-btn">送信する</button>
        </form>
        <button id="closeSellerModal">閉じる</button>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // draft保存
        const chatMessage = document.getElementById('chatMessage');
        const storageKey = 'chatMessageDraft_' + chatMessage.dataset.chatId;

        // ページ読み込み時、保存されたデータがあればセット
        const savedDraft = localStorage.getItem(storageKey);
        if (savedDraft) {
            chatMessage.value = savedDraft;
        }

        // 入力時に保存
        chatMessage.addEventListener('input', function() {
            localStorage.setItem(storageKey, chatMessage.value);
        });

        // 送信時に削除
        const chatForm = document.querySelector('.chat__form');
        chatForm.addEventListener('submit', function() {
            localStorage.removeItem(storageKey);
        });

        // チャットメッセージ初期位置を最下部にスクロール
        const messagesContainer = document.querySelector('.chat__messages');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // 完了ボタンのモーダル表示
        const completeBtn = document.querySelector('.chat__complete-btn');
        const completeModal = document.getElementById('completeModal');
        const closeModal = document.getElementById('closeModal');
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('selectedRating');
        let selectedRating = 0;

        if (completeBtn && completeModal && closeModal) {
            completeBtn.addEventListener('click', function() {
                completeModal.style.display = 'flex';
            });
            closeModal.addEventListener('click', function() {
                completeModal.style.display = 'none';
            });
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    selectedRating = parseInt(this.getAttribute('data-value'));
                    ratingInput.value = selectedRating;
                    stars.forEach(s => s.classList.toggle('selected', parseInt(s.getAttribute('data-value')) <= selectedRating));
                });
            });
        }

        // 出品者モーダル
        const sellerModal = document.getElementById('sellerRatingModal');
        const closeSellerModal = document.getElementById('closeSellerModal');
        const sellerStars = document.querySelectorAll('.seller-star');
        const sellerRatingInput = document.getElementById('sellerSelectedRating');
        let sellerSelectedRating = 0;

        if (sellerModal && closeSellerModal) {
            sellerModal.style.display = 'flex';
            closeSellerModal.addEventListener('click', function() {
                sellerModal.style.display = 'none';
            });
            sellerStars.forEach(star => {
                star.addEventListener('click', function() {
                    sellerSelectedRating = parseInt(this.getAttribute('data-value'));
                    sellerRatingInput.value = sellerSelectedRating;
                    sellerStars.forEach(s => s.classList.toggle('selected', parseInt(s.getAttribute('data-value')) <= sellerSelectedRating));
                });
            });
        }

        // 画像モーダル
        document.querySelectorAll('.chat__message-image img').forEach(image => {
            image.addEventListener('click', function() {
                const modal = document.getElementById('imageModal');
                const modalImg = document.getElementById('modalImage');
                modal.style.display = 'block';
                modalImg.src = this.src;
            });
        });

        // 画像モーダル閉じる
        const modalClose = document.getElementById('modalClose');
        modalClose.addEventListener('click', function() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        });
    });
</script>
@endsection