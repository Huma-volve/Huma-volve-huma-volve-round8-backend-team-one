    {{-- Conversations Sidebar --}}
    <aside class="w-80 border-{{ $isRtl ? 'l' : 'r' }} border-slate-100 flex flex-col">

        {{-- Sidebar Header --}}
        <div class="p-4 border-b border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="ph ph-chats-circle text-primary-600"></i>
                    {{ $isRtl ? 'المحادثات' : 'Conversations' }}
                </h2>
                <span class="bg-primary-100 text-primary-700 py-0.5 px-2 rounded-full text-xs font-bold" id="conversationsCount">
                    {{ $conversations->count() }}
                </span>
            </div>
            <div class="relative">
                <input type="text" 
                       id="searchConversations"
                       placeholder="{{ $isRtl ? 'بحث في المحادثات...' : 'Search conversations...' }}" 
                       class="w-full bg-slate-50 border-none rounded-lg {{ $isRtl ? 'pr-4 pl-10' : 'pl-4 pr-10' }} py-2 text-sm focus:ring-2 focus:ring-primary-100">
                <i class="ph ph-magnifying-glass absolute {{ $isRtl ? 'left-3' : 'right-3' }} top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex gap-1 p-2 border-b border-slate-100">
            <button class="filter-tab active flex-1 px-3 py-1.5 text-xs font-medium rounded-lg" data-filter="all">
                {{ $isRtl ? 'الكل' : 'All' }}
            </button>
            <button class="filter-tab flex-1 px-3 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-slate-50" data-filter="unread">
                {{ $isRtl ? 'غير مقروء' : 'Unread' }}
            </button>
            <button class="filter-tab flex-1 px-3 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-slate-50" data-filter="favorites">
                {{ $isRtl ? 'المفضلة' : 'Favorites' }}
            </button>
            <button class="filter-tab flex-1 px-3 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-slate-50" data-filter="archived">
                {{ $isRtl ? 'الأرشيف' : 'Archived' }}
            </button>
        </div>

        {{-- Conversations List --}}
        <div class="flex-1 overflow-y-auto" id="conversationsList">
            @forelse($conversations as $conversation)
                <div class="conversation-item p-3 border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition-colors"
                     data-conversation-id="{{ $conversation['id'] }}"
                     data-unread="{{ $conversation['unread_count'] > 0 ? 'true' : 'false' }}"
                     data-favorite="{{ $conversation['is_favorite'] ? 'true' : 'false' }}"
                     data-archived="{{ $conversation['is_archived'] ? 'true' : 'false' }}">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img src="{{ $conversation['patient']?->profile_photo_path 
                                ? asset('storage/' . $conversation['patient']->profile_photo_path) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($conversation['patient']?->name ?? 'U') . '&background=random' }}" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <span class="unread-badge absolute -top-1 {{ $isRtl ? '-left-1' : '-right-1' }} w-5 h-5 bg-primary-600 text-white text-xs rounded-full flex items-center justify-center font-bold {{ $conversation['unread_count'] > 0 ? '' : 'hidden' }}">
                                {{ $conversation['unread_count'] }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-semibold text-slate-800 truncate">
                                    {{ $conversation['patient']?->name ?? ($isRtl ? 'مستخدم' : 'User') }}
                                </h4>
                                <span class="text-xs text-slate-400 conversation-time">
                                    {{ $conversation['updated_at']?->diffForHumans(short: true) }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 truncate last-message">
                                {{ $conversation['last_message']?->body ?? ($isRtl ? 'لا توجد رسائل' : 'No messages yet') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full text-slate-400 p-8">
                    <i class="ph ph-chats-circle text-5xl mb-3"></i>
                    <p class="text-sm">{{ $isRtl ? 'لا توجد محادثات' : 'No conversations yet' }}</p>
                </div>
            @endforelse
        </div>

    </aside>