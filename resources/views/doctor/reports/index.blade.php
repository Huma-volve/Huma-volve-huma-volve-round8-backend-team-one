@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">{{ __('Earnings & Reports') }}</h1>
        <p class="text-slate-500">{{ __('Track your financial performance and appointment statistics.') }}</p>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                    <i class="ph-fill ph-currency-dollar text-2xl"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">${{ number_format($totalEarnings, 2) }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Total Earnings') }}</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="ph-fill ph-check-circle text-2xl"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $completedBookings }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Completed Sessions') }}</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                    <i class="ph-fill ph-x-circle text-2xl"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $cancelledBookings }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Cancelled') }}</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                    <i class="ph-fill ph-calendar text-2xl"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $totalBookings }}</h3>
            <p class="text-slate-500 font-medium">{{ __('Total Bookings') }}</p>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-800">{{ __('Monthly Earnings Breakdown') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">{{ __('Month') }}</th>
                        <th class="px-6 py-4 font-medium">{{ __('Earnings') }}</th>
                        <th class="px-6 py-4 font-medium">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($monthlyEarnings as $earning)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $earning->month)->format('F Y') }}
                        </td>
                        <td class="px-6 py-4 text-green-600 font-bold">
                            ${{ number_format($earning->total, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold">{{ __('Paid') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-slate-500">{{ __('No earnings records found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection