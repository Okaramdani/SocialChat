<?php
/**
 * Voice Call View
 * Halaman voice call dengan WebRTC
 */
?>

@extends('layouts.user')

@section('title', 'Voice Call - Social Chat')

@section('content')
<div class="h-screen bg-gradient-to-br from-dark-200 to-dark-300 flex flex-col items-center justify-center" x-data="voiceCallApp">
    <!-- User Info -->
    <div class="text-center mb-8">
        <div class="relative inline-block">
            <img src="{{ $otherUser?->avatar_url ?? asset('images/default-avatar.png') }}" 
                 class="w-32 h-32 rounded-full object-cover mb-4 ring-4 ring-primary-500"
                 :class="{ 'animate-pulse': calling }">
            @if($otherUser?->is_online)
                <span class="absolute bottom-4 right-4 w-5 h-5 bg-4 border-dark-300-green-500 border rounded-full"></span>
            @endif
        </div>
        <h2 class="text-2xl font-bold text-white">{{ $otherUser?->name ?? 'Unknown' }}</h2>
        <p class="text-gray-400 mt-2" x-text="callStatus">Calling...</p>
    </div>
    
    <!-- Call Duration -->
    <div class="text-4xl font-mono text-white mb-12" x-show="connected" x-text="callDuration">00:00</div>
    
    <!-- Call Actions -->
    <div class="flex items-center gap-6">
        <!-- Mute -->
        <button @click="toggleMute()" 
                class="w-16 h-16 rounded-full flex items-center justify-center transition-colors"
                :class="muted ? 'bg-red-500' : 'bg-gray-700 hover:bg-gray-600'">
            <i class="fas text-xl" :class="muted ? 'fa-microphone-slash' : 'fa-microphone'"></i>
        </button>
        
        <!-- End Call -->
        <button @click="endCall()" 
                class="w-20 h-20 rounded-full bg-red-600 hover:bg-red-700 flex items-center justify-center transition-colors">
            <i class="fas fa-phone-slash text-2xl"></i>
        </button>
        
        <!-- Speaker -->
        <button @click="toggleSpeaker()" 
                class="w-16 h-16 rounded-full flex items-center justify-center transition-colors"
                :class="speakerOn ? 'bg-primary-500' : 'bg-gray-700 hover:bg-gray-600'">
            <i class="fas text-xl" :class="speakerOn ? 'fa-volume-up' : 'fa-volume-mute'"></i>
        </button>
    </div>
</div>

<script>
const voiceCallApp = {
    chatId: {{ $chatId }},
    type: '{{ $type }}',
    localStream: null,
    peerConnection: null,
    muted: false,
    speakerOn: true,
    calling: true,
    connected: false,
    callStatus: 'Calling...',
    callDuration: '00:00',
    durationInterval: null,
    
    init() {
        this.initLocalStream();
    },
    
    async initLocalStream() {
        try {
            this.localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
            this.initWebRTC();
        } catch (err) {
            console.error('Error accessing microphone:', err);
            this.callStatus = 'Microphone access denied';
        }
    },
    
    initWebRTC() {
        const config = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };
        this.peerConnection = new RTCPeerConnection(config);
        
        this.localStream.getTracks().forEach(track => {
            this.peerConnection.addTrack(track, this.localStream);
        });
        
        this.peerConnection.ontrack = () => {
            this.connected = true;
            this.calling = false;
            this.callStatus = 'Connected';
            this.startTimer();
        };
        
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                this.sendSignal('ice-candidate', event.candidate);
            }
        };
        
        this.createOffer();
    },
    
    async createOffer() {
        const offer = await this.peerConnection.createOffer();
        await this.peerConnection.setLocalDescription(offer);
        this.sendSignal('offer', offer);
    },
    
    async handleOffer(offer) {
        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
        const answer = await this.peerConnection.createAnswer();
        await this.peerConnection.setLocalDescription(answer);
        this.sendSignal('answer', answer);
    },
    
    async handleAnswer(answer) {
        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
    },
    
    async handleIceCandidate(candidate) {
        await this.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
    },
    
    sendSignal(type, data) {
        fetch('/api/call/signaling', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                target_user_id: {{ $otherUser?->id ?? 'null' }},
                signal: { type: type, data: data }
            })
        });
    },
    
    startTimer() {
        let seconds = 0;
        this.durationInterval = setInterval(() => {
            seconds++;
            const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            this.callDuration = `${mins}:${secs}`;
        }, 1000);
    },
    
    toggleMute() {
        this.muted = !this.muted;
        this.localStream.getAudioTracks().forEach(track => track.enabled = !this.muted);
    },
    
    toggleSpeaker() {
        this.speakerOn = !this.speakerOn;
    },
    
    endCall() {
        if (this.localStream) this.localStream.getTracks().forEach(track => track.stop());
        if (this.peerConnection) this.peerConnection.close();
        if (this.durationInterval) clearInterval(this.durationInterval);
        window.location.href = '/chat/' + this.chatId;
    }
};

// Listen for signaling
if (typeof Echo !== 'undefined') {
    Echo.private('user.{{ auth()->id() }}')
        .listen('WebRTCSignalEvent', (event) => {
            console.log('Received signal:', event);
            const data = event.signal;
            if (data.type === 'offer') voiceCallApp.handleOffer(data.data);
            else if (data.type === 'answer') voiceCallApp.handleAnswer(data.data);
            else if (data.type === 'ice-candidate') voiceCallApp.handleIceCandidate(data.data);
        });
}

document.addEventListener('DOMContentLoaded', () => voiceCallApp.init());
</script>
@endsection
