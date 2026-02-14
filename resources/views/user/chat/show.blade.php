<?php
/**
 * Chat Show View
 * Halaman percakapan individual
 */
?>

@extends('layouts.user')

@section('title', 'Chat - Social Chat')

@section('content')
<div class="flex h-[calc(100vh-4rem)]">
    <!-- Chat List Sidebar -->
    <div class="w-80 bg-white dark:bg-dark-100 border-r border-gray-200 dark:border-gray-700 flex flex-col hidden lg:flex">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-3">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
            <h2 class="text-xl font-bold">Chats</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @foreach(auth()->user()->chats()->with('participants')->get() as $c)
                @include('components.chat-item', ['chat' => $c, 'user' => auth()->user()])
            @endforeach
        </div>
    </div>
    
    <!-- Chat Area -->
    <div class="flex-1 flex flex-col bg-gray-50 dark:bg-dark-200">
        <!-- Chat Header -->
        <div class="bg-white dark:bg-dark-100 border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="lg:hidden p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                    <i class="fas fa-arrow-left"></i>
                </a>
                
                @if($chat->type === 'group')
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white">
                        <i class="fas fa-users"></i>
                    </div>
                @else
                    @php
                        $otherUser = $chat->participants()->where('user_id', '!=', auth()->id())->first();
                    @endphp
                    <img src="{{ $otherUser?->avatar_url ?? asset('images/default-avatar.png') }}" 
                         class="w-10 h-10 rounded-xl object-cover">
                @endif
                
                <div>
                    <h3 class="font-semibold">{{ $chat->getDisplayName(auth()->user()) }}</h3>
                    <p class="text-xs text-gray-500">
                        @if($chat->type === 'group')
                            {{ $chat->participants->count() }} participants
                        @elseif($otherUser?->is_online)
                            Online
                        @else
                            Last seen {{ $otherUser?->last_seen?->shortRelativeDiffForHumans() }}
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <button @click="startVideoCall()" class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-video text-gray-600 dark:text-gray-300"></i>
                </button>
                <button @click="startVoiceCall()" class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-phone text-gray-600 dark:text-gray-300"></i>
                </button>
                <button @click="$dispatch('open-modal', 'chat-info')" class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-info-circle text-gray-600 dark:text-gray-300"></i>
                </button>
            </div>
        </div>
        
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 chat-container" id="chatMessages">
            <div id="messagesList">
                @foreach($chat->messages()->with(['sender', 'reactions', 'replyTo'])->where('is_deleted', false)->orderBy('created_at', 'asc')->limit(50)->get() as $message)
                    @include('components.message-item', ['message' => $message])
                @endforeach
            </div>
            
            @include('components.typing-indicator')
        </div>
        
        <!-- Reply Preview -->
        <div id="replyPreview" class="hidden px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-dark-100">
            <div class="flex items-center gap-2">
                <i class="fas fa-reply text-primary-500"></i>
                <div class="flex-1">
                    <p class="text-xs text-gray-500">Replying to <span id="replyName"></span></p>
                    <p class="text-sm truncate" id="replyContent"></p>
                </div>
                <button onclick="cancelReply()" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="bg-white dark:bg-dark-100 border-t border-gray-200 dark:border-gray-700 p-4">
            <form id="messageForm" class="flex items-end gap-2" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="chat_id" value="{{ $chat->id }}">
                <input type="hidden" name="reply_to_id" id="replyToId">
                
                <!-- Attachment Button -->
                <div class="relative" x-data="{ showMenu: false }">
                    <button type="button" @click="showMenu = !showMenu" 
                            class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-paperclip text-gray-600 dark:text-gray-300"></i>
                    </button>
                    
                    <!-- Attachment Menu -->
                    <div x-show="showMenu" x-cloak @click.away="showMenu = false"
                         class="absolute bottom-full left-0 mb-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-2 min-w-[200px]">
                        <button type="button" onclick="document.getElementById('imageInput').click(); showMenu = false"
                                class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                <i class="fas fa-image text-green-600"></i>
                            </div>
                            <span class="text-sm">Photo</span>
                        </button>
                        <button type="button" onclick="document.getElementById('videoInput').click(); showMenu = false"
                                class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                <i class="fas fa-video text-purple-600"></i>
                            </div>
                            <span class="text-sm">Video</span>
                        </button>
                        <button type="button" onclick="document.getElementById('audioInput').click(); showMenu = false"
                                class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                                <i class="fas fa-microphone text-yellow-600"></i>
                            </div>
                            <span class="text-sm">Voice</span>
                        </button>
                        <button type="button" onclick="document.getElementById('fileInput').click(); showMenu = false"
                                class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-file text-gray-600"></i>
                            </div>
                            <span class="text-sm">Document</span>
                        </button>
                        <button type="button" onclick="document.getElementById('locationInput').click(); showMenu = false"
                                class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <span class="text-sm">Location</span>
                        </button>
                    </div>
                    
                    <!-- Hidden Inputs -->
                    <input type="file" id="imageInput" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'image')">
                    <input type="file" id="videoInput" class="hidden" accept="video/*" onchange="handleFileSelect(this, 'video')">
                    <input type="file" id="audioInput" class="hidden" accept="audio/*" onchange="handleFileSelect(this, 'voice')">
                    <input type="file" id="fileInput" class="hidden" 
                           onchange="handleFileSelect(this, 'file')"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.7z,.gif,.svg,.webp">
                    <input type="file" id="locationInput" class="hidden" onchange="handleLocation(this)">
                </div>
                
                <!-- Location Button -->
                <button type="button" onclick="document.getElementById('locationInput').click()" 
                        class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="Kirim Lokasi">
                    <i class="fas fa-map-marker-alt"></i>
                </button>
                
                <!-- Mic Button (Voice Recording) -->
                <button type="button" id="micBtn" onclick="toggleVoiceRecording()"
                        class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-microphone"></i>
                </button>
                
                <!-- Text Input -->
                <div class="flex-1">
                    <textarea name="content" id="messageInput" rows="1"
                              placeholder="Ketik pesan..."
                              class="w-full px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500 resize-none"
                              oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                              onkeydown="handleKeydown(event)"></textarea>
                </div>
                
                <!-- Emoji Button -->
                <button type="button" onclick="toggleEmojiPicker()" 
                        class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-smile text-gray-600 dark:text-gray-300"></i>
                </button>
                
                <!-- Send Button -->
                <button type="submit" class="p-2.5 rounded-xl bg-primary-500 text-white hover:bg-primary-600">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            
            <!-- Voice Recording Panel -->
            <div id="voiceRecordingPanel" class="hidden mt-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center gap-3">
                <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-red-600 dark:text-red-400" id="recordingTime">0:00</span>
                <span class="text-sm text-red-500"> Merekam...</span>
                <button type="button" onclick="stopVoiceRecording()" class="ml-auto p-2 bg-red-500 text-white rounded-full hover:bg-red-600">
                    <i class="fas fa-stop"></i>
                </button>
            </div>
            
            <!-- File Preview -->
            <div id="filePreview" class="hidden mt-2 p-2 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-file text-gray-500"></i>
                <span class="flex-1 text-sm" id="fileName"></span>
                <button onclick="clearFile()" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Chat Info Sidebar (optional) -->
    <div class="w-72 bg-white dark:bg-dark-100 border-l border-gray-200 dark:border-gray-700 hidden xl:flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold">Details</h3>
        </div>
        
        <div class="p-4 space-y-4 overflow-y-auto flex-1">
            @if($chat->type === 'group')
                <div>
                    <p class="text-xs text-gray-500 mb-2">Participants</p>
                    @foreach($chat->participants as $participant)
                        <div class="flex items-center gap-2 py-2">
                            <img src="{{ $participant->avatar_url }}" class="w-8 h-8 rounded-full">
                            <div class="flex-1">
                                <p class="text-sm">{{ $participant->name }}</p>
                                <p class="text-xs text-gray-500">{{ $participant->is_online ? 'Online' : 'Offline' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <div>
                <p class="text-xs text-gray-500 mb-2">Details</p>
                <button onclick="pinChat()" class="w-full flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-thumbtack"></i> Pin Chat
                </button>
                <button onclick="startVideoCall()" class="w-full flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-video"></i> Video Call
                </button>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 mb-2">Actions</p>
                @if($chat->type === 'group')
                    <button onclick="leaveGroup()" class="w-full flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-sign-out-alt"></i> Keluar Grup
                    </button>
                    <button onclick="deleteGroup()" class="w-full flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500">
                        <i class="fas fa-trash"></i> Hapus Grup
                    </button>
                @else
                    <button onclick="deleteChat()" class="w-full flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500">
                        <i class="fas fa-trash"></i> Hapus Chat
                    </button>
                    <button onclick="removeContact()" class="w-full flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500">
                        <i class="fas fa-user-minus"></i> Hapus Kontak
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Variables
let replyToId = null;
let selectedFile = null;
let selectedFileType = 'file';
const chatId = {{ $chat->id }};
const userId = {{ auth()->id() }};

// Voice Recording Variables
let mediaRecorder = null;
let audioChunks = [];
let voiceRecordingInterval = null;
let recordingSeconds = 0;

// File type icons
const fileIcons = {
    'image': 'fa-image',
    'video': 'fa-video',
    'voice': 'fa-microphone',
    'file': 'fa-file',
    'location': 'fa-map-marker-alt'
};

// Toggle Voice Recording
async function toggleVoiceRecording() {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        stopVoiceRecording();
    } else {
        startVoiceRecording();
    }
}

async function startVoiceRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        recordingSeconds = 0;
        
        mediaRecorder.ondataavailable = event => {
            audioChunks.push(event.data);
        };
        
        mediaRecorder.onstop = async () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const formData = new FormData();
            formData.append('file', audioBlob, 'voice.webm');
            formData.append('type', 'voice');
            
            // Stop all tracks
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
            
            // Send voice message
            fetch(`/api/chats/${chatId}/messages/media`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            }).then(() => window.location.reload())
              .catch(err => {
                  console.error('Error sending voice:', err);
                  alert('Gagal mengirim voice note');
              });
        };
        
        mediaRecorder.start();
        
        // Show recording panel
        document.getElementById('voiceRecordingPanel').classList.remove('hidden');
        document.getElementById('micBtn').classList.add('bg-red-500', 'text-white');
        
        // Update recording time
        voiceRecordingInterval = setInterval(() => {
            recordingSeconds++;
            const minutes = Math.floor(recordingSeconds / 60);
            const seconds = recordingSeconds % 60;
            document.getElementById('recordingTime').textContent = 
                `${minutes}:${String(seconds).padStart(2, '0')}`;
        }, 1000);
        
    } catch (err) {
        console.error('Error accessing microphone:', err);
        alert('Tidak dapat akses microphone. Izinkan akses microphone.');
    }
}

function stopVoiceRecording() {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        clearInterval(voiceRecordingInterval);
        document.getElementById('voiceRecordingPanel').classList.add('hidden');
        document.getElementById('micBtn').classList.remove('bg-red-500', 'text-white');
    }
}

// Send message
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const content = input.value.trim();
    
    if (!content && !selectedFile) return;
    
    const sendBtn = document.querySelector('button[type="submit"]');
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    if (selectedFile) {
        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('type', selectedFileType);
        if (content) formData.append('caption', content);
        if (replyToId) formData.append('reply_to_id', replyToId);
        
        fetch(`/api/chats/${chatId}/messages/media`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        }).then(response => {
            if (!response.ok) throw new Error('Failed to send');
            return response.json();
        }).then(() => {
            clearFile();
            cancelReply();
            input.value = '';
        }).catch(err => {
            console.error('Error:', err);
            alert('Failed to send message');
        }).finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
    } else {
        fetch(`/api/chats/${chatId}/messages`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ content, reply_to_id: replyToId })
        }).then(response => {
            if (!response.ok) throw new Error('Failed to send');
            return response.json();
        }).then(() => {
            input.value = '';
            cancelReply();
        }).catch(err => {
            console.error('Error:', err);
            alert('Failed to send message');
        }).finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
    }
});

function handleKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.form.dispatchEvent(new Event('submit'));
    }
}

function handleFileSelect(input, type = null) {
    if (input.files[0]) {
        selectedFile = input.files[0];
        selectedFileType = type || getFileType(selectedFile);
        
        // Show file preview
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const icon = preview.querySelector('i');
        
        fileName.textContent = selectedFile.name + ' (' + formatFileSize(selectedFile.size) + ')';
        icon.className = 'fas ' + fileIcons[selectedFileType];
        preview.classList.remove('hidden');
    }
}

function getFileType(file) {
    const type = file.type;
    if (type.startsWith('image/')) return 'image';
    if (type.startsWith('video/')) return 'video';
    if (type.startsWith('audio/')) return 'voice';
    return 'file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function handleLocation(input) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            fetch(`/api/chats/${chatId}/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ content: `${lat},${lng}`, type: 'location' })
            }).then(() => window.location.reload());
        }, (error) => {
            alert('Could not get location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by this browser');
    }
}

function clearFile() {
    selectedFile = null;
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

function setReply(messageId, senderName, content) {
    replyToId = messageId;
    document.getElementById('replyToId').value = messageId;
    document.getElementById('replyName').textContent = senderName;
    document.getElementById('replyContent').textContent = content.substring(0, 50);
    document.getElementById('replyPreview').classList.remove('hidden');
}

function cancelReply() {
    replyToId = null;
    document.getElementById('replyToId').value = '';
    document.getElementById('replyPreview').classList.add('hidden');
}

// Mark as read
fetch(`/api/chats/${chatId}/read`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
});

// Real-time with Echo
if (typeof Echo !== 'undefined') {
    Echo.private(`chat.${chatId}`)
        .listen('MessageSent', (e) => {
            if (e.message.sender_id !== userId) {
                appendMessage(e.message);
            }
        })
        .listen('TypingEvent', (e) => {
            if (e.user_id !== userId) {
                document.getElementById('typingIndicator').style.display = 'flex';
                setTimeout(() => {
                    document.getElementById('typingIndicator').style.display = 'none';
                }, 3000);
            }
        });
}

function appendMessage(message) {
    const list = document.getElementById('messagesList');
    const div = document.createElement('div');
    div.className = 'message-wrapper flex gap-2 mb-3' + (message.sender_id === userId ? ' flex-row-reverse' : '');
    div.innerHTML = `<p>${message.content}</p>`;
    list.appendChild(div);
    scrollToBottom();
}

function scrollToBottom() {
    const container = document.getElementById('chatMessages');
    container.scrollTop = container.scrollHeight;
}

scrollToBottom();

// Call functions
function startVideoCall() {
    initiateCall('video');
}

function startVoiceCall() {
    initiateCall('voice');
}

function initiateCall(type) {
    const receiverId = {{ $otherUser?->id ?? 'null' }};
    if (!receiverId) {
        alert('Cannot start call: no recipient');
        return;
    }
    
    fetch('/api/call/initiate', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({
            chat_id: chatId,
            type: type,
            receiver_id: receiverId
        })
    }).then(response => {
        if (!response.ok) throw new Error('Failed to initiate call');
        return response.json();
    }).then(callLog => {
        window.location.href = `/call/${chatId}/${type}?call_id=${callLog.id}`;
    }).catch(err => {
        console.error('Error:', err);
        alert('Failed to start call');
    });
}

function pinChat() {
    fetch(`/api/chats/${chatId}/pin`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.reload());
}

function leaveGroup() {
    if (!confirm('Keluar dari grup ini?')) return;
    fetch(`/api/chats/${chatId}/leave`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href = '/dashboard');
}

function deleteGroup() {
    if (!confirm('Hapus grup ini? Semua pesan akan dihapus.')) return;
    fetch(`/api/chats/${chatId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href = '/dashboard');
}

function deleteChat() {
    if (!confirm('Hapus chat ini? Semua pesan akan dihapus.')) return;
    fetch(`/api/chats/${chatId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href = '/dashboard');
}

function removeContact() {
    if (!confirm('Hapus kontak ini? Chat juga akan dihapus.')) return;
    fetch(`/api/chats/${chatId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href = '/dashboard');
}

// Message Actions
document.addEventListener('reply-message', function(e) {
    const messageId = e.detail.id;
    const messageEl = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageEl) {
        const content = messageEl.querySelector('p')?.textContent || '';
        replyToId = messageId;
        document.getElementById('replyToId').value = messageId;
        document.getElementById('replyName').textContent = 'Message';
        document.getElementById('replyContent').textContent = content.substring(0, 50);
        document.getElementById('replyPreview').classList.remove('hidden');
    }
});

document.addEventListener('delete-message', function(e) {
    const messageId = e.detail.id;
    if (!confirm('Hapus pesan ini?')) return;
    
    fetch(`/api/messages/${messageId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.reload());
});

document.addEventListener('edit-message', function(e) {
    const messageId = e.detail.id;
    const messageEl = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageEl) {
        const content = messageEl.querySelector('p')?.textContent || '';
        const newContent = prompt('Edit pesan:', content);
        if (newContent === null || newContent === content) return;
        
        fetch(`/api/messages/${messageId}`, {
            method: 'PUT',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({ content: newContent })
        }).then(() => window.location.reload());
    }
});
</script>
@endsection
