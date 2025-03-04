<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Condition;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test 支払い方法を選択できる */
    public function 支払い方法を選択できる()
    {

        $condition = Condition::factory()->create();
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'price' => 1000,
            'condition_id' => $condition->id,
        ]);
        /** @var User $user */
        $this->actingAs($user)
            ->withSession(['current_item_id' => $item->id]);

        $response = $this->post(route('purchase.store'), [
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'payment_method' => 'credit_card',
        ]);
    }
}
