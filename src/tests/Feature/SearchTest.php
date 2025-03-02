<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ConditionsTableSeeder;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionsTableSeeder::class);
    }

    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        Item::factory()->create(['name' => 'MacBook Pro', 'condition_id' => 1]);
        Item::factory()->create(['name' => 'MacBook Air', 'condition_id' => 1]);
        Item::factory()->create(['name' => 'iPad Pro', 'condition_id' => 1]);

        $response = $this->get(route('item.index', ['search' => 'MacBook']));

        $response->assertSee('MacBook Pro');
        $response->assertSee('MacBook Air');
        $response->assertDontSee('iPad Pro');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $item1 = Item::factory()->create(['name' => 'MacBook Pro', 'condition_id' => 1]);
        $item2 = Item::factory()->create(['name' => 'MacBook Air', 'condition_id' => 1]);

        // `likedItems()` が存在することを確認
        if (method_exists($user, 'likedItems')) {
            $user->likedItems()->attach($item1->id);
        } else {
            $this->fail('User model is missing the likedItems() relationship.');
        }

        $response = $this->get(route('item.index', ['search' => 'MacBook']));
        $response = $this->get(route('item.index', ['filter' => 'mylist', 'search' => 'MacBook']));

        $response->assertSee('value="MacBook"', false);
        $response->assertSee($item1->name);
        $response->assertDontSee($item2->name);
    }
}
