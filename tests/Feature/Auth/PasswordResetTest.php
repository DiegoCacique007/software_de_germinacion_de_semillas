<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200)
            ->assertSee('name="_token"', false)
            ->assertDontSee('name="password"', false)
            ->assertDontSee('name="password_confirmation"', false);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $originalHash = $user->password;

        // El antiguo payload vulnerable tampoco debe poder cambiar la contraseña.
        $this->post('/forgot-password', [
            'email' => $user->email,
            'password' => 'attacker-password',
            'password_confirmation' => 'attacker-password',
        ])->assertSessionHasNoErrors()->assertSessionHas('status');

        $this->assertSame($originalHash, $user->fresh()->password);
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $stored = DB::table('password_reset_tokens')->where('email', $user->email)->first();
            $this->assertNotNull($stored);
            $this->assertNotSame($notification->token, $stored->token);
            $this->assertTrue(Hash::check($notification->token, $stored->token));
            $mail = $notification->toMail($user);
            $this->assertSame(route('password.reset', [
                'token' => $notification->token,
                'email' => $user->email,
            ]), $mail->actionUrl);

            return true;
        });
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);
            $response->assertSee('name="token"', false)->assertSee('name="_token"', false);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_invalid_expired_and_reused_tokens_cannot_change_password(): void
    {
        $user = User::factory()->create();
        $originalHash = $user->password;
        $token = Password::createToken($user);

        $this->post('/reset-password', $this->resetPayload($user, 'invalid-token'))
            ->assertSessionHasErrors('email');
        $this->assertSame($originalHash, $user->fresh()->password);

        $this->travel(config('auth.passwords.users.expire') + 1)->minutes();
        $this->post('/reset-password', $this->resetPayload($user, $token))
            ->assertSessionHasErrors('email');
        $this->assertSame($originalHash, $user->fresh()->password);
        $this->travelBack();

        $this->post('/reset-password', $this->resetPayload($user, $token))
            ->assertSessionHasNoErrors()->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

        $newHash = $user->fresh()->password;
        $this->post('/reset-password', $this->resetPayload($user, $token, 'another-password'))
            ->assertSessionHasErrors('email');
        $this->assertSame($newHash, $user->fresh()->password);
    }

    public function test_reset_requires_matching_email_and_valid_fields_without_flashing_secrets(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $originalHash = $user->password;
        $otherHash = $other->password;
        $token = Password::createToken($user);

        $this->post('/reset-password', $this->resetPayload($other, $token))
            ->assertSessionHasErrors('email');
        $this->assertSame($otherHash, $other->fresh()->password);

        foreach ([
            ['token' => ''],
            ['token' => ['invalid']],
            ['email' => 'invalid'],
            ['password' => 'short', 'password_confirmation' => 'short'],
            ['password_confirmation' => 'different'],
            ['password_confirmation' => ''],
        ] as $invalid) {
            $this->post('/reset-password', array_replace($this->resetPayload($user, $token), $invalid))
                ->assertSessionHasErrors()
                ->assertSessionMissing('_old_input.token')
                ->assertSessionMissing('_old_input.password')
                ->assertSessionMissing('_old_input.password_confirmation');
            $this->assertSame($originalHash, $user->fresh()->password);
            $this->assertTrue(Password::tokenExists($user, $token));
        }
    }

    public function test_request_is_generic_and_broker_throttles_repeated_emails(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $originalHash = $user->password;

        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();
        $this->assertSame($originalHash, $user->fresh()->password);
        $message = session('status');
        $stored = DB::table('password_reset_tokens')->where('email', $user->email)->value('token');
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status', $message);
        $this->assertSame($stored, DB::table('password_reset_tokens')->where('email', $user->email)->value('token'));
        Notification::assertSentToTimes($user, ResetPassword::class, 1);

        $this->post('/forgot-password', ['email' => 'missing@example.test'])
            ->assertSessionHasNoErrors()->assertSessionHas('status', $message);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'missing@example.test']);
        $this->post('/forgot-password', ['email' => 'invalid'])->assertSessionHasErrors('email');
        $this->post('/forgot-password', [])->assertSessionHasErrors('email');
    }

    public function test_only_new_password_can_log_in_after_reset(): void
    {
        $role = Role::create(['clave' => 'encargado', 'nombre' => 'Encargado', 'activo' => true]);
        $user = User::factory()->create(['role_id' => $role->id, 'activo' => true]);
        $rememberToken = $user->remember_token;
        $token = Password::createToken($user);

        $this->post('/reset-password', $this->resetPayload($user, $token))
            ->assertRedirect(route('login'))->assertSessionHasNoErrors();
        $this->assertNotSame($rememberToken, $user->fresh()->remember_token);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'new-password'])
            ->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    private function resetPayload(User $user, string $token, string $password = 'new-password'): array
    {
        return [
            'email' => $user->email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ];
    }
}
