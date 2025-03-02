<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品一覧ページで全商品が表示される()
    {
        Item::factory()->count(3)->create();

        $response = $this->get(route('item.index'));

        $response->assertStatus(200);
        $response->assertViewHas('items');
        $this->assertEquals(3, $response->viewData('items')->count());
    }

    /** @test */
    public function 購入済み商品には_Sold_のラベルが表示される()
    {
        $soldItem = Item::factory()->create();
        Purchase::factory()->create(['item_id' => $soldItem->id]); // 購入記録を追加

        $availableItem = Item::factory()->create();

        $response = $this->get(route('item.index'));

        $response->assertSee('Sold'); // 購入済み商品が「Sold」と表示されるか確認
        $response->assertSee($availableItem->name);
    }

    /** @test */
    public function ログインユーザーは自分が出品した商品を一覧に表示しない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownItem = Item::factory()->create(['user_id' => $user->id]);
        $otherItem = Item::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $response = $this->get(route('item.index'));

        $response->assertDontSee($ownItem->name);
        $response->assertSee($otherItem->name);
    }


    /** @test */
    public function 検索結果がない場合_メッセージが表示される()
    {
        $response = $this->get(route('item.index', ['search' => 'notfound']));

        $response->assertStatus(200);
        $response->assertSee('検索結果が見つかりませんでした。');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
    }
}
