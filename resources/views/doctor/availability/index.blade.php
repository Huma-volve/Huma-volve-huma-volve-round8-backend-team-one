@extends('layouts.doctor')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ __('Manage Availability') }}</h1>
                <p class="text-slate-500 mt-1">
                    {{ __('Set your weekly availability schedule for patients to book appointments.') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">{{ __('There were errors with your submission') }}</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add Availability Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <h2 class="text-lg font-semibold text-slate-800">{{ __('Add New Availability') }}</h2>
            </div>
            <form action="{{ route('doctor.availability.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Day of Week -->
                    <div class="space-y-2">
                        <label for="day_of_week"
                            class="block text-sm font-medium text-slate-700">{{ __('Day of Week') }}</label>
                        <div class="relative">
                            <select name="day_of_week" id="day_of_week"
                                class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors text-slate-700">
                                <option value="0">{{ __('Sunday') }}</option>
                                <option value="1">{{ __('Monday') }}</option>
                                <option value="2">{{ __('Tuesday') }}</option>
                                <option value="3">{{ __('Wednesday') }}</option>
                                <option value="4">{{ __('Thursday') }}</option>
                                <option value="5">{{ __('Friday') }}</option>
                                <option value="6">{{ __('Saturday') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Start Time -->
                    <div class="space-y-2">
                        <label for="start_time"
                            class="block text-sm font-medium text-slate-700">{{ __('Start Time') }}</label>
                        <input type="time" name="start_time" id="start_time" required
                            class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                    </div>

                    <!-- End Time -->
                    <div class="space-y-2">
                        <label for="end_time" class="block text-sm font-medium text-slate-700">{{ __('End Time') }}</label>
                        <input type="time" name="end_time" id="end_time" required
                            class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                    </div>

                    <!-- Duration -->
                    <div class="space-y-2">
                        <label for="avg_consultation_time"
                            class="block text-sm font-medium text-slate-700">{{ __('Session Duration (min)') }}</label>
                        <input type="number" name="avg_consultation_time" id="avg_consultation_time" value="30" min="5"
                            max="120" required
                            class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('Add Schedule') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Schedules -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-800">{{ __('Current Availability') }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $schedules->count() }} {{Str::plural('Slot', $schedules->count())}}
                </span>
            </div>

            @if($schedules->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900">{{ __('No availability set') }}</h3>
                    <p class="mt-1 text-slate-500">{{ __('Add your weekly schedule above to start accepting appointments.') }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    {{ __('Day') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    {{ __('Time Range') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    {{ __('Duration') }}</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($schedules->sortBy('day_of_week') as $schedule)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 font-semibold text-sm mr-3">
                                                {{ substr(Carbon\Carbon::parse("Sunday")->addDays($schedule->day_of_week)->format('l'), 0, 1) }}
                                            </span>
                                            <span class="text-sm font-medium text-slate-900">
                                                {{ Carbon\Carbon::parse("Sunday")->addDays($schedule->day_of_week)->format('l') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">
                                            {{ Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                                            <span class="text-slate-400 mx-1">→</span>
                                            {{ Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            {{ $schedule->avg_consultation_time }} {{ __('min') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('doctor.availability.destroy', $schedule->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this schedule?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors p-2 hover:bg-red-50 rounded-full">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection