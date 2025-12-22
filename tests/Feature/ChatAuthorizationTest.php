<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper method to create an UMKM with an admin
     */
    private function createUmkmWithAdmin(): array
    {
        $adminToko = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);
        $umkm = Umkm::factory()->create([
            'owner_user_id' => $adminToko->id,
            'status' => Umkm::STATUS_APPROVED,
        ]);

        return ['admin' => $adminToko, 'umkm' => $umkm];
    }

    /**
     * Test that super admin can chat with everyone
     */
    public function test_super_admin_can_chat_with_all_users(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $adminToko = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);
        $anotherSuperAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->assertTrue($superAdmin->canChatWith($user));
        $this->assertTrue($superAdmin->canChatWith($adminToko));
        $this->assertTrue($superAdmin->canChatWith($anotherSuperAdmin));
    }

    /**
     * Test that all users can chat with super admin
     */
    public function test_all_users_can_chat_with_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $adminToko = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);

        $this->assertTrue($user->canChatWith($superAdmin));
        $this->assertTrue($adminToko->canChatWith($superAdmin));
    }

    /**
     * Test that regular user can chat with their UMKM's admin
     */
    public function test_user_can_chat_with_their_umkm_admin(): void
    {
        // Create an UMKM with an admin
        $data = $this->createUmkmWithAdmin();
        $adminToko = $data['admin'];
        $umkm = $data['umkm'];

        // Create a user assigned to this UMKM
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm->id,
        ]);

        $this->assertTrue($user->canChatWith($adminToko));
    }

    /**
     * Test that regular user cannot chat with other UMKM's admin
     */
    public function test_user_cannot_chat_with_other_umkm_admin(): void
    {
        // Create UMKM 1 with admin
        $data1 = $this->createUmkmWithAdmin();
        $umkm1 = $data1['umkm'];

        // Create UMKM 2 with admin
        $data2 = $this->createUmkmWithAdmin();
        $adminToko2 = $data2['admin'];

        // Create a user assigned to UMKM 1
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm1->id,
        ]);

        // User should NOT be able to chat with admin of UMKM 2
        $this->assertFalse($user->canChatWith($adminToko2));
    }

    /**
     * Test that regular users cannot chat with each other
     */
    public function test_users_cannot_chat_with_each_other(): void
    {
        $user1 = User::factory()->create(['role' => User::ROLE_USER]);
        $user2 = User::factory()->create(['role' => User::ROLE_USER]);

        $this->assertFalse($user1->canChatWith($user2));
        $this->assertFalse($user2->canChatWith($user1));
    }

    /**
     * Test that admin toko can chat with their UMKM's users
     */
    public function test_admin_toko_can_chat_with_their_umkm_users(): void
    {
        // Create an UMKM with an admin
        $data = $this->createUmkmWithAdmin();
        $adminToko = $data['admin'];
        $umkm = $data['umkm'];

        // Create users assigned to this UMKM
        $user1 = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm->id,
        ]);
        $user2 = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm->id,
        ]);

        $this->assertTrue($adminToko->canChatWith($user1));
        $this->assertTrue($adminToko->canChatWith($user2));
    }

    /**
     * Test that admin toko cannot chat with users from other UMKMs
     */
    public function test_admin_toko_cannot_chat_with_other_umkm_users(): void
    {
        // Create UMKM 1 with admin
        $data1 = $this->createUmkmWithAdmin();
        $adminToko1 = $data1['admin'];

        // Create UMKM 2 with admin
        $data2 = $this->createUmkmWithAdmin();
        $umkm2 = $data2['umkm'];

        // Create a user assigned to UMKM 2
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm2->id,
        ]);

        // Admin of UMKM 1 should NOT be able to chat with user of UMKM 2
        $this->assertFalse($adminToko1->canChatWith($user));
    }

    /**
     * Test that admin toko cannot chat with other admin toko
     */
    public function test_admin_toko_cannot_chat_with_other_admin_toko(): void
    {
        $adminToko1 = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);
        $adminToko2 = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);

        $this->assertFalse($adminToko1->canChatWith($adminToko2));
        $this->assertFalse($adminToko2->canChatWith($adminToko1));
    }

    /**
     * Test that user without umkm_id cannot see any contacts except super admins
     */
    public function test_user_without_umkm_id_can_only_see_super_admins(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => null,
        ]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $adminToko = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);

        $allowedUsers = $user->getAllowedChatUsers();

        // Should only contain super admin
        $this->assertCount(1, $allowedUsers);
        $this->assertTrue($allowedUsers->contains($superAdmin));
        $this->assertFalse($allowedUsers->contains($adminToko));
    }

    /**
     * Test that admin toko without UMKM can only see super admins
     */
    public function test_admin_toko_without_umkm_can_only_see_super_admins(): void
    {
        $adminToko = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $allowedUsers = $adminToko->getAllowedChatUsers();

        // Should only contain super admin
        $this->assertCount(1, $allowedUsers);
        $this->assertTrue($allowedUsers->contains($superAdmin));
        $this->assertFalse($allowedUsers->contains($user));
    }

    /**
     * Test getAllowedChatUsers returns correct users for regular user
     */
    public function test_get_allowed_chat_users_for_regular_user(): void
    {
        // Create an UMKM with an admin
        $data = $this->createUmkmWithAdmin();
        $adminToko = $data['admin'];
        $umkm = $data['umkm'];

        // Create a user assigned to this UMKM
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm->id,
        ]);

        // Create a super admin
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // Create another admin and user that should NOT be visible
        $otherAdminToko = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);

        $allowedUsers = $user->getAllowedChatUsers();

        // Should contain their admin and super admin only
        $this->assertCount(2, $allowedUsers);
        $this->assertTrue($allowedUsers->contains($adminToko));
        $this->assertTrue($allowedUsers->contains($superAdmin));
        $this->assertFalse($allowedUsers->contains($otherAdminToko));
        $this->assertFalse($allowedUsers->contains($otherUser));
    }

    /**
     * Test getAllowedChatUsers returns correct users for admin toko
     */
    public function test_get_allowed_chat_users_for_admin_toko(): void
    {
        // Create an UMKM with an admin
        $data = $this->createUmkmWithAdmin();
        $adminToko = $data['admin'];
        $umkm = $data['umkm'];

        // Create users assigned to this UMKM
        $user1 = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm->id,
        ]);
        $user2 = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm->id,
        ]);

        // Create a super admin
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // Create another admin and user that should NOT be visible
        $otherAdminToko = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);

        $allowedUsers = $adminToko->getAllowedChatUsers();

        // Should contain their users and super admin
        $this->assertCount(3, $allowedUsers);
        $this->assertTrue($allowedUsers->contains($user1));
        $this->assertTrue($allowedUsers->contains($user2));
        $this->assertTrue($allowedUsers->contains($superAdmin));
        $this->assertFalse($allowedUsers->contains($otherAdminToko));
        $this->assertFalse($allowedUsers->contains($otherUser));
    }

    /**
     * Test getAllowedChatUsers returns all users for super admin
     */
    public function test_get_allowed_chat_users_for_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user1 = User::factory()->create(['role' => User::ROLE_USER]);
        $user2 = User::factory()->create(['role' => User::ROLE_USER]);
        $adminToko1 = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);
        $adminToko2 = User::factory()->create(['role' => User::ROLE_ADMIN_TOKO]);

        $allowedUsers = $superAdmin->getAllowedChatUsers();

        // Should contain all users except self
        $this->assertCount(4, $allowedUsers);
        $this->assertTrue($allowedUsers->contains($user1));
        $this->assertTrue($allowedUsers->contains($user2));
        $this->assertTrue($allowedUsers->contains($adminToko1));
        $this->assertTrue($allowedUsers->contains($adminToko2));
        $this->assertFalse($allowedUsers->contains($superAdmin));
    }

    /**
     * Test that chat relationship is bidirectional for allowed users
     */
    public function test_chat_authorization_is_bidirectional(): void
    {
        // Create an UMKM with an admin
        $data = $this->createUmkmWithAdmin();
        $adminToko = $data['admin'];
        $umkm = $data['umkm'];

        // Create a user assigned to this UMKM
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'umkm_id' => $umkm->id,
        ]);

        // Both should be able to chat with each other
        $this->assertTrue($user->canChatWith($adminToko));
        $this->assertTrue($adminToko->canChatWith($user));
    }
}
