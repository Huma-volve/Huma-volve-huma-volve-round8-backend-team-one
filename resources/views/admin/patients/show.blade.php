<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patient Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Patient Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div class="flex items-center">
                            @if ($patient->profile_photo_path)
                                <img src="{{ asset('storage/' . $patient->profile_photo_path) }}" alt="{{ $patient->name }}"
                                    class="h-20 w-20 rounded-full object-cover mr-6">
                            @else
                                <div
                                    class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center mr-6 text-gray-500 text-2xl uppercase font-bold">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-2xl font-bold">{{ $patient->name }}</h3>
                                <p class="text-gray-500">{{ $patient->email }}</p>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <form method="POST" action="{{ route('admin.patients.toggle-block', $patient) }}">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 rounded-md font-semibold text-white uppercase tracking-widest text-xs transition duration-150 ease-in-out {{ $patient->is_blocked ? 'bg-green-600 hover:bg-green-700 focus:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:bg-red-700 focus:ring-red-500' }}">
                                    {{ $patient->is_blocked ? 'Unblock Patient' : 'Block Patient' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                        <div>
                            <p class="text-gray-500">Phone</p>
                            <p class="font-medium">{{ $patient->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Gender</p>
                            <p class="font-medium">{{ $patient->patientProfile->gender ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Birthdate</p>
                            <p class="font-medium">{{ $patient->patientProfile->birthdate ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Age</p>
                            <p class="font-medium">
                                @if (optional($patient->patientProfile)->birthdate)
                                    {{ \Carbon\Carbon::parse($patient->patientProfile->birthdate)->age }} years
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Member Since</p>
                            <p class="font-medium">{{ $patient->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Bookings -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Booking History</h3>
                @if ($bookings->isEmpty())
                    <p class="text-gray-500">No bookings found for this patient.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($bookings as $booking)
                            <a href="{{ route('admin.bookings.show', $booking) }}"
                                class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-2">
                                        <span
                                            class="text-sm font-medium text-gray-500">{{ $booking->created_at->format('M d, Y') }}</span>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if ($booking->status === 'completed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                            @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-900">
                                        <p><span class="font-medium">Date:</span>
                                            {{ \Carbon\Carbon::parse($booking->appointment_date)->format('M d, Y') }}
                                        </p>
                                        <p><span class="font-medium">Time:</span>
                                            {{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}
                                        </p>
                                        <p><span class="font-medium">Price:</span>
                                            ${{ number_format($booking->price_at_booking, 2) }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
