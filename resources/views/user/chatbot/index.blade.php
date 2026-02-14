<?php
/**
 * Chatbot View
 * AI chatbot with OpenRouter API
 */
?>

@extends('layouts.user')

@section('title', 'AI Chatbot - Social Chat')

@section('content')
<div class="max-w-2xl mx-auto h-[calc(100vh-8rem)] flex flex-col">
    <div class="bg-white dark:bg-dark-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                <i class="fas fa-robot text-white"></i>
            </div>
            <div>
                <h2 class="font-semibold">Social Chat AI</h2>
                <p class="text-xs text-gray-500">AI Assistant (GPT-3.5)</p>
            </div>
        </div>
        
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chatbotMessages">
            <!-- Welcome Message -->
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-tl-md px-4 py-2 max-w-[80%]">
                    <p class="text-sm">Halo! Saya Social Chat AI Assistant. Saya bisa membantu menjawab pertanyaanmu tentang apapun. Coba tanya sesuatu!</p>
                </div>
            </div>
        </div>
        
        <!-- Input -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <form id="chatbotForm" class="flex gap-2">
                <input type="text" id="chatbotInput" placeholder="Type a message..."
                       class="flex-1 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 border-0 focus:ring-2 focus:ring-primary-500">
                <button type="submit" class="px-4 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function addMessage(content, isUser = false) {
    const container = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    div.className = `flex gap-3 ${isUser ? 'flex-row-reverse' : ''}`;
    div.innerHTML = isUser 
        ? `<div class="bg-primary-500 text-white rounded-2xl rounded-tr-md px-4 py-2 max-w-[80%]"><p class="text-sm">${content}</p></div>`
        : `<div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center flex-shrink-0"><i class="fas fa-robot text-white text-sm"></i></div><div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-tl-md px-4 py-2 max-w-[80%]"><p class="text-sm">${content}</p></div>`;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

document.getElementById('chatbotForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('chatbotInput');
    const message = input.value.trim();
    if (!message) return;
    
    addMessage(message, true);
    input.value = '';
    
    // Show loading
    addMessage('<i class="fas fa-spinner fa-spin"></i> Mengetik...');
    
        try {
            const response = await fetch('/api/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            
            // Remove loading message
            const container = document.getElementById('chatbotMessages');
            container.lastElementChild.remove();
            
            if (response.ok) {
                addMessage(data.reply);
            } else {
                addMessage('Error: ' + (data.reply || 'Terjadi kesalahan'));
            }
        } catch (error) {
            // Remove loading message
            const container = document.getElementById('chatbotMessages');
            container.lastElementChild.remove();
            
            console.error('Chat error:', error);
            addMessage('Maaf, terjadi kesalahan jaringan. Coba lagi nanti.');
        }
});
</script>
@endsection
