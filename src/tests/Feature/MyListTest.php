<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
    }

    /** @test */
    public function いいねした商品だけが表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $likedItem = Item::factory()->create();
        $otherItem = Item::factory()->create();

        $user->likes()->attach($likedItem->id);

        $response = $this->get(route('item.index', ['filter' => 'mylist']));

        $response->assertSee($likedItem->name);
        $response->assertDontSee($otherItem->name);
    }

    /** @test */
    public function 購入済み商品には_Sold_のラベルが表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $soldItem = Item::factory()->create();
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $response = $this->get(route('item.index', ['filter' => 'mylist']));

        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownItem = Item::factory()->create(['user_id' => $user->id]);
        $otherItem = Item::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $response = $this->get(route('item.index', ['filter' => 'mylist']));

        $response->assertDontSee($ownItem->name);
        $response->assertSee($otherItem->name);
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $response = $this->get(route('item.index', ['filter' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee('検索結果が見つかりませんでした。');
    }
}
