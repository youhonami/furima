<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use Database\Seeders\ConditionsTableSeeder;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionsTableSeeder::class);
    }

    /** @test */
    public function 商品詳細ページに必要な情報が表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id, 'condition_id' => 1]);
        $category = Category::factory()->create();
        $item->categories()->attach($category->id);
        $comment = Comment::factory()->create(['item_id' => $item->id, 'user_id' => $user->id]);

        $response = $this->get(route('item.show', $item->id));

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee($category->name);
        $response->assertSee($comment->content);
        $response->assertSee($user->name);
    }

    /** @test */
    public function 複数選択されたカテゴリが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id, 'condition_id' => 1]);
        $categories = Category::factory()->count(3)->create();
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('item.show', $item->id));

        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
