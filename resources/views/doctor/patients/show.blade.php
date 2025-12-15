@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('doctor.patients.index') }}" class="text-slate-500 hover:text-slate-800 flex items-center gap-2 mb-2 transition-colors">
            <i class="ph ph-arrow-left"></i> {{ __('Back to Patients') }}
        </a>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $patient->name }}</h1>
                <p class="text-slate-500">{{ $patient->email }}</p>
            </div>
            <!-- Actions like Block could go here -->
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Patient Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-24 h-24 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-3xl mb-4">
                        {{ substr($patient->name, 0, 1) }}
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $patient->name }}</h2>
                    <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full mt-1">{{ __('Patient') }}</span>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">{{ __('Gender') }}</span>
                        <span class="font-medium text-slate-800 capitalize">{{ $patient->patientProfile->gender ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">{{ __('Birthdate') }}</span>
                        <span class="font-medium text-slate-800">{{ $patient->patientProfile->birthdate ? \Carbon\Carbon::parse($patient->patientProfile->birthdate)->format('M d, Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">{{ __('Age') }}</span>
                        <span class="font-medium text-slate-800">{{ $patient->patientProfile->birthdate ? \Carbon\Carbon::parse($patient->patientProfile->birthdate)->age : '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">{{ __('Joined') }}</span>
                        <span class="font-medium text-slate-800">{{ $patient->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-lg text-slate-800">{{ __('Appointment History') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">{{ __('Date') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Time') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Details') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($bookings as $booking)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-800 font-medium">
                                    {{ \Carbon\Carbon::parse($booking->appointment_date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($booking->status == 'confirmed') bg-green-100 text-green-700
                                        @elseif($booking->status == 'completed') bg-blue-100 text-blue-700
                                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                                        @else bg-slate-100 text-slate-700 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('doctor.bookings.show', $booking) }}" class="text-indigo-600 hover:underline">{{ __('View') }}</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    {{ __('No appointment history found with you.') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($bookings->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $bookings->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection