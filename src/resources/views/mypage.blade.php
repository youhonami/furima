@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')

<main class="mypage-container">
    <section class="profile">
        <div class="user-icon">
            <!-- プロフィール画像を表示 -->
            <img src="{{ $user->profile && $user->profile->img ? asset('storage/' . $user->profile->img) : asset('storage/images/default-user-icon.png') }}" alt="ユーザーアイコン">
        </div>
        <div class="user-info">
            <h1>{{ $user->name }}</h1>
            <a href="{{ route('profile.edit') }}" class="edit-profile">プロフィールを編集</a>

        </div>
    </section>

    <section class="products">
        <h2>出品した商品</h2>
        <div class="item-grid">
            @foreach ($items as $item)
            <a href="{{ route('item.show', $item->id) }}" class="item-card">
                <img src="{{ $item->img ? asset('storage/' . $item->img) : asset('storage/images/product-placeholder.png') }}" alt="商品画像" class="item-image">
                <h2 class="item-name">{{ $item->name }}</h2>
            </a>
            @endforeach

            @if ($items->isEmpty())
            <p>出品した商品はありません。</p>
            @endif
        </div>
    </section>
</main>
</body>

</html>

@endsection