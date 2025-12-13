@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ showCancelModal: false, showRescheduleModal: false }">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('doctor.bookings.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('Back to Bookings') }}
        </a>
        <h1 class="text-2xl font-bold text-slate-800">{{ __('Booking Details') }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Patient Information -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 h-fit">
            <h2 class="text-xl font-semibold mb-4 text-slate-800">{{ __('Patient Information') }}</h2>
            <div class="flex flex-col items-center mb-6">
                @if($booking->patient && $booking->patient->user && $booking->patient->user->profile_photo_path)
                    <img class="h-24 w-24 rounded-full object-cover mb-4 shadow-sm" src="{{ asset($booking->patient->user->profile_photo_path) }}" alt="{{ $booking->patient->user->name }}">
                @else
                    <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-3xl mb-4">
                        {{ substr($booking->patient->user->name ?? 'P', 0, 1) }}
                    </div>
                @endif
                <h3 class="text-xl font-bold text-slate-900">{{ $booking->patient->user->name ?? __('Unknown') }}</h3>
                <p class="text-slate-500">{{ $booking->patient->user->email ?? '' }}</p>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase">{{ __('Phone') }}</p>
                    <p class="text-slate-700">{{ $booking->patient->user->phone ?? __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase">{{ __('Gender') }}</p>
                    <p class="text-slate-700">{{ ucfirst($booking->patient->gender ?? __('N/A')) }}</p>
                </div>
                 <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase">{{ __('Birthdate') }}</p>
                    <p class="text-slate-700">{{ $booking->patient->birthdate ? $booking->patient->birthdate->format('M d, Y') : __('N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase">{{ __('Age') }}</p>
                    <p class="text-slate-700">
                        @if($booking->patient && $booking->patient->birthdate)
                            {{ $booking->patient->birthdate->age }} {{ __('years') }}
                        @else
                            {{ __('N/A') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">{{ __('Appointment Details') }}</h2>
                    <span class="px-4 py-1.5 rounded-full text-sm font-medium
                        @if($booking->status == 'completed') bg-green-100 text-green-800
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                        @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ custom_ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">{{ __('Date') }}</p>
                        <div class="flex items-center text-slate-800 text-lg">
                            <svg class="h-5 w-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $booking->appointment_date->format('l, M d, Y') }}
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">{{ __('Time') }}</p>
                        <div class="flex items-center text-slate-800 text-lg">
                            <svg class="h-5 w-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $booking->appointment_time->format('h:i A') }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-slate-500 mb-1">{{ __('Notes') }}</p>
                        <p class="text-slate-700 bg-slate-50 p-4 rounded-lg">
                            {{ $booking->notes ?? __('No notes provided.') }}
                        </p>
                    </div>

                    <div class="md:col-span-2 border-t pt-4 mt-2">
                        <div class="flex justify-between items-center">
                             <div>
                                <p class="text-sm font-medium text-slate-500">{{ __('Payment Status') }}</p>
                                <p class="text-slate-800 font-medium">{{ custom_ucfirst($booking->payment_status) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500">{{ __('Amount') }}</p>
                                <p class="text-slate-800 font-bold text-lg">${{ number_format($booking->price_at_booking, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($booking->status == 'cancelled')
                <div class="bg-red-50 rounded-xl shadow-sm p-6 border border-red-100">
                    <h3 class="text-red-800 font-semibold mb-3">{{ __('Cancellation Details') }}</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium text-red-700">{{ __('Reason:') }}</span> {{ $booking->cancellation_reason }}</p>
                        <p><span class="font-medium text-red-700">{{ __('Cancelled At:') }}</span> {{ $booking->cancelled_at->format('M d, Y h:i A') }}</p>
                        <p><span class="font-medium text-red-700">{{ __('Cancelled By:') }}</span> {{ $booking->cancelledBy->name ?? 'N/A' }}</p>
                    </div>
                </div>
            @endif

            @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex flex-col md:flex-row gap-4">
                    <button @click="showRescheduleModal = true" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ __('Reschedule Appointment') }}
                    </button>
                    <button @click="showCancelModal = true" class="flex-1 bg-white border border-red-500 text-red-600 hover:bg-red-50 font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('Cancel Appointment') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Cancel Modal -->
    <div x-cloak x-show="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCancelModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showCancelModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('doctor.bookings.cancel', $booking) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">{{ __('Cancel Appointment') }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">{{ __('Are you sure you want to cancel this appointment? This action cannot be undone.') }}</p>
                                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">{{ __('Reason for Cancellation') }}</label>
                                    <textarea id="cancellation_reason" name="cancellation_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">{{ __('Confirm Cancellation') }}</button>
                        <button type="button" @click="showCancelModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">{{ __('Keep Booking') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div x-cloak x-show="showRescheduleModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRescheduleModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showRescheduleModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('doctor.bookings.reschedule', $booking) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">{{ __('Reschedule Appointment') }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">{{ __('Please select a new date and time for the appointment.') }}</p>

                                     <div class="mb-4">
                                        <label for="appointment_date" class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
                                        <input type="date" name="appointment_date" id="appointment_date" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    </div>

                                    <div class="mb-4">
                                        <label for="appointment_time" class="block text-sm font-medium text-gray-700">{{ __('Time') }}</label>
                                        <input type="time" name="appointment_time" id="appointment_time" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">{{ __('Confirm Rescheduling') }}</button>
                        <button type="button" @click="showRescheduleModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">{{ __('Cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@php
    function custom_ucfirst($string) {
        return ucfirst($string);
    }
@endphp
@endsection
