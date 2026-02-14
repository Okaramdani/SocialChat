<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;

class MessageController extends Controller
{
    // Get messages dalam chat
    public function index(Request $request, Chat $chat)
    {
        $messages = $chat->messages()
            ->with(['sender:id,name,avatar', 'reactions', 'replyTo'])
            ->where('is_deleted', false)
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json($messages);
    }

    // Kirim pesan teks
    public function store(Request $request, Chat $chat)
    {
        $data = $request->validate([
            'content' => 'nullable|string',
            'type' => 'nullable|string|in:text,location',
            'reply_to_id' => 'nullable|exists:messages,id',
            'self_destruct_minutes' => 'nullable|integer|min:1|max:10080', // max 7 hari
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $request->user()->id,
            'content' => $data['content'] ?? '',
            'reply_to_id' => $data['reply_to_id'] ?? null,
            'type' => $data['type'] ?? 'text',
            'self_destruct_at' => isset($data['self_destruct_minutes'])
                ? now()->addMinutes($data['self_destruct_minutes'])
                : null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
        ]);

        // Update chat updated_at
        $chat->touch();

        // Load relasi untuk response
        $message->load(['sender', 'replyTo']);

        // Broadcast event jika bukan scheduled
        if (!$message->scheduled_at) {
            event(new MessageSent($message));
            
            // Send notification to chat participants (except sender)
            $chat->participants()->where('user_id', '!=', $request->user()->id)->get()->each(function ($participant) use ($message) {
                $participant->notify(new NewMessageNotification($message));
            });
        }

        return response()->json($message);
    }

    // Kirim pesan dengan media
    public function storeMedia(Request $request, Chat $chat)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB max
            'type' => 'required|in:image,video,file,voice,location',
            'caption' => 'nullable|string',
            'reply_to_id' => 'nullable|exists:messages,id',
            'self_destruct_minutes' => 'nullable|integer|min:1|max:10080',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('messages', $filename, 'public');

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $request->user()->id,
            'content' => $request->caption ?? '',
            'reply_to_id' => $request->reply_to_id ?? null,
            'type' => $request->type,
            'file_path' => $filename,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'self_destruct_at' => $request->self_destruct_minutes
                ? now()->addMinutes($request->self_destruct_minutes)
                : null,
        ]);

        $chat->touch();
        $message->load(['sender']);

        event(new MessageSent($message));
        
        // Send notification to chat participants (except sender)
        $chat->participants()->where('user_id', '!=', $request->user()->id)->get()->each(function ($participant) use ($message) {
            $participant->notify(new NewMessageNotification($message));
        });

        return response()->json($message);
    }

    // Tambah reaction
    public function react(Request $request, Message $message)
    {
        $request->validate([
            'emoji' => 'required|string|max:10',
        ]);

        $reaction = MessageReaction::updateOrCreate(
            [
                'message_id' => $message->id,
                'user_id' => $request->user()->id,
            ],
            ['emoji' => $request->emoji]
        );

        return response()->json($reaction);
    }

    // Hapus reaction
    public function removeReaction(Message $message)
    {
        MessageReaction::where('message_id', $message->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['success' => true]);
    }

    // Update pesan (edit) - hanya untuk pengirim
    public function update(Request $request, Message $message)
    {
        $user = Auth::user();

        if ($user->id !== $message->sender_id) {
            abort(403, 'Tidak berhak mengedit pesan ini');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $message->update(['content' => $request->content]);

        return response()->json(['message' => $message]);
    }

    // Hapus pesan - hanya untuk pengirim
    public function destroy(Message $message)
    {
        $user = Auth::user();

        if ($user->id !== $message->sender_id) {
            abort(403, 'Tidak berhak menghapus pesan ini');
        }

        $message->update(['is_deleted' => true, 'content' => null, 'file_path' => null]);

        return response()->json(['success' => true]);
    }

    // Mark chat as read
    public function markAsRead(Request $request, Chat $chat)
    {
        $chat->participants()->updateExistingPivot($request->user()->id, [
            'last_read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
