<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_ユーザー情報が取得できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('mypage'));

        $response->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee($profile->postal_code)
            ->assertSee($profile->address)
            ->assertSee($item->name);
    }

    /** @test */
    public function test_ユーザー情報変更画面の初期値が正しく設定されている()
    {
        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'テストマンション101'
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200)
            ->assertSee('123-4567')
            ->assertSee('東京都新宿区')
            ->assertSee('テストマンション101');
    }

    /** @test */
    public function test_ユーザーがプロフィールを更新できる()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->image('profile.jpg');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => '更新されたユーザー',
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市',
            'building' => 'テストマンション202',
            'profile_image' => $file,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新されたユーザー',
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市',
            'building' => 'テストマンション202',
        ]);

        Storage::disk('public')->assertExists('profile_images/' . $file->hashName());

        $response->assertRedirect(route('profile.edit'));
    }
}
