<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Database\Seeders\ConditionsTableSeeder;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionsTableSeeder::class);
    }

    /** @test */
    public function ユーザーは購入を完了できる()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);

        $this->actingAs($user);

        $response = $this->post(route('purchase.store'), [
            'item_id' => $item->id,
            'payment_method' => 'credit_card',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区道玄坂1-1-1',
        ]);

        $response->assertRedirect(route('purchase.success'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'credit_card',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区道玄坂1-1-1',
        ]);
    }

    /** @test */
    public function 購入した商品は商品一覧に「sold」と表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'credit_card',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区道玄坂1-1-1',
        ]);

        $item->update(['is_sold' => true]);

        $this->actingAs($user);

        $response = $this->get(route('item.index'));
        $response->assertSee('sold');
    }

    /** @test */
    public function 購入した商品がプロフィールの購入履歴に追加される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'credit_card',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区道玄坂1-1-1',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage'));
        $response->assertSee(htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8'));
    }
}
