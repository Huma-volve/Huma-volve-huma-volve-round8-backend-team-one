<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Booking Information</h3>
                            <p class="text-sm text-gray-500">ID: #{{ $booking->id }}</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span
                                class="px-3 py-1 text-sm font-semibold rounded-full
                                @if ($booking->status === 'completed') bg-green-100 text-green-800
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Appointment Details</h4>
                            <div class="bg-gray-50 p-4 rounded-md space-y-2 text-sm">
                                <p><span class="text-gray-500 block">Date</span>
                                    {{ \Carbon\Carbon::parse($booking->appointment_date)->format('M d, Y') }}</p>
                                <p><span class="text-gray-500 block">Time</span>
                                    {{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}</p>
                                <p><span class="text-gray-500 block">Price</span>
                                    ${{ number_format($booking->price_at_booking, 2) }}</p>
                                <p><span class="text-gray-500 block">Payment Status</span>
                                    {{ ucfirst($booking->payment_status) }}</p>
                                @if ($booking->notes)
                                    <div class="mt-2">
                                        <span class="text-gray-500 block">Notes</span>
                                        <p class="text-gray-700">{{ $booking->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Doctor Information</h4>
                            <div class="bg-gray-50 p-4 rounded-md flex items-center space-x-4">
                                @if ($booking->doctor->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $booking->doctor->user->profile_photo_path) }}"
                                        class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <div
                                        class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold">
                                        {{ substr($booking->doctor->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $booking->doctor->user->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $booking->doctor->speciality->name ?? 'Specialist' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($booking->status === 'cancelled')
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <h4 class="font-medium text-red-700 mb-2">Cancellation Details</h4>
                            <div class="bg-red-50 p-4 rounded-md text-sm space-y-2">
                                <p><span class="text-gray-500 block">Cancelled By</span>
                                    {{ $booking->cancelled_by ?? 'N/A' }}</p>
                                <p><span class="text-gray-500 block">Reason</span>
                                    {{ $booking->cancellation_reason ?? 'No reason provided' }}</p>
                                <p><span class="text-gray-500 block">Cancelled At</span>
                                    {{ $booking->cancelled_at ? \Carbon\Carbon::parse($booking->cancelled_at)->format('M d, Y h:i A') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Patient Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Information</h3>
                    <div class="flex items-center mb-6">
                        @if ($booking->patient->profile_photo_path)
                            <img src="{{ asset('storage/' . $booking->patient->profile_photo_path) }}"
                                class="h-16 w-16 rounded-full object-cover mr-4">
                        @else
                            <div
                                class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center mr-4 text-gray-500 text-xl font-bold">
                                {{ substr($booking->patient->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h4 class="text-xl font-bold">{{ $booking->patient->name }}</h4>
                            <p class="text-gray-500">{{ $booking->patient->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <p class="text-gray-500">Phone</p>
                            <p class="font-medium">{{ $booking->patient->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Gender</p>
                            <p class="font-medium">{{ $booking->patient->patientProfile->gender ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Age</p>
                            <p class="font-medium">
                                @if (optional($booking->patient->patientProfile)->birthdate)
                                    {{ \Carbon\Carbon::parse($booking->patient->patientProfile->birthdate)->age }}
                                    years
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.patients.show', $booking->patient) }}"
                            class="text-indigo-600 hover:text-indigo-900 font-medium">
                            View Full Patient Profile &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
