<?php
/**
 * WebRTC Call View
 * Halaman untuk video/voice call menggunakan WebRTC
 */
?>

@extends('layouts.app')

@section('title', 'Call - Social Chat')

@section('content')
<div class="min-h-screen bg-dark-300 flex flex-col items-center justify-center"
     x-data="callApp()"
     x-init="init()">
    
    <!-- Call Container -->
    <div class="w-full max-w-2xl p-6">
        
        <!-- Caller Info -->
        <div class="text-center mb-8">
            <img :src="caller?.avatar || 'https://ui-avatars.com/api/?name=User'" 
                 class="w-24 h-24 rounded-full mx-auto mb-4 ring-4 ring-primary-500">
            <h2 class="text-2xl font-bold text-white" x-text="caller?.name || 'Calling...'"></h2>
            <p class="text-gray-400" x-text="status === 'calling' ? 'Calling...' : status === 'connected' ? 'Connected' : 'Ended'"></p>
        </div>

        <!-- Video Grid -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <!-- Remote Video -->
            <div class="relative aspect-video bg-dark-100 rounded-2xl overflow-hidden">
                <video x-ref="remoteVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <div class="absolute bottom-2 left-2 text-white text-sm bg-black/50 px-2 py-1 rounded">
                    <span x-text="caller?.name || 'Remote'"></span>
                </div>
            </div>
            
            <!-- Local Video -->
            <div class="relative aspect-video bg-dark-100 rounded-2xl overflow-hidden">
                <video x-ref="localVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                <div class="absolute bottom-2 left-2 text-white text-sm bg-black/50 px-2 py-1 rounded">Anda</div>
            </div>
        </div>

        <!-- Call Controls -->
        <div class="flex justify-center space-x-4">
            <!-- Mute -->
            <button @click="toggleMute()" 
                    class="p-4 rounded-full transition-all"
                    :class="isMuted ? 'bg-red-500' : 'bg-gray-700'">
                <i class="fas" :class="isMuted ? 'fa-microphone-slash' : 'fa-microphone'"></i>
            </button>
            
            <!-- Video -->
            <button @click="toggleVideo()" 
                    class="p-4 rounded-full transition-all"
                    :class="!isVideoEnabled ? 'bg-red-500' : 'bg-gray-700'">
                <i class="fas" :class="isVideoEnabled ? 'fa-video' : 'fa-video-slash'"></i>
            </button>
            
            <!-- End Call -->
            <button @click="endCall()" 
                    class="p-4 rounded-full bg-red-500 hover:bg-red-600 transition-all">
                <i class="fas fa-phone-slash"></i>
            </button>
        </div>

    </div>

</div>

<script>
function callApp() {
    return {
        chatId: {{ $chatId ?? 0 }},
        callType: '{{ $callType ?? 'video' }}',
        status: 'calling',
        caller: null,
        peerConnection: null,
        localStream: null,
        isMuted: false,
        isVideoEnabled: true,

        init() {
            this.startLocalStream();
            this.setupPeerConnection();
        },

        async startLocalStream() {
            try {
                this.localStream = await navigator.mediaDevices.getUserMedia({
                    video: this.callType === 'video',
                    audio: true
                });
                this.$refs.localVideo.srcObject = this.localStream;
            } catch (err) {
                console.error('Error accessing media devices:', err);
            }
        },

        setupPeerConnection() {
            const config = {
                iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
            };
            
            this.peerConnection = new RTCPeerConnection(config);

            // Add local stream to peer connection
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => {
                    this.peerConnection.addTrack(track, this.localStream);
                });
            }

            // Handle incoming stream
            this.peerConnection.ontrack = (event) => {
                this.$refs.remoteVideo.srcObject = event.streams[0];
                this.status = 'connected';
            };

            // Handle ICE candidates
            this.peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    this.sendSignal({ candidate: event.candidate });
                }
            };
        },

        async toggleMute() {
            this.isMuted = !this.isMuted;
            this.localStream.getAudioTracks().forEach(track => {
                track.enabled = !this.isMuted;
            });
        },

        toggleVideo() {
            this.isVideoEnabled = !this.isVideoEnabled;
            this.localStream.getVideoTracks().forEach(track => {
                track.enabled = this.isVideoEnabled;
            });
        },

        endCall() {
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => track.stop());
            }
            if (this.peerConnection) {
                this.peerConnection.close();
            }
            window.location.href = '/dashboard';
        },

        sendSignal(data) {
            // WebSocket or API call to send signaling data
            fetch('/api/call/signaling', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    target_user_id: this.receiverId,
                    signal: data
                })
            });
        }
    };
}
</script>
@endsection
