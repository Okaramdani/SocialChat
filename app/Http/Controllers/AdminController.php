<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Story;
use App\Models\CallLog;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Dashboard admin
    public function dashboard(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'total_chats' => Chat::count(),
            'total_messages' => Message::count(),
            'total_stories' => Story::count(),
            'online_users' => User::where('is_online', true)->count(),
            'suspended_users' => User::where('is_suspended', true)->count(),
        ];

        $recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get();
        $recentMessages = Message::with('sender')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentMessages'));
    }

    // List semua user
    public function users(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    // Detail user
    public function userDetail(User $user)
    {
        $user->load(['chats', 'stories', 'sentMessages', 'outgoingCalls', 'incomingCalls']);
        return view('admin.user-detail', compact('user'));
    }

    // Suspend user
    public function suspendUser(Request $request, User $user)
    {
        $user->update(['is_suspended' => true]);
        return back()->with('success', 'User di-suspend');
    }

    // Unsuspend user
    public function unsuspendUser(Request $request, User $user)
    {
        $user->update(['is_suspended' => false]);
        return back()->with('success', 'User diaktifkan kembali');
    }

    // Hapus user
    public function deleteUser(Request $request, User $user)
    {
        $user->delete();
        return redirect('/admin/users')->with('success', 'User dihapus');
    }

    // Hapus pesan apapun
    public function deleteMessage(Message $message)
    {
        $message->update(['is_deleted' => true, 'content' => null, 'file_path' => null]);
        return back()->with('success', 'Pesan dihapus');
    }

    // Hapus story apapun
    public function deleteStory(Story $story)
    {
        $story->update(['is_deleted' => true]);
        return back()->with('success', 'Story dihapus');
    }

    // View all call logs
    public function callLogs(Request $request)
    {
        $calls = CallLog::with(['caller:id,name,avatar', 'receiver:id,name,avatar'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('admin.call-logs', compact('calls'));
    }

    // Get online users
    public function onlineUsers(Request $request)
    {
        $onlineUsers = User::where('is_online', true)
            ->select('id', 'name', 'avatar', 'last_seen')
            ->get();

        return response()->json($onlineUsers);
    }

    // Get user activity stats
    public function userStats(Request $request, User $user)
    {
        $stats = [
            'total_messages' => $user->sentMessages()->count(),
            'total_chats' => $user->chats()->count(),
            'total_stories' => $user->stories()->count(),
            'total_calls_made' => $user->outgoingCalls()->count(),
            'total_calls_received' => $user->incomingCalls()->count(),
            'last_active' => $user->last_seen,
        ];

        return response()->json($stats);
    }

    // Update user (admin)
    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|in:admin,user',
            'bio' => 'nullable|string',
            'mood' => 'nullable|string|max:50',
        ]);

        $user->update($data);

        return back()->with('success', 'User diupdate');
    }

    // Create admin user
    public function createAdmin(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        return back()->with('success', 'Admin dibuat');
    }
}
