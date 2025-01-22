@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<main>
    <h2>商品の出品</h2>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- 商品画像 -->
        <div class="form-group">
            <label for="image">商品画像</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <!-- 商品の詳細 -->
        <div class="form-group">
            <label for="categories">カテゴリー</label>
            <div id="categories">
                @foreach ($categories as $category)
                <label>
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                    {{ $category->name }}
                </label>
                @endforeach
            </div>
        </div>
        <!-- 商品の状態 -->
        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select id="condition" name="condition">
                <option value="">選択してください</option>
                @foreach ($conditions as $condition)
                <option value="{{ $condition->id }}">{{ $condition->condition }}</option>
                @endforeach
            </select>
        </div>


        <!-- 商品名と説明 -->
        <div class="form-group">
            <label for="name">商品名</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">商品説明</label>
            <textarea id="description" name="description" rows="4" required></textarea>
        </div>

        <!-- 販売価格 -->
        <div class="form-group">
            <label for="price">販売価格</label>
            <input type="number" id="price" name="price" min="0" required>
        </div>

        <button type="submit" class="submit-button">出品する</button>
    </form>
</main>
</body>

</html>

@endsection