@extends('layouts.doctor')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ __('My Appointments') }}</h2>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form action="{{ route('doctor.bookings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Search -->
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search by Patient Name or Date...') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        onchange="this.form.submit()">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}
                        </option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>
                            {{ __('Confirmed') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            {{ __('Completed') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            {{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <select name="date_filter"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        onchange="this.form.submit()">
                        <option value="">{{ __('All Dates') }}</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>
                            {{ __('Today') }}</option>
                        <option value="upcoming" {{ request('date_filter') == 'upcoming' ? 'selected' : '' }}>
                            {{ __('Upcoming') }}</option>
                        <option value="past" {{ request('date_filter') == 'past' ? 'selected' : '' }}>
                            {{ __('Past') }}</option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>
                            {{ __('This Week') }}</option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>
                            {{ __('This Month') }}</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Bookings List -->
        @if ($bookings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($bookings as $booking)
                    <a href="{{ route('doctor.bookings.show', $booking) }}" class="block group">
                        <div
                            class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-100 h-full flex flex-col">
                            <div class="p-5 flex-1">
                                <div class="flex items-center justify-between mb-4">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $booking->status === 'confirmed'
                                            ? 'bg-green-100 text-green-800'
                                            : ($booking->status === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : ($booking->status === 'cancelled'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="flex items-center mb-4">
                                    <div
                                        class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-lg mr-4 uppercase">
                                        {{ substr($booking->patient?->user?->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <h3
                                            class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                            {{ $booking->patient?->user?->name ?? __('Unknown Patient') }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $booking->appointment_date->format('M d, Y') }} •
                                            {{ $booking->appointment_time->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>

                                @if ($booking->notes)
                                    <p class="text-sm text-gray-600 line-clamp-2 mb-2">
                                        <span class="font-medium text-gray-700">{{ __('Note:') }}</span>
                                        {!! nl2br(e($booking->notes)) !!}</p>
                                @endif
                            </div>
                            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ __('Price:') }} {{ number_format($booking->price_at_booking, 2) }} EGP
                                </span>
                                <span class="text-indigo-600 text-sm font-medium group-hover:underline">
                                    {{ __('View Details') }} &rarr;
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No bookings found') }}</h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Adjust your filters or search to find what you are looking for.') }}</p>
            </div>
        @endif
    </div>
@endsection
