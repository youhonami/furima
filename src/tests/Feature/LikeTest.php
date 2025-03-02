<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ConditionsTableSeeder;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionsTableSeeder::class);
    }

    /** @test */
    public function いいねアイコンを押すといいねした商品として登録される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);

        $this->actingAs($user);

        $response = $this->post(route('like', $item->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('item_user_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function いいね済みのアイコンの色が変化する()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);


        $user->likedItems()->attach($item->id);
        $this->actingAs($user);

        $response = $this->get(route('item.show', $item->id));

        $response->assertSee('liked');
    }

    /** @test */
    public function いいねを解除するといいねが取り消される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);

        $user->likedItems()->attach($item->id);
        $this->actingAs($user);

        $response = $this->post(route('like', $item->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('item_user_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
