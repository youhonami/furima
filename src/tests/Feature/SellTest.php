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

    /** @test */
    public function 商品出品が成功する()
    {

        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        $file = UploadedFile::fake()->image('product.jpg');
        /** @var User $user */
        $response = $this->actingAs($user)->post(route('sell.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品です。',
            'price' => 5000,
            'condition_id' => $condition->id,
        ]);

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'price' => 5000,
            'condition_id' => $condition->id,
        ]);

        $this->assertDatabaseMissing('items', ['brand' => 'テストブランド']);
        Storage::disk('public')->assertExists('product_images/' . $file->hashName());
        $response->assertRedirect(route('sell.complete'));
    }

    /** @test */
    public function 必須項目が未入力の場合_バリデーションエラーになる()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('sell.store'), []);

        $response->assertSessionHasErrors([
            'image',
            'categories',
            'condition',
            'name',
            'description',
            'price'
        ]);
    }

    /** @test */
    public function 画像がない場合_バリデーションエラーになる()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        /** @var User $user */
        $response = $this->actingAs($user)->post(route('sell.store'), [
            'categories' => [$category->id],
            'condition' => $condition->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品です。',
            'price' => 5000,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function 販売価格が負の値の場合_バリデーションエラーになる()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        /** @var User $user */
        $response = $this->actingAs($user)->post(route('sell.store'), [
            'categories' => [$category->id],
            'condition' => $condition->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品です。',
            'price' => -500,
        ]);

        $response->assertSessionHasErrors(['price']);
    }
}
