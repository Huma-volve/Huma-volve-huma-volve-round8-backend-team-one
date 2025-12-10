@php
    $locale = request('lang', app()->getLocale());
    $isRtl = $locale == 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $dir }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800" x-data="{ sidebarOpen: false, darkMode: false }">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity 
         class="fixed inset-0 z-20 bg-black/50 lg:hidden" 
         @click="sidebarOpen = false"></div>

    @include('layouts.partials.sidebar')

    <!-- Main Content -->
    <div class="lg:{{ $isRtl ? 'mr-64' : 'ml-64' }} flex flex-col min-h-screen transition-all duration-300">
        
        @include('layouts.partials.header')

        <!-- Dynamic Content Area -->
        <main class="flex-1 p-4 lg:p-8">
            @hasSection('content')
                @yield('content')
            @else
                <!-- DUMMY CONTENT FOR PREVIEW (If no content yielded) -->
                
                <div class="space-y-8">
                    <!-- Welcome Section -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">{{ $isRtl ? 'صباح الخير، د. سميث! 👋' : 'Good Morning, Dr. Smith! 👋' }}</h1>
                            <p class="text-slate-500">{{ $isRtl ? 'إليك آخر المستجدات المتعلقة بمرضاك اليوم.' : 'Here\'s what\'s happening with your patients today.' }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 font-medium text-sm transition-colors shadow-sm">
                                {{ $isRtl ? 'عرض السياسات' : 'View Policies' }}
                            </button>
                            <button class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm shadow-md shadow-primary-200 transition-colors flex items-center gap-2">
                                <i class="ph ph-plus-circle text-lg"></i>
                                {{ $isRtl ? 'موعد جديد' : 'New Appointment' }}
                            </button>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Stat Card 1 -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                                    <i class="ph ph-users"></i>
                                </div>
                                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-800">1,234</h3>
                            <p class="text-slate-500 text-sm">{{ $isRtl ? 'إجمالي المرضى' : 'Total Patients' }}</p>
                        </div>

                        <!-- Stat Card 2 -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-2xl">
                                    <i class="ph ph-calendar-check"></i>
                                </div>
                                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">+5%</span>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-800">42</h3>
                            <p class="text-slate-500 text-sm">{{ $isRtl ? 'المواعيد القادمة' : 'Upcoming Appointments' }}</p>
                        </div>

                        <!-- Stat Card 3 -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl">
                                    <i class="ph ph-chats-circle"></i>
                                </div>
                                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">+28%</span>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-800">15</h3>
                            <p class="text-slate-500 text-sm">{{ $isRtl ? 'رسائل غير مقروءة' : 'Unread Messages' }}</p>
                        </div>

                        <!-- Stat Card 4 (Admin) -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl">
                                    <i class="ph ph-files"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-full">Admin</span>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-800">8</h3>
                            <p class="text-slate-500 text-sm">{{ $isRtl ? 'تحديثات السياسة المعلقة' : 'Pending Policy Updates' }}</p>
                        </div>
                    </div>

                    <!-- Split Section: Chat & Recent -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <!-- Chat Interface Preview (Doctor Panel Requirement) -->
                        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[500px]">
                            <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                    <i class="ph ph-chat-circle-text text-primary-600"></i>
                                    {{ $isRtl ? 'الاستشارة النشطة' : 'Active Consultation' }}
                                </h3>
                                <button class="text-sm text-primary-600 hover:underline">{{ $isRtl ? 'عرض الكل' : 'View All' }}</button>
                            </div>
                            
                            <!-- Chat Area -->
                            <div class="flex-1 p-4 bg-slate-50/50 overflow-y-auto space-y-4">
                                <!-- Patient Message -->
                                <div class="flex gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+J&background=random" class="w-8 h-8 rounded-full">
                                    <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 max-w-[80%]">
                                        <p class="text-sm text-slate-700">{{ $isRtl ? 'مرحبا دكتور، أشعر بالدوار قليلا بعد تناول الدواء الجديد.' : 'Hello Doctor, I\'ve been feeling a bit dizzy after taking the new medication.' }}</p>
                                        <span class="text-[10px] text-slate-400 mt-1 block">10:30 AM</span>
                                    </div>
                                </div>

                                <!-- Doctor Message -->
                                <div class="flex gap-3 flex-row-reverse">
                                    <img src="https://ui-avatars.com/api/?name=Dr+Smith&background=0d9488&color=fff" class="w-8 h-8 rounded-full">
                                    <div class="bg-primary-600 p-3 rounded-2xl rounded-tr-none shadow-md shadow-primary-100 max-w-[80%]">
                                        <p class="text-sm text-white">{{ $isRtl ? 'أهلا سارة. هل يحدث هذا مباشرة بعد تناوله؟' : 'Hi Sarah. Does this happen immediately after taking it? Or later in the day?' }}</p>
                                        <span class="text-[10px] text-primary-200 mt-1 block {{ $isRtl ? 'text-left' : 'text-right' }}">10:32 AM</span>
                                    </div>
                                </div>

                                <!-- Patient Photo -->
                                <div class="flex gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+J&background=random" class="w-8 h-8 rounded-full">
                                    <div class="bg-white p-2 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 max-w-[80%]">
                                        <div class="w-48 h-32 bg-slate-200 rounded-lg animate-pulse flex items-center justify-center text-slate-400 text-xs">
                                            [Sent Image]
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Area -->
                            <div class="p-4 border-t border-slate-100 bg-white rounded-b-2xl">
                                <div class="flex gap-2">
                                    <button class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-colors">
                                        <i class="ph ph-paperclip text-xl"></i>
                                    </button>
                                    <input type="text" placeholder="{{ $isRtl ? 'اكتب رسالتك...' : 'Type your message...' }}" class="flex-1 bg-slate-50 border-none rounded-full px-4 text-sm focus:ring-2 focus:ring-primary-100">
                                    <button class="p-2 text-white bg-primary-600 hover:bg-primary-700 rounded-full shadow-lg shadow-primary-200 transition-colors">
                                        <i class="ph ph-paper-plane-right {{ $isRtl ? 'transform rotate-180' : '' }} text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column (Admin / Notifications) -->
                        <div class="space-y-6">
                            <!-- Admin: Pending Content -->
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                    <i class="ph ph-shield-check text-purple-600"></i>
                                    {{ $isRtl ? 'تحديثات السياسة' : 'Policy Updates' }}
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3 pb-3 border-b border-slate-50 last:border-0 last:pb-0">
                                        <div class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center flex-shrink-0">
                                            <i class="ph ph-file-text"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700">{{ $isRtl ? 'سياسة الخصوصية v2.4' : 'Privacy Policy v2.4' }}</p>
                                            <p class="text-xs text-slate-500">{{ $isRtl ? 'بانتظار الموافقة' : 'Waiting for approval' }}</p>
                                        </div>
                                        <button class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-2 py-1 rounded-md transition-colors mr-auto {{ $isRtl ? 'mr-auto ml-0' : 'ml-auto' }}">{{ $isRtl ? 'مراجعة' : 'Review' }}</button>
                                    </div>
                                    <div class="flex items-start gap-3 pb-3 border-b border-slate-50 last:border-0 last:pb-0">
                                        <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                                            <i class="ph ph-check-circle"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700">{{ $isRtl ? 'شروط الخدمة' : 'Terms of Service' }}</p>
                                            <p class="text-xs text-slate-500">{{ $isRtl ? 'نشرت أمس' : 'Published yesterday' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- System Status -->
                            <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-6 rounded-2xl shadow-lg run-text-white text-white">
                                <h3 class="font-bold mb-2">{{ $isRtl ? 'حالة النظام' : 'System Status' }}</h3>
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                    <span class="text-sm text-slate-300">{{ $isRtl ? 'جميع الخدمات تعمل' : 'All services operational' }}</span>
                                </div>
                                <div class="w-full bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full w-[98%]"></div>
                                </div>
                                <div class="flex justify-between mt-2 text-xs text-slate-400">
                                    <span>Uptime</span>
                                    <span>99.9%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
        </main>

        @include('layouts.partials.footer')
    </div>
</body>
</html>
