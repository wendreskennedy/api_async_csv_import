<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\ImportStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ImportStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_import_status()
    {
        $adminUser = AdminUser::factory()->create([
            'user' => 'admin',
            'password' => Hash::make('password')
        ]);

        $importStatus = ImportStatus::factory()->create();

        $response = $this->postJson('/api/login', [
            'user' => 'admin',
            'password' => 'password'
        ]);

        $token = $response->json('token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/import-status/{$importStatus->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Import Status',
                'data' => [
                    'id' => $importStatus->id,
                    'status' => $importStatus->status,
                ]
            ]);
    }
}
