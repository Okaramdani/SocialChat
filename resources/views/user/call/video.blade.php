<?php
/**
 * Video Call View
 * Halaman video call dengan WebRTC
 */
?>

@extends('layouts.user')

@section('title', 'Video Call - Social Chat')

@section('content')
<div class="h-screen bg-dark-300 flex flex-col" x-data="callApp">
    <!-- Call Header -->
    <div class="bg-dark-100/80 backdrop-blur-sm p-4 flex items-center justify-between z-10">
        <div class="flex items-center gap-3">
            <img src="{{ $otherUser?->avatar_url ?? asset('images/default-avatar.png') }}" class="w-10 h-10 rounded-full">
            <div>
                <p class="font-semibold text-white">{{ $otherUser?->name ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-400" x-text="callStatus">Calling...</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="toggleMute()" class="p-3 rounded-full" :class="muted ? 'bg-red-500' : 'bg-gray-700'">
                <i class="fas" :class="muted ? 'fa-microphone-slash' : 'fa-microphone'"></i>
            </button>
            <button @click="toggleVideo()" class="p-3 rounded-full" :class="videoOff ? 'bg-red-500' : 'bg-gray-700'">
                <i class="fas" :class="videoOff ? 'fa-video-slash' : 'fa-video'"></i>
            </button>
            <button @click="endCall()" class="p-4 rounded-full bg-red-600 hover:bg-red-700">
                <i class="fas fa-phone-slash"></i>
            </button>
        </div>
    </div>
    
    <!-- Video Grid -->
    <div class="flex-1 relative">
        <!-- Remote Video -->
        <video id="remoteVideo" autoplay playsinline 
               class="w-full h-full object-cover"></video>
        
        <!-- Local Video (Picture-in-Picture) -->
        <div class="absolute bottom-4 right-4 w-48 h-36 rounded-xl overflow-hidden shadow-lg bg-gray-800">
            <video id="localVideo" autoplay playsinline muted
                   class="w-full h-full object-cover mirror"></video>
        </div>
        
        <!-- Call Controls -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-4">
            <button @click="toggleMute()" class="p-4 rounded-full bg-gray-700/80 hover:bg-gray-600">
                <i class="fas" :class="muted ? 'fa-microphone-slash' : 'fa-microphone'"></i>
            </button>
            <button @click="toggleVideo()" class="p-4 rounded-full bg-gray-700/80 hover:bg-gray-600">
                <i class="fas" :class="videoOff ? 'fa-video-slash' : 'fa-video'"></i>
            </button>
            <button @click="endCall()" class="p-4 rounded-full bg-red-600 hover:bg-red-700">
                <i class="fas fa-phone-slash"></i>
            </button>
        </div>
    </div>
</div>

<style>
.mirror { transform: scaleX(-1); }
</style>

<script>
const callApp = {
    callId: {{ $callLog?->id ?? 'null' }},
    chatId: {{ $chatId }},
    type: '{{ $type }}',
    peerConnection: null,
    localStream: null,
    muted: false,
    videoOff: false,
    callStatus: 'Connecting...',
    
    init() {
        this.initLocalStream();
        this.initWebRTC();
    },
    
    async initLocalStream() {
        try {
            this.localStream = await navigator.mediaDevices.getUserMedia({
                video: this.type === 'video',
                audio: true
            });
            document.getElementById('localVideo').srcObject = this.localStream;
        } catch (err) {
            console.error('Error accessing media devices:', err);
            this.callStatus = 'Camera access denied';
        }
    },
    
    initWebRTC() {
        const config = {
            iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
        };
        
        this.peerConnection = new RTCPeerConnection(config);
        
        // Add local tracks
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
        }
        
        // Handle remote stream
        this.peerConnection.ontrack = (event) => {
            document.getElementById('remoteVideo').srcObject = event.streams[0];
            this.callStatus = 'Connected';
        };
        
        // ICE candidates
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                this.sendSignal('ice-candidate', event.candidate);
            }
        };
        
        // Create offer if initiator
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
    
    toggleMute() {
        this.muted = !this.muted;
        this.localStream.getAudioTracks().forEach(track => track.enabled = !this.muted);
    },
    
    toggleVideo() {
        this.videoOff = !this.videoOff;
        this.localStream.getVideoTracks().forEach(track => track.enabled = !this.videoOff);
    },
    
    endCall() {
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
        }
        if (this.peerConnection) {
            this.peerConnection.close();
        }
        window.location.href = '/chat/' + this.chatId;
    }
};

// Listen for signaling events via Echo
// For caller: listen to own user channel
// For callee: will receive call notification via CallInitiated event
if (typeof Echo !== 'undefined') {
    // Listen for incoming signals (for both caller and callee)
    Echo.private('user.{{ auth()->id() }}')
        .listen('WebRTCSignalEvent', (event) => {
            console.log('Received signal:', event);
            const data = event.signal;
            if (data.type === 'offer') callApp.handleOffer(data.data);
            else if (data.type === 'answer') callApp.handleAnswer(data.data);
            else if (data.type === 'ice-candidate') callApp.handleIceCandidate(data.data);
        })
        .listen('CallInitiated', (event) => {
            console.log('Call initiated:', event);
            if (event.callLog.receiver_id === {{ auth()->id() }}) {
                // Incoming call - answer
                callApp.callId = event.callLog.id;
                callApp.callStatus = 'Incoming call...';
            }
        });
}

document.addEventListener('DOMContentLoaded', () => callApp.init());
</script>
@endsection
