<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_upload_a_csv_file()
    {
        $adminUser = AdminUser::factory()->create([
            'user' => 'admin',
            'password' => Hash::make('password')
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'user' => 'admin',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        $file = UploadedFile::fake()->create('test.csv', 100, 'text/csv');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload', [
                'file' => $file,
            ]);

        $response->assertJson([
            'success' => true,
            'message' => 'OK',
        ]);
    }

    /** @test */
    public function it_requires_a_valid_csv_file()
    {
        $adminUser = AdminUser::factory()->create([
            'user' => 'admin',
            'password' => Hash::make('password')
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'user' => 'admin',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        $file = UploadedFile::fake()->create('file.csv', 1000, 'text/csv');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload', [
                'file' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OK'
            ]);
    }
}
