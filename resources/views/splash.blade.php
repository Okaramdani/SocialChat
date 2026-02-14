<?php
/**
 * Splash Screen - Super Premium Cinematic Edition
 */
?>

<!DOCTYPE html>
<html lang="id" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    loading: true,
    init() {
        if (localStorage.getItem('darkMode') === 'true' ||
           (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        setTimeout(() => {
            this.loading = false;
            setTimeout(() => {
                window.location.href = '{{ auth()->check() ? '/dashboard' : '/login' }}';
            }, 1200);
        }, 8000); // 8 detik cinematic
    }
}" x-init="init()">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Social Chat - Connect Smarter</title>

<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body { font-family: 'Inter', sans-serif; }

/* Animated gradient */
.splash-bg {
    background: linear-gradient(135deg,#0ea5e9,#8b5cf6,#ec4899);
    background-size: 300% 300%;
    animation: gradientMove 10s ease infinite;
}
@keyframes gradientMove {
    0% {background-position:0% 50%;}
    50% {background-position:100% 50%;}
    100% {background-position:0% 50%;}
}

/* Cinematic fade */
.fade-out {
    opacity:0;
    filter:blur(10px);
    transform:scale(1.05);
    transition:all 1.2s ease;
}

/* Logo reveal */
.logo-animate {
    animation: logoReveal 1.8s ease forwards;
}
@keyframes logoReveal {
    0% {opacity:0; transform:scale(0.6); filter:blur(15px);}
    100% {opacity:1; transform:scale(1); filter:blur(0);}
}

/* Loader */
.loader-ring {
    border:3px solid rgba(255,255,255,0.2);
    border-top:3px solid #fff;
    border-radius:50%;
    width:45px;
    height:45px;
    animation:spin 1s linear infinite;
}
@keyframes spin {
    to {transform:rotate(360deg);}
}

/* Progress */
.progress-fill {
    height:100%;
    background:linear-gradient(90deg,#fff,rgba(255,255,255,.7));
    animation:progress 8s ease-in-out;
}
@keyframes progress {
    from {width:0%;}
    to {width:100%;}
}

/* Shimmer name */
.creator-name {
    position:relative;
    color:white;
    font-weight:600;
    overflow:hidden;
}
.creator-name::after {
    content:"";
    position:absolute;
    top:0;
    left:-100%;
    width:100%;
    height:100%;
    background:linear-gradient(120deg,transparent,rgba(255,255,255,.6),transparent);
    animation:shimmer 3s infinite;
}
@keyframes shimmer {
    100% {left:100%;}
}

/* Floating glow particles */
.particle {
    position:absolute;
    border-radius:50%;
    background:rgba(255,255,255,0.08);
    filter:blur(40px);
    animation:float 12s infinite ease-in-out;
}
@keyframes float {
    0%,100% {transform:translateY(0);}
    50% {transform:translateY(-40px);}
}
</style>
</head>

<body class="splash-bg min-h-screen flex items-center justify-center overflow-hidden"
      :class="{'fade-out': !loading}">

<!-- Background Particles -->
<div class="particle w-64 h-64 top-20 left-20"></div>
<div class="particle w-72 h-72 bottom-20 right-20" style="animation-delay:3s;"></div>
<div class="particle w-48 h-48 top-1/2 left-1/3" style="animation-delay:6s;"></div>

<div class="text-center px-6" x-show="loading">

    <!-- Logo -->
    <div class="mb-10 logo-animate">
        <div class="w-28 h-28 mx-auto bg-white/20 backdrop-blur-xl rounded-3xl
                    flex items-center justify-center shadow-2xl">
            <i class="fas fa-comments text-6xl text-white"></i>
        </div>
    </div>

    <h1 class="text-5xl font-extrabold text-white tracking-tight">
        Social Chat
    </h1>

    <p class="text-white/70 mt-3 text-base">
        Connect Smarter
    </p>

    <p class="mt-4 text-xs uppercase tracking-widest text-white/50">
        Crafted by
    </p>
    <p class="creator-name text-sm mt-1">
        Oka Ramdani
    </p>

    <!-- Loader -->
    <div class="mt-10">
        <div class="loader-ring mx-auto mb-4"></div>

        <div class="w-56 mx-auto h-1 bg-white/20 rounded overflow-hidden">
            <div class="progress-fill"></div>
        </div>

        <p class="text-white/50 text-xs mt-4">Launching Experience...</p>
    </div>

</div>

</body>
</html>
