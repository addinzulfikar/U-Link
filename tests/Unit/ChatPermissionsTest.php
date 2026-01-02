<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class ChatPermissionsTest extends TestCase
{
    public function test_admin_toko_can_chat_with_other_admin_toko(): void
    {
        $a = new User(['role' => User::ROLE_ADMIN_TOKO]);
        $a->id = 1;

        $b = new User(['role' => User::ROLE_ADMIN_TOKO]);
        $b->id = 2;

        $this->assertTrue($a->canChatWith($b));
        $this->assertTrue($b->canChatWith($a));
    }

    public function test_admin_toko_cannot_chat_with_self(): void
    {
        $a = new User(['role' => User::ROLE_ADMIN_TOKO]);
        $a->id = 1;

        $this->assertFalse($a->canChatWith($a));
    }
}
