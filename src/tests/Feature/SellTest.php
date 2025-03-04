<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 商品の出品が成功することを確認
     */
    public function test_商品出品が成功する()
    {
        // ストレージをモック
        Storage::fake('public');

        // ユーザー作成
        $user = User::factory()->create();

        // カテゴリー・商品の状態を作成
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();

        // 画像ファイルを作成
        $file = UploadedFile::fake()->image('product.jpg');

        // フォーム送信
        $response = $this->actingAs($user)->post(route('sell.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド', // ✅ brand を明示的に指定
            'description' => 'これはテスト用の商品です。',
            'price' => 5000,
            'condition_id' => $condition->id,
        ]);


        // データベースに保存されていることを確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'price' => 5000,
            'condition_id' => $condition->id,
        ]);


        // 画像が正しくアップロードされていることを確認
        Storage::disk('public')->assertExists('product_images/' . $file->hashName());

        // リダイレクトを確認
        $response->assertRedirect(route('sell.complete'));
    }

    /**
     * @test
     * 必須項目が未入力の場合、バリデーションエラーになることを確認
     */
    public function test_必須項目が未入力の場合_バリデーションエラーになる()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // フォーム送信（すべて未入力）
        $response = $this->actingAs($user)->post(route('sell.store'), []);

        // バリデーションエラーを確認
        $response->assertSessionHasErrors([
            'image',
            'categories',
            'condition',
            'name',
            'description',
            'price'
        ]);
    }

    /**
     * @test
     * 画像がない場合、バリデーションエラーになることを確認
     */
    public function test_画像がない場合_バリデーションエラーになる()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // カテゴリー・商品の状態を作成
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();

        // フォーム送信（画像なし）
        $response = $this->actingAs($user)->post(route('sell.store'), [
            'categories' => [$category->id],
            'condition' => $condition->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品です。',
            'price' => 5000,
        ]);

        // バリデーションエラーを確認
        $response->assertSessionHasErrors(['image']);
    }

    /**
     * @test
     * 販売価格が負の値の場合、バリデーションエラーになることを確認
     */
    public function test_販売価格が負の値の場合_バリデーションエラーになる()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // カテゴリー・商品の状態を作成
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();

        // フォーム送信（負の値）
        $response = $this->actingAs($user)->post(route('sell.store'), [
            'categories' => [$category->id],
            'condition' => $condition->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品です。',
            'price' => -500,
        ]);

        // バリデーションエラーを確認
        $response->assertSessionHasErrors(['price']);
    }
}
