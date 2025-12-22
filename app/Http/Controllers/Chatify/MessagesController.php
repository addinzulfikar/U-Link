<?php

namespace App\Http\Controllers\Chatify;

use Chatify\Http\Controllers\MessagesController as ChatifyMessagesController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessagesController extends ChatifyMessagesController
{
    /**
     * Get contacts list - filtered by role
     */
    public function getContacts(Request $request)
    {
        $user = auth()->user();
        
        // Get allowed users based on role
        $allowedUsers = $user->getAllowedChatUsers();
        $allowedUserIds = $allowedUsers->pluck('id')->toArray();

        // Get the original contacts from Chatify
        $contacts = User::where('id', '!=', $user->id)
            ->whereIn('id', $allowedUserIds)
            ->get();

        $contactsData = [];

        foreach ($contacts as $contact) {
            $contactsData[] = $this->getUserWithAvatar($contact);
        }

        return response()->json([
            'contacts' => $contactsData,
        ]);
    }

    /**
     * Fetch messages - with authorization check
     */
    public function fetch(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $userId = $request->input('id');
        $currentUser = auth()->user();
        $targetUser = User::find($userId);

        // Authorization check
        if (!$targetUser) {
            return response()->json([
                'error' => 'Pengguna tidak ditemukan',
            ], 404);
        }

        if (!$currentUser->canChatWith($targetUser)) {
            return response()->json([
                'error' => 'Anda tidak memiliki akses untuk melihat percakapan ini',
            ], 403);
        }

        // Call parent method to fetch messages
        return parent::fetch($request);
    }

    /**
     * Send message - with authorization check
     */
    public function send(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file',
        ]);

        $recipientId = $request->input('id');
        $currentUser = auth()->user();
        $recipient = User::find($recipientId);

        // Authorization check
        if (!$recipient) {
            return response()->json([
                'error' => 'Penerima tidak ditemukan',
            ], 404);
        }

        if (!$currentUser->canChatWith($recipient)) {
            return response()->json([
                'error' => 'Anda tidak memiliki akses untuk mengirim pesan ke pengguna ini',
            ], 403);
        }

        // Call parent method to send message
        return parent::send($request);
    }

    /**
     * Search for contacts - filtered by role
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $user = auth()->user();
        
        // Get allowed users based on role
        $allowedUsers = $user->getAllowedChatUsers();
        $allowedUserIds = $allowedUsers->pluck('id')->toArray();

        // Search only among allowed contacts
        $users = User::where('id', '!=', $user->id)
            ->whereIn('id', $allowedUserIds)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->get();

        $records = [];
        foreach ($users as $searchUser) {
            $records[] = $this->getUserWithAvatar($searchUser);
        }

        return response()->json([
            'records' => $records,
        ]);
    }

    /**
     * Get user data with avatar
     */
    private function getUserWithAvatar($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => asset('storage/' . config('chatify.user_avatar.folder') . '/' . 
                        ($user->avatar ?? config('chatify.user_avatar.default'))),
            'active_status' => $user->active_status ?? 0,
        ];
    }

    /**
     * Fetch id info for specific user - with authorization
     */
    public function idFetchData(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $userId = $request->input('id');
        $currentUser = auth()->user();
        $targetUser = User::find($userId);

        // Authorization check
        if (!$targetUser || !$currentUser->canChatWith($targetUser)) {
            return response()->json([
                'error' => 'Akses ditolak',
            ], 403);
        }

        // Call parent method
        return parent::idFetchData($request);
    }
}
