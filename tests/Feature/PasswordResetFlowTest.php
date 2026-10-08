<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Alice Walker',
            'email' => 'alice@example.com',
            'phone' => '+12025550177',
            'password' => Hash::make('oldpassword123'),
            'role' => 'user'
        ]);
    }

    public function test_forgot_password_api_dispatches_notification_with_blade_template()
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'alice@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        Notification::assertSentTo(
            $this->user,
            ResetPasswordNotification::class,
            function ($notification) {
                return ! empty($notification->token);
            }
        );
    }

    public function test_reset_password_email_blade_view_renders_properly()
    {
        $token = 'test-token-123456';
        $notification = new ResetPasswordNotification($token);
        $mail = $notification->toMail($this->user);

        $this->assertStringContainsString('Reset Your Password', $mail->subject);
        $this->assertEquals('emails.reset-password', $mail->view);
        $this->assertArrayHasKey('resetUrl', $mail->viewData);
        $this->assertArrayHasKey('token', $mail->viewData);
        $this->assertEquals($token, $mail->viewData['token']);

        // Render blade view
        $html = view('emails.reset-password', $mail->viewData)->render();
        $this->assertStringContainsString('Olivia Supermarket', $html);
        $this->assertStringContainsString('Alice Walker', $html);
        $this->assertStringContainsString($token, $html);
        $this->assertStringContainsString('Reset Password', $html);
    }

    public function test_validate_reset_token_api()
    {
        $token = Password::createToken($this->user);

        // 1. Valid Token
        $validRes = $this->postJson('/api/v1/auth/validate-reset-token', [
            'email' => 'alice@example.com',
            'token' => $token
        ]);

        $validRes->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'email' => 'alice@example.com',
            ]);

        // 2. Invalid Token
        $invalidRes = $this->postJson('/api/v1/auth/validate-reset-token', [
            'email' => 'alice@example.com',
            'token' => 'invalid-token-xyz'
        ]);

        $invalidRes->assertStatus(400)
            ->assertJson([
                'valid' => false,
            ]);

        // 3. Non-existent User
        $userNotFoundRes = $this->postJson('/api/v1/auth/validate-reset-token', [
            'email' => 'nonexistent@example.com',
            'token' => $token
        ]);

        $userNotFoundRes->assertStatus(404)
            ->assertJson([
                'valid' => false,
            ]);
    }

    public function test_reset_password_api_completes_password_change()
    {
        $token = Password::createToken($this->user);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'alice@example.com',
            'password' => 'NewSecurePassword123',
            'password_confirmation' => 'NewSecurePassword123'
        ]);

        $response->assertStatus(200);

        // Verify that user can login with new password
        $this->user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123', $this->user->password));
    }
}
