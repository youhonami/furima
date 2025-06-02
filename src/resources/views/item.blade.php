@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<main class="product">
    <div class="product__container">
        <div class="product__image-block">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}">
        </div>

        <div class="product__details">
            <h1 class="product__name">{{ $item->name }}</h1>
            <p class="product__brand">{{ $item->brand ?? 'ノーブランド' }}</p>

            <p class="product__price">¥{{ number_format($item->price) }}（税込）</p>

            <div class="product__actions">
                <div class="product__like-comment">
                    <!-- いいねボタン -->
                    @auth
                    <form action="{{ route('like', ['id' => $item->id]) }}" method="POST" class="product__like-form">
                        @csrf
                        <button type="submit" class="product__like-btn {{ $item->isLikedBy(Auth::user()) ? 'liked' : '' }}">
                            {{ $item->isLikedBy(Auth::user()) ? '❤️' : '🤍' }}
                        </button>

                        <span class="product__like-count">{{ $item->likes->count() }}</span>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="product__like-btn">🤍</a>
                    <span class="product__like-count">{{ $item->likes->count() }}</span>
                    @endauth

                    <!-- コメント数アイコン -->
                    <div class="product__comment-count">
                        <i class="bi bi-chat"></i>
                        <span>{{ $item->comments->count() }}</span>
                    </div>
                </div>

                <!-- 購入手続き・出品者へ連絡ボタン -->
                <div class="product__purchase-btn-wrap">
                    @auth
                    @if ($item->user_id !== Auth::id())
                    <!-- 出品者以外のみ有効 -->
                    <a href="{{ route('purchase', ['id' => $item->id]) }}" class="product__purchase-btn">
                        <i class="bi bi-cart-fill"></i> 購入手続きへ
                    </a>
                    <a href="{{ route('chat.show', ['chat' => $chat->id]) }}" class="product__contact-btn">
                        <i class="bi bi-envelope-fill"></i> 出品者へ連絡
                    </a>

                    @else
                    <!-- 出品者には無効化 -->
                    <button class="product__purchase-btn product__purchase-btn--disabled" disabled>
                        <i class="bi bi-cart-fill"></i> 購入手続きへ
                    </button>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="product__purchase-btn">
                        <i class="bi bi-cart-fill"></i> 購入手続きへ
                    </a>
                    <a href="{{ route('login') }}" class="product__contact-btn">
                        <i class="bi bi-envelope-fill"></i> 出品者へ連絡
                    </a>
                    @endauth
                </div>
            </div>

            <div class="product__description">
                <h2>商品説明</h2>
                <p>{{ $item->description }}</p>
            </div>

            <div class="product__info">
                <h2>商品の情報</h2>
                <p>
                    カテゴリー：
                    @foreach ($item->categories as $category)
                    {{ $category->name }}{{ !$loop->last ? '、' : '' }}
                    @endforeach
                </p>
                <p>商品の状態：{{ $item->condition ? $item->condition->condition : '状態不明' }}</p>
            </div>

            <!-- コメントセクション -->
            <div class="product__comments">
                <!-- コメント一覧 -->
                <div class="product__comment-section">
                    <h2>コメント ({{ $item->comments->count() }})</h2>
                    <div class="product__comment-list">
                        @forelse ($item->comments as $comment)
                        <div class="product__comment">
                            <p>
                                <img
                                    src="{{ $comment->user->profile->img ? asset('storage/' . $comment->user->profile->img) : asset('images/default-icon.png') }}"
                                    alt="ユーザーアイコン"
                                    class="product__user-icon">
                                <strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}
                            </p>
                            <p class="product__comment-time">{{ $comment->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @empty
                        <p>まだコメントがありません。</p>
                        @endforelse
                    </div>
                </div>

                <h2>商品へのコメント</h2>
                <!-- コメント投稿フォーム -->
                <form action="{{ route('comments.store', ['item' => $item->id]) }}" method="POST" class="product__comment-form">
                    @csrf
                    <textarea name="content" class="product__comment-textarea" placeholder="コメントを入力してください">{{ old('content') }}</textarea>

                    @error('content')
                    <p class="product__error-message">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="product__comment-submit">コメントを投稿</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection