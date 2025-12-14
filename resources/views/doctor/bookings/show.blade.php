@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Link -->
    <a href="{{ route('doctor.bookings.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6 font-medium">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        {{ __('Back to Bookings') }}
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content: Patient & Booking Details -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Patient Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">{{ __('Patient Information') }}</h3>
                <div class="flex items-start">
                    <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-3xl mr-6 uppercase flex-shrink-0">
                         {{ substr($booking->patient?->user?->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Name') }}</label>
                            <p class="text-gray-800 font-medium">{{ $booking->patient?->user?->name ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Email') }}</label>
                            <p class="text-gray-800">{{ $booking->patient?->user?->email ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Phone') }}</label>
                            <p class="text-gray-800">{{ $booking->patient?->user?->phone ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Gender') }}</label>
                            <p class="text-gray-800 capitalize">{{ $booking->patient?->gender ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Birthdate') }}</label>
                            <p class="text-gray-800">
                                {{ $booking->patient?->birthdate ? $booking->patient->birthdate->format('M d, Y') : __('N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Age') }}</label>
                            <p class="text-gray-800">
                                {{ $booking->patient?->birthdate ? $booking->patient->birthdate->age . ' ' . __('Years') : __('N/A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
                 <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">{{ __('Booking Information') }}</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Status') }}</label>
                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' :
                              ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                              ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    <div>
                         <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Payment Status') }}</label>
                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($booking->payment_status ?? 'Pending') }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Date') }}</label>
                        <p class="text-gray-800 font-medium">{{ $booking->appointment_date->format('l, F j, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Time') }}</label>
                        <p class="text-gray-800 font-medium">{{ $booking->appointment_time->format('h:i A') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-400 uppercase font-bold">{{ __('Notes') }}</label>
                        <p class="text-gray-800 bg-gray-50 p-3 rounded-md mt-1">{{ $booking->notes ?? __('No notes provided.') }}</p>
                    </div>

                    @if($booking->status === 'cancelled')
                        <div class="md:col-span-2 bg-red-50 p-4 rounded-md border border-red-100 mt-2">
                            <h4 class="font-bold text-red-800 mb-2">{{ __('Cancellation Details') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-red-400 uppercase font-bold">{{ __('Cancelled At') }}</label>
                                    <p class="text-red-900 text-sm">{{ $booking->cancelled_at ? $booking->cancelled_at->format('M d, Y h:i A') : '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-red-400 uppercase font-bold">{{ __('Cancelled By') }}</label>
                                    <p class="text-red-900 text-sm">{{ $booking->cancelledBy?->name ?? 'System/User' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                     <label class="block text-xs text-red-400 uppercase font-bold">{{ __('Reason') }}</label>
                                     <p class="text-red-900 text-sm italic">"{{ $booking->cancellation_reason }}"</p>
                                </div>
                            </div>
                        </div>
                    @endif
                 </div>
            </div>
        </div>

        <!-- Sidebar: Actions -->
        <div class="space-y-6">
            @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                <!-- Actions Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('Actions') }}</h3>

                    <div x-data="{ showCancel: false, showReschedule: false }">
                        <div class="space-y-3">
                            <!-- Reschedule Button -->
                            <button @click="showReschedule = !showReschedule; showCancel = false"
                                    class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Reschedule Booking') }}
                            </button>

                            <!-- Cancel Button -->
                            <button @click="showCancel = !showCancel; showReschedule = false"
                                    class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                {{ __('Cancel Booking') }}
                            </button>
                        </div>

                        <!-- Reschedule Form -->
                        <div x-show="showReschedule" x-transition class="mt-4 pt-4 border-t">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Select New Appointment') }}</h4>
                            <form action="{{ route('doctor.bookings.reschedule', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Available Slots') }}</label>
                                    <select name="appointment_data" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required x-on:change="$refs.dateInput.value = $event.target.options[$event.target.selectedIndex].dataset.date; $refs.timeInput.value = $event.target.options[$event.target.selectedIndex].dataset.time">
                                        <option value="">{{ __('Choose a slot...') }}</option>
                                        @foreach($slots as $slot)
                                            <option value="{{ $slot['date'] }}|{{ $slot['start_time'] }}"
                                                    data-date="{{ $slot['date'] }}"
                                                    data-time="{{ $slot['start_time'] }}">
                                                {{ $slot['day_name'] }}, {{ $slot['date'] }} at {{ $slot['start_time'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <!-- Hidden inputs to split the value in controller if needed, or handle splitting in controller -->
                                    <!-- Wait, controller expects appointment_date and appointment_time separated -->
                                    <!-- I'll use JS to populate hidden fields or parsing in controller.
                                         The controller implementation I saw:
                                         $request->validate([ 'appointment_date' => ..., 'appointment_time' => ... ]);
                                         So I need to send them separately.
                                    -->
                                    <input type="hidden" name="appointment_date" x-ref="dateInput">
                                    <input type="hidden" name="appointment_time" x-ref="timeInput">
                                </div>

                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                    {{ __('Confirm Reschedule') }}
                                </button>
                            </form>
                        </div>

                        <!-- Cancel Form -->
                        <div x-show="showCancel" x-transition class="mt-4 pt-4 border-t">
                             <h4 class="font-medium text-red-900 mb-2">{{ __('Cancel Booking') }}</h4>
                             <form action="{{ route('doctor.bookings.cancel', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="reason" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Reason for Cancellation') }}</label>
                                    <textarea id="reason" name="cancellation_reason" rows="3" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Enter reason..." required></textarea>
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    {{ __('Confirm Cancellation') }}
                                </button>
                             </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
