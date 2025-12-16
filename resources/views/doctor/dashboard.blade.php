@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Welcome Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-8">
        <div class="flex flex-col md:flex-row items-center gap-8">

            <!-- Profile Photo -->
            <div class="relative group">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-50 shadow-md">
                    @if(Auth::user()->profile_photo_path)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-4xl font-bold uppercase">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    @endif
                </div>
                {{-- <div class="absolute bottom-2 right-2 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div> --}}
            </div>

            <!-- Welcome Text -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl font-bold text-slate-800 mb-2">
                    {{ __('Welcome back,') }} <span class="text-indigo-600">{{ Auth::user()->name }}</span>! 👋
                </h1>
                <div class="flex flex-col md:flex-row items-center gap-2 text-slate-500 mb-4 md:mb-0">
                    <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium">
                        {{ Auth::user()->doctorProfile?->speciality?->name ?? __('General Practitioner') }}
                    </span>
                    <span class="hidden md:inline text-slate-300">•</span>
                    <span class="text-sm">{{ __('Have a great day at work!') }}</span>
                </div>
            </div>

            <!-- Quick Action or Stat -->
            <div class="flex-shrink-0">
                <a href="{{ route('doctor.bookings.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all hover:shadow-md">
                    {{ __('View Schedule') }}
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Total Bookings Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">{{ __('Active') }}</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $bookingsCount }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Total Bookings') }}</p>
        </div>

        <!-- Placeholder Stats (Can be real later) -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">
                {{ $ratingAvg ? number_format($ratingAvg, 1) : 'N/A' }}
            </h3>
            <p class="text-slate-500 font-medium">{{ __('Average Rating') }}</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">${{ Auth::user()->doctorProfile->session_price ?? 0 }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Session Price') }}</p>
        </div>

    </div>

    <!-- Hints or Tips Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white">
        <div class="flex flex-col md:flex-row items-start justify-between gap-6">
            <div>
                <h3 class="text-xl font-bold mb-2">{{ __('Manage your Appointments efficiently!') }}</h3>
                <p class="text-indigo-100 max-w-xl">
                    {{ __('Keep track of your patient schedule, handle cancellations, and stay organized. Don\'t forget to check your messages for any urgent patient inquiries.') }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
