@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<main class="main">

    <!-- タブメニュー -->
    <!-- タブメニュー -->
    <div class="tabs">
        <a href="{{ route('items.index', ['filter' => 'recommended', 'search' => request('search')]) }}"
            class="tabs__item {{ request('filter') !== 'mylist' ? 'tabs__item--active' : '' }}">
            おすすめ
        </a>

        <a href="{{ route('items.index', ['filter' => 'mylist', 'search' => request('search')]) }}"
            class="tabs__item {{ request('filter') === 'mylist' ? 'tabs__item--active' : '' }}">
            マイリスト
        </a>
    </div>

    <!-- 商品一覧 -->
    <div class="item-list">
        @foreach($items as $item)
        @if($item->isSold())
        <!-- 購入済み商品 -->
        <div class="item-list__card item-list__card--sold">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="item-list__image">
            <h2 class="item-list__name">{{ Str::limit($item->name, 20, '...') }}</h2>
            <p class="item-list__sold-label">Sold</p>
        </div>
        @else
        <!-- 購入可能商品 -->
        <a href="{{ route('item.show', $item->id) }}" class="item-list__card">
            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="item-list__image">
            <h2 class="item-list__name">{{ Str::limit($item->name, 20, '...') }}</h2>
        </a>
        @endif
        @endforeach
    </div>

    <!-- 検索結果がない場合のメッセージ -->
    @if($items->isEmpty())
    <p>検索結果が見つかりませんでした。</p>
    @endif
</main>
@endsection