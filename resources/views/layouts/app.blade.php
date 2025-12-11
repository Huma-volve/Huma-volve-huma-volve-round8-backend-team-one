@php
    $locale = request('lang', app()->getLocale());
    $isRtl = $locale == 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $dir }}">
<head>
    @include('layouts.partials.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>
@stack('scripts')
</body>
</html>
