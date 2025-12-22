<?php

namespace App\Policies;

use App\Models\User;

class ChatPolicy
{
    /**
     * Determine if the user can send a message to another user
     */
    public function sendMessage(User $user, User $recipient): bool
    {
        return $user->canChatWith($recipient);
    }

    /**
     * Determine if the user can view conversation with another user
     */
    public function viewConversation(User $user, User $otherUser): bool
    {
        return $user->canChatWith($otherUser);
    }

    /**
     * Determine if the user can access the chat system
     */
    public function accessChat(User $user): bool
    {
        // All authenticated users can access chat
        return true;
    }
}
