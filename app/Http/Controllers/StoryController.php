<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewStoryNotification;

class StoryController extends Controller
{
    // Get stories dari semua yang di-follow
    public function index(Request $request)
    {
        $user = $request->user();

        $stories = Story::with('user:id,name,avatar,is_online')
            ->where('is_deleted', false)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        return response()->json($stories);
    }

    // Get stories dari user tertentu
    public function userStories(User $user)
    {
        $stories = $user->stories()
            ->where('is_deleted', false)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($stories);
    }

    // Upload story
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'type' => 'required|in:image,video',
            'caption' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('stories', $filename, 'public');

        $expiresHours = $request->input('expires_in', 24);
        
        $story = Story::create([
            'user_id' => $request->user()->id,
            'media_path' => $filename,
            'type' => $request->type,
            'caption' => $request->caption,
            'expires_at' => now()->addHours($expiresHours),
        ]);
        
        $story->load('user');
        
        // Notify all other users about new story
        User::where('id', '!=', $request->user()->id)->each(function ($user) use ($story) {
            $user->notify(new NewStoryNotification($story));
        });

        return response()->json($story, 201);
    }

    // Hapus story (admin bisa hapus semua, user hanya miliknya)
    public function destroy(Story $story)
    {
        $user = Auth::user();

        if ($user->id !== $story->user_id && !$user->isAdmin()) {
            abort(403, 'Tidak berhak menghapus story ini');
        }

        $story->update(['is_deleted' => true]);

        if ($story->media_path) {
            Storage::disk('public')->delete('stories/' . $story->media_path);
        }

        return response()->json(['success' => true]);
    }

    // Get story viewer count (untuk admin)
    public function views(Story $story)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        return response()->json([
            'views' => $story->views()->count(),
            'story' => $story,
        ]);
    }
}
