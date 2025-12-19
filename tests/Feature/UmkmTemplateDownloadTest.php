<?php

namespace Tests\Feature;

use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmTemplateDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_toko_can_download_template_with_umkm(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN_TOKO,
        ]);

        $umkm = Umkm::factory()->create([
            'owner_user_id' => $user->id,
            'name' => 'Toko Test',
            'address' => 'Jl. Test No. 123',
            'city' => 'Jakarta',
            'phone' => '081234567890',
            'status' => Umkm::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($user)->get('/umkm/download-template');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('Template_Produk_', $response->headers->get('content-disposition'));
    }

    public function test_admin_toko_without_umkm_cannot_download_template(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN_TOKO,
        ]);

        $response = $this->actingAs($user)->get('/umkm/download-template');

        $response->assertRedirect('/umkm/create');
        $response->assertSessionHas('error', 'Anda belum memiliki UMKM.');
    }

    public function test_admin_toko_with_pending_umkm_cannot_download_template(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN_TOKO,
        ]);

        $umkm = Umkm::factory()->create([
            'owner_user_id' => $user->id,
            'status' => Umkm::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->get('/umkm/download-template');

        $response->assertRedirect('/umkm/manage');
        $response->assertSessionHas('error', 'UMKM harus disetujui terlebih dahulu untuk mengunduh template.');
    }

    public function test_regular_user_cannot_download_template(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($user)->get('/umkm/download-template');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }

    public function test_guest_cannot_download_template(): void
    {
        $response = $this->get('/umkm/download-template');

        $response->assertRedirect('/login');
    }
}
