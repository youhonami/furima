<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メールアドレス未入力時のバリデーションメッセージ確認
     */
    public function test_email_required_validation()
    {
        $response = $this->withoutMiddleware()->post('/login', [
            '_token' => csrf_token(),
            'email' => '',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    /**
     * パスワード未入力時のバリデーションメッセージ確認
     */
    public function test_password_required_validation()
    {
        $response = $this->withoutMiddleware()->post('/login', [
            '_token' => csrf_token(),
            'email' => 'test@example.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    /**
     * 誤った情報でログインした場合のエラー確認
     */
    public function test_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->withoutMiddleware()->post('/login', [
            '_token' => csrf_token(),
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }

    /**
     * 正しい情報でログインした場合の動作確認
     */
    public function test_successful_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->withoutMiddleware()->post('/login', [
            '_token' => csrf_token(),
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
