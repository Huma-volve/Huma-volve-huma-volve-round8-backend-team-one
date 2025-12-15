@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">
            {{ __('Admin Dashboard') }}
        </h1>
        <p class="text-slate-500 mt-2">{{ __('Overview of the platform performance and activities.') }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Doctors Stat -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="ph-fill ph-stethoscope text-2xl"></i>
                </div>
                {{-- <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">+5%</span> --}}
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $doctorsCount }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Total Doctors') }}</p>
        </div>

        <!-- Patients Stat -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                    <i class="ph-fill ph-users text-2xl"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $patientsCount }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Total Patients') }}</p>
        </div>

        <!-- Bookings Stat -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                    <i class="ph-fill ph-calendar-check text-2xl"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $bookingsCount }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Total Bookings') }}</p>
        </div>

        <!-- Revenue Stat -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center text-yellow-600">
                    <i class="ph-fill ph-currency-dollar text-2xl"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">${{ number_format($revenue, 2) }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Total Revenue') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Recent Doctors -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">{{ __('New Doctors') }}</h3>
                <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium">{{ __('View All') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Speciality') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Joined Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($newDoctors as $doctor)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden">
                                    @if($doctor->profile_photo_path)
                                    <img src="{{ Storage::url($doctor->profile_photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary-100 text-primary-600 font-bold text-xs">
                                        {{ substr($doctor->name, 0, 1) }}
                                    </div>
                                    @endif
                                </div>
                                {{ $doctor->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $doctor->doctorProfile?->speciality?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $doctor->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-slate-500">{{ __('No new doctors found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">{{ __('Recent Bookings') }}</h3>
                <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium">{{ __('View All') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">{{ __('Date') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Doctor') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Patient') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentBookings as $booking)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-slate-500">
                                {{ \Carbon\Carbon::parse($booking->date)->format('M d') }} - {{ $booking->start_time }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $booking->doctor->user->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $booking->patient->user->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($booking->status == 'confirmed') bg-green-100 text-green-700
                                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-700
                                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                                        @else bg-slate-100 text-slate-700 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-slate-500">{{ __('No bookings found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection