<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Profile;

class AddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 住所変更後に購入画面に正しく反映される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストビル101'
        ]);

        $this->actingAs($user)->post(route('address.update'), [
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市',
            'building' => 'テストマンション202'
        ])->assertRedirect();

        $this->get(route('purchase', ['id' => $item->id]))
            ->assertSee('987-6543')
            ->assertSee('大阪府大阪市')
            ->assertSee('テストマンション202');
    }

    /** @test */
    public function 購入時に住所が正しく紐づく()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '111-2222',
            'address' => '北海道札幌市',
            'building' => 'サンプルハウス303'
        ]);

        $response = $this->actingAs($user)->post(route('purchase.store'), [
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'postal_code' => '111-2222',
            'address' => '北海道札幌市',
            'building' => 'サンプルハウス303'
        ]);
    }
}
