<div>
<div x-data="{ open: false }" class="relative">
    <!-- زر النوتيفيكيشن -->
    <button @click="open = !open" class="relative flex items-center p-2 rounded-full hover:bg-slate-100 transition">
        <i class="ph ph-bell text-xl text-slate-600"></i>
        @if($notifications->whereNull('read_at')->count() > 0)
            <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span>
        @endif
    </button>

    <!-- قائمة النوتيفيكيشن -->
    <div x-show="open" x-cloak
         x-on:click.away="$wire.call('markAsRead'); open = false"
         class="absolute mt-3 right-4 w-80 bg-white rounded-2xl shadow-lg border border-slate-200 py-3 z-50 overflow-hidden">
         
        @forelse($notifications as $notification)
            @php $Unread = is_null($notification->read_at); @endphp
            <div class="flex items-start px-4 py-2 text-sm hover:bg-slate-50 cursor-pointer transition">
                <!-- الدائرة الزرقا -->
                @if($Unread)
                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full mr-2 mt-1 animate-pulse"></span>
                @else
                    <span class="w-2.5 h-2.5 mr-2"></span>
                @endif

                <!-- المحتوى -->
                <div class="flex flex-col">
                    <span class="{{ $Unread ? 'font-bold' : 'font-semibold' }}">
                        {{ $notification->data['type'] ?? 'No type' }}
                    </span>
                    <span class="{{ $Unread ? 'font-bold' : 'font-normal' }} text-slate-600">
                        {{ $notification->data['message'] ?? 'No message' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="px-4 py-2 text-sm text-slate-400 text-center">
                No notifications
            </div>
        @endforelse
    </div>
</div>
</div>
