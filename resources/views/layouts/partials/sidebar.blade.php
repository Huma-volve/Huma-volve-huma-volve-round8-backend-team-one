    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 start-0 z-30 w-64 bg-white border-r {{ $isRtl ? 'border-l' : 'border-r' }} border-slate-200 transform transition-transform duration-300 lg:translate-x-0"
        :class="{ 'translate-x-0': sidebarOpen, '{{ $isRtl ? 'translate-x-full' : '-translate-x-full' }}': !sidebarOpen }">

        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-slate-100">
            <div class="flex items-center gap-2 text-primary-600 font-bold text-2xl">
                <i class="ph-fill ph-heartbeat"></i>
                <span>Huma<span class="text-slate-800">Volve</span></span>
            </div>
        </div>

        <!-- Scrollable Navigation -->
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
            <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                {{ $isRtl ? 'لوحة الطبيب' : 'Doctor Panel' }}
            </div>

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-slate-50 text-slate-600 hover:text-primary-600' }} transition-colors group">
                <i
                    class="ph ph-squares-four text-lg {{ request()->routeIs('dashboard') ? '' : 'group-hover:scale-110' }} transition-transform"></i>
                <span>{{ $isRtl ? 'لوحة التحكم' : 'Dashboard' }}</span>
            </a>

            <a href="{{ route('doctor.chat.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('doctor.chat.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-slate-50 text-slate-600 hover:text-primary-600' }} transition-colors group">
                <i
                    class="ph ph-chats-circle text-lg {{ request()->routeIs('doctor.chat.*') ? '' : 'group-hover:scale-110' }} transition-transform"></i>
                <span>{{ $isRtl ? 'الرسائل' : 'Messages' }}</span>
                <span
                    class="bg-primary-100 text-primary-700 py-0.5 px-2 rounded-full text-xs font-bold {{ $isRtl ? 'mr-auto' : 'ml-auto' }}">5</span>
            </a>

            <a href="{{ route('doctor.bookings.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('doctor.bookings.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-slate-50 text-slate-600 hover:text-primary-600' }} transition-colors group">
                <i
                    class="ph ph-calendar-check text-lg {{ request()->routeIs('doctor.bookings.*') ? '' : 'group-hover:scale-110' }} transition-transform"></i>
                <span>{{ $isRtl ? 'المواعيد' : 'Appointments' }}</span>
            </a>

        </nav>
    </aside>
