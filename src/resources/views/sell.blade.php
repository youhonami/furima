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
            @error('image')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- カテゴリー -->
        <div class="form-group">
            <label for="categories">カテゴリー</label>
            <div id="categories">
                @foreach ($categories as $category)
                <input type="checkbox" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}"
                    {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                <label for="category-{{ $category->id }}">{{ $category->name }}</label>
                @endforeach
            </div>
            @error('categories')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の状態 -->
        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select id="condition" name="condition">
                <option value="" selected disabled>選択してください</option>
                @foreach ($conditions as $condition)
                <option value="{{ $condition->id }}" {{ old('condition') == $condition->id ? 'selected' : '' }}>
                    {{ $condition->condition }}
                </option>
                @endforeach
            </select>
            @error('condition')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <!-- 商品名 -->
        <div class="form-group">
            <label for="name">商品名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            @error('name')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- ブランド名 -->
        <div class="form-group">
            <label for="brand">ブランド名</label>
            <input type="text" id="brand" name="brand" value="{{ old('brand') }}">
            @error('brand')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品説明 -->
        <div class="form-group">
            <label for="description">商品説明</label>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
            @error('description')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- 販売価格 -->
        <div class="form-group">
            <label for="price">販売価格</label>
            <input type="number" id="price" name="price" min="0" value="{{ old('price') }}">
            @error('price')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="submit-button">出品する</button>
    </form>
</main>
</body>

</html>

@endsection