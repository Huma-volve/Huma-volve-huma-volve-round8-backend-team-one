export function initDoctorChat(config) {
    const {
        baseUrl,
        doctorId,
        isRtl
    } = config;

    const elements = {
        conversationItems: document.querySelectorAll('.conversation-item'),
        emptyState: document.getElementById('emptyState'),
        chatContainer: document.getElementById('chatContainer'),
        messagesContainer: document.getElementById('messagesContainer'),
        messageForm: document.getElementById('messageForm'),
        messageInput: document.getElementById('messageInput'),
        currentConversationId: document.getElementById('currentConversationId'),
        chatPatientName: document.getElementById('chatPatientName'),
        chatPatientAvatar: document.getElementById('chatPatientAvatar'),
        filterTabs: document.querySelectorAll('.filter-tab'),
        attachmentBtn: document.getElementById('attachmentBtn'),
        attachmentInput: document.getElementById('attachmentInput'),
        searchInput: document.getElementById('searchConversations'),
        voiceBtn: document.getElementById('voiceBtn'),
        voiceIcon: document.getElementById('voiceIcon'),
        recordingIndicator: document.getElementById('recordingIndicator')
    };

    let currentChannel = null;

    function init() {
        setupFilterTabs();
        setupConversationSelection();
        setupMessageForm();
        setupAttachment();
        setupSearch();
        setupVoiceRecording();
    }

    function setupFilterTabs() {
        elements.filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                elements.filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                filterConversations(this.dataset.filter);
            });
        });
    }

    function filterConversations(filter) {
        elements.conversationItems.forEach(item => {
            const isUnread = item.dataset.unread === 'true';
            const isFavorite = item.dataset.favorite === 'true';
            const isArchived = item.dataset.archived === 'true';

            let show = false;
            switch (filter) {
                case 'all':
                    show = !isArchived;
                    break;
                case 'unread':
                    show = isUnread && !isArchived;
                    break;
                case 'favorites':
                    show = isFavorite && !isArchived;
                    break;
                case 'archived':
                    show = isArchived;
                    break;
            }
            item.style.display = show ? 'block' : 'none';
        });
    }

    function setupConversationSelection() {
        elements.conversationItems.forEach(item => {
            item.addEventListener('click', function() {
                selectConversation(this);
            });

            item.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                showContextMenu(e, this);
            });
        });
    }

    function selectConversation(item) {
        const conversationId = item.dataset.conversationId;
        const patientName = item.querySelector('h4').textContent;
        const patientAvatar = item.querySelector('img').src;

        elements.conversationItems.forEach(i => i.classList.remove('bg-primary-50'));
        item.classList.add('bg-primary-50');

        elements.currentConversationId.value = conversationId;
        elements.chatPatientName.textContent = patientName;
        elements.chatPatientAvatar.src = patientAvatar;

        elements.emptyState.classList.add('hidden');
        elements.chatContainer.classList.remove('hidden');

        window.dispatchEvent(new CustomEvent('open-chat'));

        loadMessages(conversationId);
        subscribeToChannel(conversationId);
        markConversationAsRead(conversationId);

        item.dataset.unread = 'false';
        const badge = item.querySelector('.unread-badge');
        if (badge) badge.classList.add('hidden');
    }

    async function markConversationAsRead(conversationId) {
        try {
            await fetch(`${baseUrl}/${conversationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            console.error('Error marking conversation as read:', error);
        }
    }

    function showContextMenu(e, item) {
        const existingMenu = document.getElementById('contextMenu');
        if (existingMenu) existingMenu.remove();

        const conversationId = item.dataset.conversationId;
        const isFavorite = item.dataset.favorite === 'true';
        const isArchived = item.dataset.archived === 'true';

        const menu = document.createElement('div');
        menu.id = 'contextMenu';
        menu.className = 'fixed bg-white rounded-lg shadow-lg border border-slate-200 py-2 z-50 min-w-[160px]';
        menu.style.left = `${e.pageX}px`;
        menu.style.top = `${e.pageY}px`;

        menu.innerHTML = `
            <button class="context-btn w-full px-4 py-2 text-sm text-${isRtl ? 'right' : 'left'} hover:bg-slate-50 flex items-center gap-2" data-action="favorite">
                <i class="ph ph-${isFavorite ? 'star-fill text-yellow-500' : 'star'}"></i>
                ${isFavorite ? (isRtl ? 'إزالة من المفضلة' : 'Remove from Favorites') : (isRtl ? 'إضافة للمفضلة' : 'Add to Favorites')}
            </button>
            <button class="context-btn w-full px-4 py-2 text-sm text-${isRtl ? 'right' : 'left'} hover:bg-slate-50 flex items-center gap-2" data-action="archive">
                <i class="ph ph-${isArchived ? 'archive-tray' : 'archive'}"></i>
                ${isArchived ? (isRtl ? 'إلغاء الأرشفة' : 'Unarchive') : (isRtl ? 'أرشفة' : 'Archive')}
            </button>
        `;

        document.body.appendChild(menu);

        menu.querySelectorAll('.context-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                handleContextAction(btn.dataset.action, conversationId, item);
                menu.remove();
            });
        });

        document.addEventListener('click', () => menu.remove(), { once: true });
    }

    async function handleContextAction(action, conversationId, item) {
        const endpoint = `${baseUrl}/${conversationId}/${action === 'favorite' ? 'toggle-favorite' : 'toggle-archive'}`;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                if (action === 'favorite') {
                    const current = item.dataset.favorite === 'true';
                    item.dataset.favorite = (!current).toString();
                } else {
                    const current = item.dataset.archived === 'true';
                    item.dataset.archived = (!current).toString();
                    if (!current) item.style.display = 'none';
                }
            }
        } catch (error) {
            console.error(`Error toggling ${action}:`, error);
        }
    }

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

    async function loadMessages(conversationId) {
        elements.messagesContainer.innerHTML = '<div class="flex justify-center py-8"><i class="ph ph-spinner animate-spin text-3xl text-primary-600"></i></div>';

        try {
            const response = await fetch(`${baseUrl}/${conversationId}/messages`);
            const data = await response.json();
            renderMessages(data.messages);
        } catch (error) {
            elements.messagesContainer.innerHTML = `<p class="text-center text-red-500 py-8">${isRtl ? 'حدث خطأ في تحميل الرسائل' : 'Error loading messages'}</p>`;
        }
    }

    function renderMessages(messages) {
        elements.messagesContainer.innerHTML = '';

        if (messages.length === 0) {
            elements.messagesContainer.innerHTML = `<p class="text-center text-slate-400 py-8">${isRtl ? 'لا توجد رسائل بعد' : 'No messages yet'}</p>`;
            return;
        }

        messages.forEach(message => {
            appendMessage(message, message.is_mine);
        });

        elements.messagesContainer.scrollTop = elements.messagesContainer.scrollHeight;
    }

    function appendMessage(message, isMine) {
        const messageHtml = isMine ? createSentMessage(message) : createReceivedMessage(message);
        elements.messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        elements.messagesContainer.scrollTop = elements.messagesContainer.scrollHeight;
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
        const time = formatTime(message.created_at);

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
        const time = formatTime(message.created_at);

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

    function formatTime(timestamp) {
        if (timestamp.includes('T')) {
            return new Date(timestamp).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
        return timestamp;
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

    function setupMessageForm() {
        elements.messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const body = elements.messageInput.value.trim();
            if (!body || !elements.currentConversationId.value) return;

            const tempMessage = {
                id: Date.now(),
                body: body,
                type: 'text',
                is_mine: true,
                created_at: new Date().toISOString(),
                sender_avatar: null
            };

            appendMessage(tempMessage, true);
            elements.messageInput.value = '';

            try {
                await fetch(`${baseUrl}/${elements.currentConversationId.value}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ body })
                });

                updateConversationPreview(elements.currentConversationId.value, body);
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });
    }

    function setupAttachment() {
        elements.attachmentBtn.addEventListener('click', () => elements.attachmentInput.click());

        elements.attachmentInput.addEventListener('change', async function() {
            const file = this.files[0];
            if (!file || !elements.currentConversationId.value) return;

            const formData = new FormData();
            formData.append('attachment', file);

            try {
                const response = await fetch(`${baseUrl}/${elements.currentConversationId.value}/send`, {
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
    }

    function setupSearch() {
        elements.searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            elements.conversationItems.forEach(item => {
                const name = item.querySelector('h4').textContent.toLowerCase();
                item.style.display = name.includes(query) ? 'block' : 'none';
            });
        });
    }

    function setupVoiceRecording() {
        if (!elements.voiceBtn) return;

        let mediaRecorder = null;
        let audioChunks = [];
        let isRecording = false;

        elements.voiceBtn.addEventListener('click', async function() {
            if (!elements.currentConversationId.value) {
                alert(isRtl ? 'الرجاء اختيار محادثة أولاً' : 'Please select a conversation first');
                return;
            }

            if (!isRecording) {
                // Start recording
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];

                    mediaRecorder.ondataavailable = (event) => {
                        audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = async () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        stream.getTracks().forEach(track => track.stop());
                        await sendVoiceMessage(audioBlob);
                    };

                    mediaRecorder.start();
                    isRecording = true;
                    updateRecordingUI(true);
                } catch (err) {
                    console.error('Error accessing microphone:', err);
                    alert(isRtl ? 'لا يمكن الوصول إلى الميكروفون' : 'Cannot access microphone');
                }
            } else {
                // Stop recording
                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }
                isRecording = false;
                updateRecordingUI(false);
            }
        });

        function updateRecordingUI(recording) {
            if (recording) {
                elements.voiceIcon.classList.remove('ph-microphone');
                elements.voiceIcon.classList.add('ph-stop-circle');
                elements.voiceBtn.classList.add('text-red-500');
                elements.recordingIndicator.classList.remove('hidden');
            } else {
                elements.voiceIcon.classList.remove('ph-stop-circle');
                elements.voiceIcon.classList.add('ph-microphone');
                elements.voiceBtn.classList.remove('text-red-500');
                elements.recordingIndicator.classList.add('hidden');
            }
        }

        async function sendVoiceMessage(audioBlob) {
            const formData = new FormData();
            formData.append('attachment', audioBlob, 'voice-message.webm');

            try {
                const response = await fetch(`${baseUrl}/${elements.currentConversationId.value}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (response.ok) {
                    appendMessage(data.message, true);
                } else {
                    console.error('Error sending voice message:', data);
                    alert(isRtl ? 'فشل إرسال الرسالة الصوتية' : 'Failed to send voice message');
                }
            } catch (error) {
                console.error('Error sending voice message:', error);
                alert(isRtl ? 'فشل إرسال الرسالة الصوتية' : 'Failed to send voice message');
            }
        }
    }

    init();
}