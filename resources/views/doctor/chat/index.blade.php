@php
    $locale = request('lang', app()->getLocale());
    $isRtl = $locale === 'ar';
@endphp

@extends('layouts.app')

@section('content')
<div class="flex h-[calc(100vh-theme(spacing.32))] bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

    {{-- Sidebar --}}
    @include('doctor.chat.partials.sidebar')

    {{-- Chat Area --}}
    <main class="flex-1 flex flex-col" id="chatArea">

        {{-- Empty State --}}
        <div class="flex-1 flex flex-col items-center justify-center text-slate-400" id="emptyState">
            <i class="ph ph-chat-circle-text text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-slate-600 mb-1">
                {{ $isRtl ? 'اختر محادثة' : 'Select a conversation' }}
            </h3>
            <p class="text-sm">
                {{ $isRtl ? 'اختر محادثة من القائمة لبدء المراسلة' : 'Choose a conversation from the list to start messaging' }}
            </p>
        </div>

        {{-- Chat Container --}}
        <div class="flex-1 flex flex-col hidden" id="chatContainer">

            {{-- Chat Header --}}
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="" id="chatPatientAvatar" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <h3 class="font-semibold text-slate-800" id="chatPatientName"></h3>
                        <p class="text-xs text-green-500 flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            {{ $isRtl ? 'متصل الآن' : 'Online' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-colors">
                        <i class="ph ph-phone text-xl"></i>
                    </button>
                    <button class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-colors">
                        <i class="ph ph-video-camera text-xl"></i>
                    </button>
                    <button class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-colors">
                        <i class="ph ph-dots-three-vertical text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="flex-1 p-4 bg-slate-50/50 overflow-y-auto space-y-4" id="messagesContainer"></div>

            {{-- Input Area --}}
            <div class="p-4 border-t border-slate-100 bg-white">
                <form id="messageForm" class="flex items-center gap-2">
                    <input type="hidden" id="currentConversationId" value="">
                    <button type="button" id="attachmentBtn" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-colors">
                        <i class="ph ph-paperclip text-xl"></i>
                    </button>
                    <input type="file" id="attachmentInput" class="hidden" accept="image/*,video/*">
                    <input type="text" 
                           id="messageInput"
                           placeholder="{{ $isRtl ? 'اكتب رسالتك...' : 'Type your message...' }}" 
                           class="flex-1 bg-slate-50 border-none rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-primary-100"
                           autocomplete="off">
                    <button type="submit" class="p-2 text-white bg-primary-600 hover:bg-primary-700 rounded-full shadow-lg shadow-primary-200 transition-colors">
                        <i class="ph ph-paper-plane-right {{ $isRtl ? 'rotate-180' : '' }} text-xl"></i>
                    </button>
                </form>
            </div>

        </div>

    </main>

</div>

<style>
    .filter-tab.active {
        background-color: rgb(var(--color-primary-50));
        color: rgb(var(--color-primary-700));
    }
</style>
@endsection

@push('scripts')
@vite(['resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    const conversationItems = document.querySelectorAll('.conversation-item');
    const emptyState = document.getElementById('emptyState');
    const chatContainer = document.getElementById('chatContainer');
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const currentConversationId = document.getElementById('currentConversationId');
    const chatPatientName = document.getElementById('chatPatientName');
    const chatPatientAvatar = document.getElementById('chatPatientAvatar');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const attachmentBtn = document.getElementById('attachmentBtn');
    const attachmentInput = document.getElementById('attachmentInput');
    const isRtl = document.documentElement.dir === 'rtl';
    const baseUrl = '{{ request()->is("test-chat*") ? "/test-chat" : "/doctor/chat" }}';
    const doctorId = {{ auth()->id() ?? 2 }};

    let currentChannel = null;

    // Filter Tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            filterConversations(filter);
        });
    });

    function filterConversations(filter) {
        conversationItems.forEach(item => {
            const isUnread = item.dataset.unread === 'true';
            const isFavorite = item.dataset.favorite === 'true';
            
            if (filter === 'all') {
                item.style.display = 'block';
            } else if (filter === 'unread') {
                item.style.display = isUnread ? 'block' : 'none';
            } else if (filter === 'favorites') {
                item.style.display = isFavorite ? 'block' : 'none';
            }
        });
    }

    // Conversation Selection
    conversationItems.forEach(item => {
        item.addEventListener('click', function() {
            const conversationId = this.dataset.conversationId;
            const patientName = this.querySelector('h4').textContent;
            const patientAvatar = this.querySelector('img').src;

            conversationItems.forEach(i => i.classList.remove('bg-primary-50'));
            this.classList.add('bg-primary-50');

            currentConversationId.value = conversationId;
            chatPatientName.textContent = patientName;
            chatPatientAvatar.src = patientAvatar;

            emptyState.classList.add('hidden');
            chatContainer.classList.remove('hidden');

            loadMessages(conversationId);
            subscribeToChannel(conversationId);
            
            // Mark as read
            this.dataset.unread = 'false';
            const badge = this.querySelector('.unread-badge');
            if (badge) badge.classList.add('hidden');
        });
    });

    // Subscribe to Real-time Channel
    function subscribeToChannel(conversationId) {
        if (currentChannel) {
            window.Echo.leave(currentChannel);
        }

        currentChannel = `chat.${conversationId}`;
        
        window.Echo.private(currentChannel)
            .listen('MessageSent', (data) => {
                if (data.sender_id !== doctorId) {
                    appendMessage(data, false);
                    updateConversationPreview(conversationId, data.body);
                }
            });
    }

    // Load Messages
    async function loadMessages(conversationId) {
        messagesContainer.innerHTML = '<div class="flex justify-center py-8"><i class="ph ph-spinner animate-spin text-3xl text-primary-600"></i></div>';
        
        try {
            const response = await fetch(`${baseUrl}/${conversationId}/messages`);
            const data = await response.json();
            renderMessages(data.messages);
        } catch (error) {
            messagesContainer.innerHTML = `<p class="text-center text-red-500 py-8">${isRtl ? 'حدث خطأ في تحميل الرسائل' : 'Error loading messages'}</p>`;
        }
    }

    function renderMessages(messages) {
        messagesContainer.innerHTML = '';
        
        if (messages.length === 0) {
            messagesContainer.innerHTML = `<p class="text-center text-slate-400 py-8">${isRtl ? 'لا توجد رسائل بعد' : 'No messages yet'}</p>`;
            return;
        }

        messages.forEach(message => {
            appendMessage(message, message.is_mine);
        });

        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function appendMessage(message, isMine) {
        const messageHtml = isMine ? createSentMessage(message) : createReceivedMessage(message);
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function getMessageContent(message, textColorClass) {
        const type = message.type || 'text';
        switch (type) {
            case 'text':
                return `<p class="text-sm ${textColorClass}">${escapeHtml(message.body)}</p>`;
            case 'image':
                return `<img src="${message.body}" class="rounded-lg max-w-[200px]" alt="image" onerror="this.outerHTML='<p class=\\'text-sm ${textColorClass}\\'>[Image not available]</p>'">`;
            case 'video':
                return `<video src="${message.body}" class="rounded-lg max-w-[250px]" controls></video>`;
            case 'audio':
                return `<audio src="${message.body}" class="w-full max-w-[250px]" controls></audio>`;
            default:
                return `<a href="${message.body}" target="_blank" class="flex items-center gap-2 text-sm ${textColorClass} underline"><i class="ph ph-file"></i> Download File</a>`;
        }
    }

    function createSentMessage(message) {
        const avatarUrl = message.sender_avatar || 'https://ui-avatars.com/api/?name=Dr&background=0d9488&color=fff';
        const content = getMessageContent(message, 'text-white');
        const time = message.created_at.includes('T') 
            ? new Date(message.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
            : message.created_at;
            
        return `
            <div class="flex gap-3 flex-row-reverse">
                <img src="${avatarUrl}" class="w-8 h-8 rounded-full flex-shrink-0">
                <div class="bg-primary-600 p-3 rounded-2xl ${isRtl ? 'rounded-tl-none' : 'rounded-tr-none'} shadow-md shadow-primary-100 max-w-[70%]">
                    ${content}
                    <span class="text-[10px] text-primary-200 mt-1 block ${isRtl ? 'text-left' : 'text-right'}">${time}</span>
                </div>
            </div>
        `;
    }

    function createReceivedMessage(message) {
        const avatarUrl = message.sender_avatar || 'https://ui-avatars.com/api/?name=P&background=random';
        const content = getMessageContent(message, 'text-slate-700');
        const time = message.created_at.includes('T') 
            ? new Date(message.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
            : message.created_at;
            
        return `
            <div class="flex gap-3">
                <img src="${avatarUrl}" class="w-8 h-8 rounded-full flex-shrink-0">
                <div class="bg-white p-3 rounded-2xl ${isRtl ? 'rounded-tr-none' : 'rounded-tl-none'} shadow-sm border border-slate-100 max-w-[70%]">
                    ${content}
                    <span class="text-[10px] text-slate-400 mt-1 block">${time}</span>
                </div>
            </div>
        `;
    }

    function updateConversationPreview(conversationId, lastMessage) {
        const item = document.querySelector(`[data-conversation-id="${conversationId}"]`);
        if (item) {
            const preview = item.querySelector('.last-message');
            if (preview) preview.textContent = lastMessage;
            
            const time = item.querySelector('.conversation-time');
            if (time) time.textContent = 'now';
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Send Message
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const body = messageInput.value.trim();
        if (!body || !currentConversationId.value) return;

        const tempMessage = {
            id: Date.now(),
            body: body,
            type: 'text',
            is_mine: true,
            created_at: new Date().toISOString(),
            sender_avatar: null
        };

        appendMessage(tempMessage, true);
        messageInput.value = '';

        try {
            await fetch(`${baseUrl}/${currentConversationId.value}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ body })
            });
            
            updateConversationPreview(currentConversationId.value, body);
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });

    // File Attachment
    attachmentBtn.addEventListener('click', () => attachmentInput.click());
    
    attachmentInput.addEventListener('change', async function() {
        const file = this.files[0];
        if (!file || !currentConversationId.value) return;

        const formData = new FormData();
        formData.append('attachment', file);

        try {
            const response = await fetch(`${baseUrl}/${currentConversationId.value}/send`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            appendMessage(data.message, true);
        } catch (error) {
            console.error('Error uploading file:', error);
        }

        this.value = '';
    });

    // Search
    const searchInput = document.getElementById('searchConversations');
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        conversationItems.forEach(item => {
            const name = item.querySelector('h4').textContent.toLowerCase();
            item.style.display = name.includes(query) ? 'block' : 'none';
        });
    });
});
</script>
@endpush