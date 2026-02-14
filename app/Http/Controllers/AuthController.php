<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Tampilkan form register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Cek jika user di-suspend
        if ($user && $user->is_suspended) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah di-suspend. Hubungi admin.',
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    // Logout
    public function logout(Request $request)
    {
        // Update status offline
        if (Auth::check()) {
            Auth::user()->setOffline();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // API: Login untuk mobile/API
    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->is_suspended) {
            return response()->json(['message' => 'Akun di-suspend'], 403);
        }

        if (Auth::attempt($credentials)) {
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // API: Register untuk mobile/API
    public function apiRegister(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    // API: Logout
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // Get current user
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Search users
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }
        
        $users = User::where('id', '!=', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'email', 'avatar', 'is_online', 'last_seen')
            ->limit(20)
            ->get();
        
        return response()->json(['users' => $users]);
    }

    // Update profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:150',
            'mood' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'name' => $data['name'],
            'bio' => $data['bio'] ?? null,
            'mood' => $data['mood'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('avatars', $filename, 'public');
            $updateData['avatar'] = $filename;
        }

        $user->update($updateData);

        return redirect('/profile')->with('success', 'Profile updated successfully');
    }

    // Delete avatar
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();
        
        if ($user->avatar) {
            $avatarPath = storage_path('app/public/avatars/' . $user->avatar);
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
            $user->update(['avatar' => null]);
        }

        return response()->json(['success' => true]);
    }
}
