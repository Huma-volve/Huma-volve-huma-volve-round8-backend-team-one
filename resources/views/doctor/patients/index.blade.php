@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">{{ __('My Patients') }}</h1>
        <a href="{{ route('doctor.patients.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            {{ __('Add New Patient') }}
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6">
        <form action="{{ route('doctor.patients.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1 relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name or email...') }}"
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                {{ __('Search') }}
            </button>
        </form>
    </div>

    <!-- Patients List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">{{ __('Patient') }}</th>
                        <th class="px-6 py-4 font-medium">{{ __('Gender') }}</th>
                        <th class="px-6 py-4 font-medium">{{ __('Age') }}</th>
                        <th class="px-6 py-4 font-medium text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800">{{ $patient->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $patient->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 capitalize">
                            {{ $patient->patientProfile->gender ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            <!-- TODO: Fetch last booking date efficiently -->
                            {{-- {{ $patient->bookings->last()->appointment_date ?? '-' }} --}}
                            -
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('doctor.patients.show', $patient) }}" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors" title="{{ __('View Details') }}">
                                    <i class="ph ph-eye text-lg"></i>
                                </a>

                                <a href="{{ route('doctor.patients.edit', $patient) }}" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors" title="{{ __('Edit Patient') }}">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </a>

                                <form action="{{ route('doctor.patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to remove this patient?') }}');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="{{ __('Delete Patient') }}">
                                        <i class="ph ph-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="ph ph-users text-4xl mb-2 text-slate-300"></i>
                                <p>{{ __('No patients found.') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($patients->hasPages())
        <div class="p-4 border-t border-slate-100">
            <div class="flex justify-between items-center">
                <div class="text-sm text-slate-500">
                    {{ __('Showing') }} <span class="font-medium text-slate-800">{{ $patients->firstItem() }}</span> {{ __('to') }} <span class="font-medium text-slate-800">{{ $patients->lastItem() }}</span> {{ __('of') }} <span class="font-medium text-slate-800">{{ $patients->total() }}</span> {{ __('results') }}
                </div>
                <div class="flex gap-2">
                    @if($patients->onFirstPage())
                    <span class="px-4 py-2 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed bg-slate-50">{{ __('Previous') }}</span>
                    @else
                    <a href="{{ $patients->previousPageUrl() }}" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">{{ __('Previous') }}</a>
                    @endif

                    @if($patients->hasMorePages())
                    <a href="{{ $patients->nextPageUrl() }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ __('Next') }}</a>
                    @else
                    <span class="px-4 py-2 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed bg-slate-50">{{ __('Next') }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection