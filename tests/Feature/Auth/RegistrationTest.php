<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_screen_is_disabled(): void
    {
        $this->get('/register')
            ->assertNotFound();
    }

    public function test_public_registration_endpoint_is_disabled(): void
    {
        $response = $this->post('/register', [
            'name' => 'Usuario no autorizado',
            'email' => 'registro@microseed.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNotFound();

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'registro@microseed.test',
        ]);
    }
}
