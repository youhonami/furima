@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')

<main class="main">
    <div class="item-container">
        <div class="item-image">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}">
        </div>
        <div class="item-details">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="item-price">¥{{ number_format($item->price) }}（税込）</p>
            <div class="item-actions">
                <form action="{{ route('like', ['id' => $item->id]) }}" method="POST" class="like-form">
                    @csrf
                    <button type="submit" class="like-btn">
                        {{ $item->isLikedBy(Auth::user()) ? '❤️' : '🤍' }}
                    </button>
                    <span class="like-count">{{ $item->likes->count() }}</span>
                </form>
                <!-- コメント数アイコン -->
                <div class="comment-count">
                    <i class="bi bi-chat"></i> <!-- シンプルな吹き出し -->
                    <span>{{ $item->comments->count() }}</span> <!-- コメント数 -->
                </div>
                <!-- 購入手続きボタン -->
                <a href="{{ route('purchase', ['id' => $item->id]) }}" class="purchase-btn">購入手続きへ</a>
            </div>


            <div class="item-description">
                <h2>商品説明</h2>
                <p>{{ $item->description }}</p>
            </div>
            <div class="item-info">
                <h2>商品の情報</h2>
                <p>カテゴリー：
                    @foreach ($item->categories as $category)
                    {{ $category->name }}{{ !$loop->last ? '、' : '' }}
                    @endforeach
                </p>
                <p>商品の状態：{{ $item->condition ? $item->condition->condition : '状態不明' }}</p>
            </div>
            <!-- コメントセクション -->
            <div class="comments">

                <!-- コメント一覧 -->
                <div class="comment-section">
                    <div class="comment-header">
                        <h2>コメント ({{ $item->comments->count() }})</h2>
                    </div>
                    <div class="comment-list">
                        @forelse ($item->comments as $comment)
                        <div class="comment">
                            <p>
                                <img
                                    src="{{ $comment->user->profile->img ? asset('storage/' . $comment->user->profile->img) : asset('images/default-icon.png') }}"
                                    alt="アイコン"
                                    class="user-icon">
                                <strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}
                            </p>
                            <p class="comment-time">{{ $comment->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @empty
                        <p>まだコメントがありません。</p>
                        @endforelse
                    </div>

                </div>
                <h2>商品へのコメント</h2>
                <!-- コメント投稿フォーム -->
                <form action="{{ route('comments.store', ['item' => $item->id]) }}" method="POST">
                    @csrf
                    <textarea name="comment" placeholder="コメントを入力してください" required></textarea>
                    <button type="submit">コメントを投稿</button>
                </form>

            </div>
        </div>
    </div>
</main>

<footer class="footer">
    © 2025 COACHTECH
</footer>
</body>

</html>

@endsection