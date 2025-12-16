<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('admin.bookings.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search by Patient Name -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Patient Name</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Filter by Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                                Confirmed</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                        </select>
                    </div>

                    <!-- Filter by Date -->
                    <div>
                        <label for="date_filter" class="block text-sm font-medium text-gray-700">Date</label>
                        <select name="date_filter" id="date_filter"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Dates</option>
                            <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today
                            </option>
                            <option value="week" {{ request('date_filter') === 'week' ? 'selected' : '' }}>This Week
                            </option>
                            <option value="month" {{ request('date_filter') === 'month' ? 'selected' : '' }}>This Month
                            </option>
                        </select>
                    </div>

                    <!-- Filter by Doctor -->
                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                        <select name="doctor_id" id="doctor_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Doctors</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}"
                                    {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4 flex justify-end gap-2">
                        <a href="{{ route('admin.bookings.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Reset
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            @if ($bookings->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($bookings as $booking)
                        <a href="{{ route('admin.bookings.show', $booking) }}"
                            class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $booking->patient->name }}</h3>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if ($booking->status === 'completed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p><span class="font-medium">Date:</span>
                                        {{ \Carbon\Carbon::parse($booking->appointment_date)->format('M d, Y') }}</p>
                                    <p><span class="font-medium">Time:</span>
                                        {{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}</p>
                                    <p><span class="font-medium">Doctor:</span>
                                        {{ $booking->doctor->user->name ?? 'Unknown' }}</p>
                                    <p><span class="font-medium">Price:</span>
                                        ${{ number_format($booking->price_at_booking, 2) }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-slate-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900">No Bookings Found</h3>
                    <p class="mt-1 text-slate-500">There are no bookings matching your criteria.</p>
                </div>
            @endif

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
