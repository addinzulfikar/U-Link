<?php

namespace Database\Seeders;

use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ChatifyDemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = 'password';

        $superAdmin = User::query()->firstOrCreate(
            ['email' => 'superadmin@u-link.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );
        if ($superAdmin->role !== User::ROLE_SUPER_ADMIN) {
            $superAdmin->forceFill(['role' => User::ROLE_SUPER_ADMIN])->save();
        }

        $adminToko = User::query()->firstOrCreate(
            ['email' => 'admintoko@u-link.test'],
            [
                'name' => 'Admin Toko',
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN_TOKO,
            ]
        );
        if ($adminToko->role !== User::ROLE_ADMIN_TOKO) {
            $adminToko->forceFill(['role' => User::ROLE_ADMIN_TOKO])->save();
        }

        $umkm = Umkm::query()->firstOrCreate(
            ['owner_user_id' => $adminToko->id],
            [
                'name' => 'Demo UMKM',
                'slug' => Str::slug('Demo UMKM'),
                'description' => 'Data demo untuk Chatify.',
                'status' => Umkm::STATUS_APPROVED,
            ]
        );
        if ($umkm->status !== Umkm::STATUS_APPROVED) {
            $umkm->forceFill(['status' => Umkm::STATUS_APPROVED])->save();
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'user@u-link.test'],
            [
                'name' => 'Regular User',
                'password' => Hash::make($password),
                'role' => User::ROLE_USER,
                'umkm_id' => $umkm->id,
            ]
        );
        if ($user->role !== User::ROLE_USER || $user->umkm_id !== $umkm->id) {
            $user->forceFill(['role' => User::ROLE_USER, 'umkm_id' => $umkm->id])->save();
        }

        $this->command?->info('Chatify demo users created/ensured:');
        $this->command?->info(' - superadmin@u-link.test / password (role=super_admin)');
        $this->command?->info(' - admintoko@u-link.test / password (role=admin_toko)');
        $this->command?->info(' - user@u-link.test / password (role=user, umkm_id='.$umkm->id.')');
    }
}
