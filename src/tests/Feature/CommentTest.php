<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Database\Seeders\ConditionsTableSeeder;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionsTableSeeder::class);
    }

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);

        $this->actingAs($user);

        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'content' => 'これはテストコメントです。'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。'
        ]);
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create(['condition_id' => 1]);

        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'content' => 'これはテストコメントです。'
        ]);

        $response->assertRedirect(route('login'));

        $this->assertGuest();

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);

        $this->actingAs($user);

        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
    }

    /** @test */
    public function コメントが255字以上の場合、バリデーションメッセージが表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition_id' => 1]);
        $longComment = str_repeat('あ', 256);

        $this->actingAs($user);

        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'content' => $longComment
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => $longComment
        ]);
    }
}
