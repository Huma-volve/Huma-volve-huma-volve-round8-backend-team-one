    {{-- Chat Area --}}
    <main class="flex-1 flex flex-col h-full overflow-hidden relative" id="chatArea">

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
        <div class="flex flex-col hidden h-full relative overflow-hidden bg-slate-50" id="chatContainer">

            {{-- Chat Header --}}
            <div class="p-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white z-10">
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
            <div class="p-4 border-t border-slate-100 bg-white shrink-0 z-10">
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