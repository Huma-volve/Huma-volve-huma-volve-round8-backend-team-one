        <!-- Header -->
        <header class="h-16 sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200 px-4 flex items-center justify-between">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:text-slate-800 rounded-lg hover:bg-slate-100">
                <i class="ph ph-list text-2xl"></i>
            </button>

            <!-- Search -->
            <div class="hidden md:flex items-center w-64 lg:w-96 relative">
                <i class="ph ph-magnifying-glass absolute {{ $isRtl ? 'right-3' : 'left-3' }} text-slate-400"></i>
                <input type="text" placeholder="{{ $isRtl ? 'بحث في المرضى والمواعيد...' : 'Search patients, appointments, or policies...' }}" 
                       class="w-full {{ $isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2 rounded-full border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100 focus:border-primary-400 transition-all text-sm">
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                <!-- Language Toggle -->
                <a href="?lang={{ $isRtl ? 'en' : 'ar' }}" 
                   class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full hover:bg-slate-100 text-slate-600 transition-colors"
                   title="{{ $isRtl ? 'Switch to English' : 'التبديل للعربية' }}">
                    <span class="font-bold text-sm">{{ $isRtl ? 'EN' : 'ع' }}</span>
                </a>

                <!-- Notifications -->
                <button class="relative w-10 h-10 rounded-full hover:bg-slate-100 text-slate-600 transition-colors flex items-center justify-center">
                    <i class="ph ph-bell text-xl"></i>
                    <span class="absolute top-2 {{ $isRtl ? 'left-2' : 'right-2' }} w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 {{ $isRtl ? 'pr-2 pl-1' : 'pl-2 pr-1' }} py-1 rounded-full hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <img src="https://ui-avatars.com/api/?name=Doctor+Smith&background=0d9488&color=fff" alt="Avatar" class="w-8 h-8 rounded-full shadow-sm">
                        <div class="hidden md:block text-{{ $isRtl ? 'right' : 'left' }}">
                            <p class="text-sm font-medium text-slate-700 leading-none">{{ $isRtl ? 'د. سميث' : 'Dr. Smith' }}</p>
                            <p class="text-[10px] text-slate-500 font-medium">{{ $isRtl ? 'طبيب قلب' : 'Cardiologist' }}</p>
                        </div>
                        <i class="ph ph-caret-down text-slate-400 hidden md:block"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" x-cloak
                    class="absolute {{ $isRtl ? 'left-0' : 'right-0' }} mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1"
                    x-transition.origin.top.{{ $isRtl ? 'left' : 'right' }}>
                    
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary-600 text-{{ $isRtl ? 'right' : 'left' }}">
                        {{ $isRtl ? 'الملف الشخصي' : 'Profile' }}
                    </a>

                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary-600 text-{{ $isRtl ? 'right' : 'left' }}">
                        {{ $isRtl ? 'الإعدادات' : 'Settings' }}
                    </a>

                    <div class="border-t border-slate-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-{{ $isRtl ? 'right' : 'left' }} text-start">
                            {{ $isRtl ? 'تسجيل الخروج' : 'Sign Out' }}
                        </button>
                    </form>
                    </div>
                </div>
            </div>
        </header>
