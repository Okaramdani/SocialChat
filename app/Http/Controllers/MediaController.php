<?php

namespace App\Http\Controllers;

use App\Models\MediaTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    // Get media timeline user
    public function timeline(Request $request)
    {
        $media = $request->user()->mediaTimelines()
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json($media);
    }

    // Upload ke media timeline
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'type' => 'required|in:image,video,file',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('media', $filename, 'public');

        $media = MediaTimeline::create([
            'user_id' => $request->user()->id,
            'media_path' => $filename,
            'type' => $request->type,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json($media, 201);
    }

    // Hapus media
    public function destroy(MediaTimeline $media)
    {
        $user = Auth::user();

        if ($user->id !== $media->user_id && !$user->isAdmin()) {
            abort(403);
        }

        if ($media->media_path) {
            Storage::disk('public')->delete('media/' . $media->media_path);
        }

        $media->delete();

        return response()->json(['success' => true]);
    }

    // Get media by type
    public function byType(Request $request, string $type)
    {
        $media = $request->user()->mediaTimelines()
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json($media);
    }
}
