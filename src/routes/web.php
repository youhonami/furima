<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AddressController;
use Laravel\Fortify\Fortify;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{id}', [ItemController::class, 'show'])->name('item.show');
Route::get('/mypage', [UserController::class, 'show'])->middleware('auth')->name('mypage');
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});


Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage'); // マイページ表示
Route::get('/mypage/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');


// 商品一覧を表示するためのルート（必要な場合）
Route::get('/sell', [SellController::class, 'index'])->name('sell.index');

// 商品出品ページ（現在の目的）
Route::get('/sell/create', [SellController::class, 'create'])->name('sell.create');
Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

Route::get('/mypage', [UserController::class, 'index'])->name('mypage');

Route::get('/purchase/{id}', [PurchaseController::class, 'index'])->name('purchase');

Route::get('/address/edit', [ProfileController::class, 'editAddress'])->name('address.edit');

Route::get('/address/edit', function () {
    return view('address');
})->name('address.edit');

Route::post('/items/{id}/like', [LikeController::class, 'toggleLike'])->name('like');

Route::get('/items', [ItemController::class, 'index'])->name('item.index');

Route::post('/comments/{item}', [CommentController::class, 'store'])->name('comments.store');

Route::post('/address/update', [AddressController::class, 'updateAddress'])->name('address.update');
Route::post('/purchase/complete', [PurchaseController::class, 'completePurchase'])->name('purchase.complete');

Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.store');


Route::get('/mypage', [UserController::class, 'show'])->middleware('auth')->name('mypage');


// 1) 認証待ちページ
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // ここで「メール認証してください」画面を表示
})->middleware('auth')->name('verification.notice');

// 2) 認証リンクをクリックしたとき
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証を完了
    // 認証完了後のリダイレクト先（例: プロフィール編集画面）
    return redirect()->route('profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3) 認証メール再送
Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
