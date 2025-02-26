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
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StripeWebhookController;

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


Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
Route::get('/mypage/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');


// 商品一覧を表示するためのルート
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

// 1) 認証待ちページ
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 認証リンクをクリックしたときの処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3) 認証メール再送
Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // ユーザーの直前のページを確認
    if (session('from_registration')) {
        session()->forget('from_registration');
        return redirect()->route('profile.edit');
    }

    // ログイン時は商品一覧ページへリダイレクト
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [UserController::class, 'show'])->name('mypage');
    Route::get('/sell', [SellController::class, 'index'])->name('sell.index');
    Route::get('/items', [ItemController::class, 'index'])->name('item.index');
});


Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');

Route::get('/purchase/success', function () {
    return view('purchase.success');
})->name('purchase.success');

Route::get('/purchase/cancel', function () {
    return view('purchase.cancel');
})->name('purchase.cancel');

Route::get('/items', [ItemController::class, 'index'])->name('item.index');

Route::get('/purchase/cancel/{id}', [PurchaseController::class, 'cancel'])->name('purchase.cancel');
