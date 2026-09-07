<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_google_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Masuk dengan Akun Google')
            ->assertSee(route('google.login'), false);
    }

    public function test_legacy_login_url_only_displays_the_google_login_screen(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk dengan Akun Google');
    }

    public function test_unavailable_password_and_profile_endpoints_cannot_be_accessed(): void
    {
        foreach (['/register', '/forgot-password', '/profile'] as $url) {
            $this->get($url)->assertNotFound();
        }

        $this->post('/login', ['email' => 'mahasiswa@stis.ac.id', 'password' => 'password'])
            ->assertStatus(405);
        $this->post('/register')->assertNotFound();
        $this->patch('/profile')->assertNotFound();
        $this->delete('/profile')->assertNotFound();
    }
}
