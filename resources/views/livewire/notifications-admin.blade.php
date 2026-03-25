<div>
<div x-data="{ open: false }" class="relative">
    <button 
        @click="open = !open" 
        class="relative flex items-center p-2 rounded-full hover:bg-slate-100 transition">
    <i class="ph ph-bell text-xl text-slate-600"></i>
        
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                fill="none" stroke="#4B5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 01-3.46 0"/>
        </svg>

         @if($notifications->whereNull('read_at')->count() > 0)
            <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span>
        @endif
        @if($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs font-bold
                        w-5 h-5 flex items-center justify-center rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-ref="dropdown" x-cloak
        @click.away="
        open = false;
        @this.markRead();"
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-lg border border-slate-200 py-3 z-50 max-h-96 overflow-y-auto">   @forelse($notifications as $notification)
            @php $Unread = is_null($notification->read_at); @endphp
            <div class="flex items-start px-4 py-2 text-sm hover:bg-gray-50 cursor-pointer transition">
                @if($Unread)
                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full mr-2 mt-1 animate-pulse"></span>
                @else
                    <span class="w-2.5 h-2.5 mr-2"></span>
                @endif

                <div class="flex flex-col">
                    <span class="{{ $Unread ? 'font-bold' : 'font-semibold' }}">
                        {{ $notification->data['type'] ?? 'No type' }}
                    </span>
                    <span class="{{ $Unread ? 'font-bold' : 'font-normal' }} text-gray-600">
                        {{ $notification->data['message'] ?? 'No message' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="px-4 py-2 text-sm text-gray-400 text-center">
                No notifications
            </div>
        @endforelse
                {{-- Show All Button --}}
        <div class="border-t mt-2 pt-2 px-4">
            <a
                href="{{ route('admin.notifications.index') }}"
                class="block text-center text-sm font-semibold
                        text-blue-600 hover:text-blue-800 transition">
                Show all notifications
            </a>
        </div>
    </div>
</div>
</div>
