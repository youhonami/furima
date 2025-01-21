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
        <div class="product-list">
            <div class="product">
                <img src="{{ asset('storage/images/product-placeholder.png') }}" alt="商品画像">
                <p>商品名</p>
            </div>
            <!-- ここに商品データをループで追加 -->
        </div>
    </section>
</main>
</body>

</html>

@endsection