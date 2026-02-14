{{-- User Detail View - Detail user untuk admin --}}

@extends('layouts.admin')

@section('title', 'User Detail - ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <img src="{{ $user->avatar ? asset('storage/avatars/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.$user->name }}" 
                     class="w-20 h-20 rounded-full">
                <div>
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <span class="inline-block mt-2 px-3 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-primary-100 text-primary-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ $user->role }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Informasi User</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium">
                        @if($user->is_suspended)
                            <span class="text-red-600">Suspended</span>
                        @elseif($user->is_online)
                            <span class="text-green-600">Online</span>
                        @else
                            <span class="text-gray-600">Offline</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Bergabung</p>
                    <p class="font-medium">{{ $user->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Mood</p>
                    <p class="font-medium">{{ $user->mood ?? 'Tidak ada' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Chat</p>
                    <p class="font-medium">{{ $user->chats->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Story</p>
                    <p class="font-medium">{{ $user->stories->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pesan</p>
                    <p class="font-medium">{{ $user->sentMessages->count() }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex gap-4">
            @if($user->is_suspended)
                <form action="{{ route('admin.user.unsuspend', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        Aktifkan User
                    </button>
                </form>
            @else
                <form action="{{ route('admin.user.suspend', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Suspend User
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
