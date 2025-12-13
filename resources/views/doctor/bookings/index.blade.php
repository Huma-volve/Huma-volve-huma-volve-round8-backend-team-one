@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">{{ __('My Bookings') }}</h1>

        @if($bookings->isEmpty())
            <div class="bg-white rounded-lg shadow p-6 text-center text-slate-500">
                <p>{{ __('No bookings found.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($bookings as $booking)
                    <a href="{{ route('doctor.bookings.show', $booking) }}" class="block group">
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden border border-slate-100 hover:border-blue-500">
                            <div class="p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="relative">
                                        @if($booking->patient && $booking->patient->user && $booking->patient->user->profile_photo_path)
                                            <img class="h-12 w-12 rounded-full object-cover" src="{{ asset($booking->patient->user->profile_photo_path) }}" alt="{{ $booking->patient->user->name }}">
                                        @else
                                             <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                                {{ substr($booking->patient->user->name ?? 'P', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-800 group-hover:text-blue-600 transition-colors">
                                            {{ $booking->patient->user->name ?? __('Unknown Patient') }}
                                        </h3>
                                        <p class="text-sm text-slate-500">{{ __('Patient') }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center text-slate-600">
                                        <svg class="h-5 w-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $booking->appointment_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center text-slate-600">
                                        <svg class="h-5 w-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $booking->appointment_time->format('h:i A') }}</span>
                                    </div>
                                     <div class="flex items-center mt-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($booking->status == 'completed') bg-green-100 text-green-800
                                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                            @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
