@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<main class="sell__container">
    <h2 class="sell__title">商品の出品</h2>
    <form action="{{ route('sell.store') }}" method="POST" class="sell__form" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像 -->
        <div class="sell__group">
            <label for="image" class="sell__label">商品画像</label>

            <!--  プレビュー用の枠-->
            <div class="sell__image-preview">
                <img id="image-preview" src="" alt="商品プレビュー" style="display: none;">
            </div>

            <input type="file" id="image" name="image" class="sell__input" accept="image/*" onchange="previewImage(event)">
            @error('image')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- カテゴリー -->
        <div class="sell__group">
            <label for="categories" class="sell__label">カテゴリー</label>
            <div class="sell__categories" id="categories">
                @foreach ($categories as $category)
                <input type="checkbox" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                <label for="category-{{ $category->id }}" class="sell__category-label">{{ $category->name }}</label>
                @endforeach
            </div>
            @error('categories')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の状態 -->
        <div class="sell__group">
            <label for="condition" class="sell__label">商品の状態</label>
            <select id="condition" name="condition" class="sell__input">
                <option value="" selected disabled>選択してください</option>
                @foreach ($conditions as $condition)
                <option value="{{ $condition->id }}" {{ old('condition') == $condition->id ? 'selected' : '' }}>
                    {{ $condition->condition }}
                </option>
                @endforeach
            </select>
            @error('condition')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品名 -->
        <div class="sell__group">
            <label for="name" class="sell__label">商品名</label>
            <input type="text" id="name" name="name" class="sell__input" value="{{ old('name') }}">
            @error('name')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- ブランド名 -->
        <div class="sell__group">
            <label for="brand" class="sell__label">ブランド名</label>
            <input type="text" id="brand" name="brand" class="sell__input" value="{{ old('brand') }}">
            @error('brand')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品説明 -->
        <div class="sell__group">
            <label for="description" class="sell__label">商品説明</label>
            <textarea id="description" name="description" class="sell__input sell__textarea" rows="4">{{ old('description') }}</textarea>
            @error('description')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 販売価格 -->
        <div class="sell__group">
            <label for="price" class="sell__label">販売価格</label>
            <input type="number" id="price" name="price" class="sell__input" min="0" value="{{ old('price') }}">
            @error('price')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="sell__submit-button">出品する</button>
    </form>
</main>

<!--  プレビュー用JavaScript -->
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = "block"; // 画像を表示する
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection