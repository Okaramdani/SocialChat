{{-- Admin Messages View - Daftar semua pesan untuk moderasi --}}

@extends('layouts.admin')

@section('title', 'Messages - Admin')
@section('header', 'Message Management')

@section('content')
<div class="bg-white dark:bg-dark-100 rounded-xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold">All Messages</h3>
    </div>
    
    <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sender</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Content</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chat</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @php
                $messages = \App\Models\Message::with(['sender', 'chat'])
                    ->where('is_deleted', false)
                    ->latest()
                    ->limit(50)
                    ->get();
            @endphp
            @foreach($messages as $message)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ $message->sender->avatar_url }}" class="w-8 h-8 rounded-full">
                        <span class="text-sm">{{ $message->sender->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <p class="text-sm max-w-xs truncate">{{ $message->content ?: '[' . $message->type . ']' }}</p>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">
                        {{ ucfirst($message->type) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">
                    Chat #{{ $message->chat_id }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">
                    {{ $message->created_at->format('d M, H:i') }}
                </td>
                <td class="px-4 py-3">
                    <form action="{{ route('admin.message.delete', $message) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
