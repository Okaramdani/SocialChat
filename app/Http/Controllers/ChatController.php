<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // List semua chat user
    public function index(Request $request)
    {
        $user = $request->user();
        
        $chats = $user->chats()
            ->with(['participants', 'latestMessage.sender'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('chat.index', compact('chats'));
    }

    // Detail chat
    public function show(Request $request, Chat $chat)
    {
        $user = $request->user();

        // Cek peserta
        if (!$chat->participants()->where('user_id', $user->id)->exists()) {
            abort(403, 'Anda bukan peserta chat ini');
        }

        $chat->load(['participants', 'messages.sender', 'messages.reactions']);

        return view('user.chat.show', compact('chat'));
    }

    // Buat chat 1-on-1
    public function createPrivate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $currentUser = $request->user();
        $otherUserId = $request->user_id;

        // Cek apakah chat sudah ada
        $existingChat = Chat::where('type', 'private')
            ->whereHas('participants', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id);
            })
            ->whereHas('participants', function ($query) use ($otherUserId) {
                $query->where('user_id', $otherUserId);
            })
            ->first();

        if ($existingChat) {
            return response()->json(['chat' => $existingChat]);
        }

        // Buat chat baru
        $chat = Chat::create([
            'type' => 'private',
            'created_by' => $currentUser->id,
        ]);

        $chat->participants()->attach([$currentUser->id, $otherUserId]);

        return response()->json(['chat' => $chat]);
    }

    // Buat grup chat
    public function createGroup(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'participants' => 'required|array|min:2',
            'participants.*' => 'exists:users,id',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $currentUser = $request->user();
        $participants = array_merge($data['participants'], [$currentUser->id]);

        $chat = Chat::create([
            'name' => $data['name'],
            'type' => 'group',
            'created_by' => $currentUser->id,
        ]);

        $chat->participants()->attach($participants);

        return response()->json(['chat' => $chat]);
    }

    // Pin chat
    public function pin(Request $request, Chat $chat)
    {
        $chat->update(['is_pinned' => !$chat->is_pinned]);
        return response()->json(['is_pinned' => $chat->is_pinned]);
    }

    // Update label chat
    public function updateLabel(Request $request, Chat $chat)
    {
        $request->validate([
            'label' => 'nullable|string|max:50',
        ]);

        $chat->update(['label' => $request->label]);
        return response()->json(['label' => $chat->label]);
    }

    // Get chat list untuk sidebar (API)
    public function listApi(Request $request)
    {
        $user = $request->user();

        $chats = $user->chats()
            ->with(['participants:id,name,avatar,is_online,last_seen', 'latestMessage'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($chat) use ($user) {
                $chat->display_name = $chat->getDisplayName($user);
                $chat->display_avatar = $chat->getDisplayAvatar($user);
                return $chat;
            });

        return response()->json($chats);
    }

    // Keluar dari grup
    public function leave(Request $request, Chat $chat)
    {
        $user = $request->user();

        if (!$chat->participants()->where('user_id', $user->id)->exists()) {
            abort(403, 'Anda bukan peserta chat ini');
        }

        if ($chat->type !== 'group') {
            abort(400, 'Hanya grup yang bisa ditinggalkan');
        }

        $chat->participants()->detach($user->id);

        if ($chat->participants()->count() === 0) {
            $chat->messages()->delete();
            $chat->delete();
        }

        return response()->json(['success' => true, 'message' => 'Berhasil keluar dari grup']);
    }

    // Hapus grup chat (hapus grup & semua pesan)
    public function destroy(Request $request, Chat $chat)
    {
        $user = $request->user();

        if (!$chat->participants()->where('user_id', $user->id)->exists()) {
            abort(403, 'Anda bukan peserta chat ini');
        }

        $chat->messages()->delete();
        $chat->participants()->detach();
        $chat->delete();

        return response()->json(['success' => true, 'message' => 'Grup berhasil dihapus']);
    }
}
