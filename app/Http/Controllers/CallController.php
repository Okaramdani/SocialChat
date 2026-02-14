<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Chat;
use App\Events\CallInitiated;
use App\Events\WebRTCSignalEvent;
use App\Notifications\IncomingCallNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    // Initiate call
    public function initiate(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'type' => 'required|in:video,voice',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $callLog = CallLog::create([
            'caller_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'type' => $request->type,
            'status' => 'pending',
        ]);

        // Broadcast call event
        event(new CallInitiated($callLog));
        
        // Send notification to receiver
        $receiver = \App\Models\User::find($request->receiver_id);
        if ($receiver) {
            $receiver->notify(new IncomingCallNotification($callLog));
        }

        return response()->json($callLog);
    }

    // Update call status
    public function updateStatus(Request $request, CallLog $callLog)
    {
        $request->validate([
            'status' => 'required|in:answered,missed,rejected',
            'duration' => 'nullable|integer|min:0',
        ]);

        $callLog->update([
            'status' => $request->status,
            'duration' => $request->duration ?? 0,
        ]);

        return response()->json($callLog);
    }

    // Get call logs
    public function index(Request $request)
    {
        $user = $request->user();

        $calls = CallLog::where('caller_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['caller:id,name,avatar', 'receiver:id,name,avatar'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json($calls);
    }

    // Get all call logs (admin only)
    public function allCalls(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $calls = CallLog::with(['caller:id,name,avatar', 'receiver:id,name,avatar'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($calls);
    }

    // WebRTC signaling
    public function signaling(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'signal' => 'required|array',
        ]);

        event(new WebRTCSignalEvent(
            $request->user()->id,
            $request->target_user_id,
            $request->signal
        ));

        return response()->json(['success' => true]);
    }

    // Get call log detail
    public function show(CallLog $callLog)
    {
        $callLog->load(['caller', 'receiver']);
        return response()->json($callLog);
    }
}
