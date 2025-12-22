<?php

/**
 * Example Code Snippets for Role-Based Chat Integration
 * 
 * This file provides ready-to-use code examples for integrating
 * the role-based chat system into your application.
 */

namespace App\Examples;

use App\Models\User;
use App\Models\Umkm;
use Illuminate\Support\Facades\Hash;

class ChatExamples
{
    /**
     * Example 1: Create a regular user and assign to UMKM
     */
    public static function createUserWithUmkm()
    {
        $user = User::create([
            'name' => 'Customer Name',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
            'umkm_id' => 1, // Assign to UMKM with ID 1
        ]);

        return $user;
    }

    /**
     * Example 2: Create admin_toko with their UMKM
     */
    public static function createAdminTokoWithUmkm()
    {
        // First create the user
        $adminUser = User::create([
            'name' => 'Store Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN_TOKO,
        ]);

        // Then create their UMKM
        $umkm = Umkm::create([
            'owner_user_id' => $adminUser->id,
            'name' => 'My Store',
            'description' => 'Store description',
            'phone' => '08123456789',
            'address' => 'Store address',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => Umkm::STATUS_APPROVED,
        ]);

        return $adminUser;
    }

    /**
     * Example 3: Check if user can chat with another user
     */
    public static function checkChatPermission($userId, $targetUserId)
    {
        $user = User::find($userId);
        $targetUser = User::find($targetUserId);

        if (!$user || !$targetUser) {
            return false;
        }

        return $user->canChatWith($targetUser);
    }

    /**
     * Example 4: Get all users current user can chat with
     */
    public static function getAllowedContacts($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return collect();
        }

        return $user->getAllowedChatUsers();
    }
}
