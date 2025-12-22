<?php

namespace App\Http\Controllers\Chatify;

use Chatify\Http\Controllers\MessagesController as ChatifyMessagesController;
use App\Models\User;
use Illuminate\Http\Request;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class MessagesController extends ChatifyMessagesController
{
    private function pusherIsConfigured(): bool
    {
        $key = (string) config('chatify.pusher.key', '');
        $secret = (string) config('chatify.pusher.secret', '');
        $appId = (string) config('chatify.pusher.app_id', '');

        return $key !== '' && $secret !== '' && $appId !== '';
    }

    /**
     * Get contacts list - filtered by role
     */
    public function getContacts(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        /** @var \App\Models\User $user */

        $allowedUserIds = $user->getAllowedChatUsers()->pluck('id')->all();

        $contactsQuery = User::query()
            ->where('id', '!=', $user->id)
            ->whereIn('id', $allowedUserIds)
            ->orderBy('name');

        $contacts = $contactsQuery->paginate($request->per_page ?? $this->perPage);
        $contactsHtml = '';

        foreach ($contacts->items() as $contact) {
            $contactsHtml .= Chatify::getContactItem($contact);
        }

        if ($contacts->total() < 1) {
            $contactsHtml = '<p class="message-hint center-el"><span>Your contact list is empty</span></p>';
        }

        return response()->json([
            'contacts' => $contactsHtml,
            'total' => $contacts->total() ?? 0,
            'last_page' => $contacts->lastPage() ?? 1,
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
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        /** @var \App\Models\User $currentUser */
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
            // Chatify JS sends the attachment field as "file"
            'file' => 'nullable|file',
        ]);

        $recipientId = (int) $request->input('id');
        $currentUser = $request->user();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        /** @var \App\Models\User $currentUser */
        $recipient = User::find($recipientId);

        if (!$recipient) {
            return response()->json(['error' => 'Penerima tidak ditemukan'], 404);
        }
        if (!$currentUser || !$currentUser->canChatWith($recipient)) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk mengirim pesan ke pengguna ini'], 403);
        }

        // --- Copied from vendor Chatify send(), with Pusher guarded to prevent 500 when not configured.
        $error = (object) [
            'status' => 0,
            'message' => null,
        ];

        $attachment = null;
        $attachment_title = null;

        if ($request->hasFile('file')) {
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files  = Chatify::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            if ($file->getSize() < Chatify::getMaxUploadSize()) {
                if (in_array(strtolower($file->extension()), $allowed)) {
                    $attachment_title = $file->getClientOriginalName();
                    $attachment = (string) Str::uuid() . '.' . $file->extension();
                    $file->storeAs(
                        config('chatify.attachments.folder'),
                        $attachment,
                        config('chatify.storage_disk_name')
                    );
                } else {
                    $error->status = 1;
                    $error->message = 'File extension not allowed!';
                }
            } else {
                $error->status = 1;
                $error->message = 'File size you are trying to upload is too large!';
            }
        }

        if (!$error->status) {
            $message = Chatify::newMessage([
                'from_id' => $currentUser->id,
                'to_id' => $recipientId,
                'body' => htmlentities(trim((string) $request->input('message', '')), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object) [
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim((string) $attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);

            $messageData = Chatify::parseMessage($message);

            if ($currentUser->id !== $recipientId && $this->pusherIsConfigured()) {
                try {
                    Chatify::push('private-chatify.' . $recipientId, 'messaging', [
                        'from_id' => $currentUser->id,
                        'to_id' => $recipientId,
                        'message' => Chatify::messageCard($messageData, true),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Chatify Pusher push failed; realtime disabled for this request.', [
                        'userId' => $currentUser->id,
                        'to' => $recipientId,
                        'exception' => $e->getMessage(),
                    ]);
                }
            }
        }

        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => Chatify::messageCard(@$messageData),
            'tempID' => $request['temporaryMsgId'],
        ]);
    }

    /**
     * Search for contacts - filtered by role
     */
    public function search(Request $request)
    {
        $input = trim((string) $request->input('input', $request->input('query', '')));
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        /** @var \App\Models\User $user */
        $allowedUserIds = $user->getAllowedChatUsers()->pluck('id')->all();

        $recordsQuery = User::query()
            ->where('id', '!=', $user->id)
            ->whereIn('id', $allowedUserIds)
            ->where(function ($q) use ($input) {
                $q->where('name', 'like', "%{$input}%")
                    ->orWhere('email', 'like', "%{$input}%");
            });

        $records = $recordsQuery->paginate($request->per_page ?? $this->perPage);
        $recordsHtml = '';

        foreach ($records->items() as $record) {
            $recordsHtml .= view('Chatify::layouts.listItem', [
                'get' => 'search_item',
                'user' => Chatify::getUserWithAvatar($record),
            ])->render();
        }

        if ($records->total() < 1) {
            $recordsHtml = '<p class="message-hint center-el"><span>Nothing to show.</span></p>';
        }

        return response()->json([
            'records' => $recordsHtml,
            'total' => $records->total(),
            'last_page' => $records->lastPage(),
        ]);
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
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        /** @var \App\Models\User $currentUser */
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
