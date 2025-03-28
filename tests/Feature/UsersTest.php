<?php
// tests/Feature/UserTest.php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_a_list_of_users()
    {
        $admin = AdminUser::factory()->create([
            'user' => 'admin',
            'password' => Hash::make('password')
        ]);

        $users = User::factory()->count(5)->create();

        $loginResponse = $this->postJson('/api/login', [
            'user' => 'admin',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }
}
