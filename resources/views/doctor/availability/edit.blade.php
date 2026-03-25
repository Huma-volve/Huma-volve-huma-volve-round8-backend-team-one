@extends('layouts.doctor')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ __('Edit Availability') }}</h1>
                <p class="text-slate-500 mt-1">
                    {{ __('Update your availability schedule for this time slot.') }}</p>
            </div>
            <a href="{{ route('doctor.availability.index') }}"
                class="inline-flex items-center px-4 py-2 bg-slate-100 border border-transparent rounded-lg font-semibold text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back') }}
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">{{ __('There were errors with your submission') }}</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Availability Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <h2 class="text-lg font-semibold text-slate-800">{{ __('Schedule Details') }}</h2>
            </div>
            <form action="{{ route('doctor.availability.update', $schedule->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Day of Week -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="day_of_week"
                            class="block text-sm font-medium text-slate-700">{{ __('Day of Week') }}</label>
                        <div class="relative">
                            <select name="day_of_week" id="day_of_week"
                                class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors text-slate-700">
                                <option value="0"
                                    {{ old('day_of_week', $schedule->day_of_week) == 0 ? 'selected' : '' }}>
                                    {{ __('Sunday') }}</option>
                                <option value="1"
                                    {{ old('day_of_week', $schedule->day_of_week) == 1 ? 'selected' : '' }}>
                                    {{ __('Monday') }}</option>
                                <option value="2"
                                    {{ old('day_of_week', $schedule->day_of_week) == 2 ? 'selected' : '' }}>
                                    {{ __('Tuesday') }}</option>
                                <option value="3"
                                    {{ old('day_of_week', $schedule->day_of_week) == 3 ? 'selected' : '' }}>
                                    {{ __('Wednesday') }}</option>
                                <option value="4"
                                    {{ old('day_of_week', $schedule->day_of_week) == 4 ? 'selected' : '' }}>
                                    {{ __('Thursday') }}</option>
                                <option value="5"
                                    {{ old('day_of_week', $schedule->day_of_week) == 5 ? 'selected' : '' }}>
                                    {{ __('Friday') }}</option>
                                <option value="6"
                                    {{ old('day_of_week', $schedule->day_of_week) == 6 ? 'selected' : '' }}>
                                    {{ __('Saturday') }}</option>
                            </select>
                        </div>
                    </div>

                    @php
                        $startTime = \Carbon\Carbon::parse($schedule->start_time);
                        $endTime = \Carbon\Carbon::parse($schedule->end_time);
                        $startHour12 = $startTime->format('h');
                        $startMinute = $startTime->format('i');
                        $startPeriod = $startTime->format('A');
                        $endHour12 = $endTime->format('h');
                        $endMinute = $endTime->format('i');
                        $endPeriod = $endTime->format('A');
                    @endphp

                    <!-- Start Time -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">{{ __('Start Time') }}</label>
                        <div class="flex items-center gap-2">
                            <select id="start_hour" required
                                class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                        {{ $startHour12 == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <span class="text-slate-500 font-semibold">:</span>
                            <select id="start_minute" required
                                class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                                @for ($i = 0; $i <= 55; $i += 5)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                        {{ $startMinute == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <select id="start_period" required
                                class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                                <option value="AM" {{ $startPeriod == 'AM' ? 'selected' : '' }}>AM</option>
                                <option value="PM" {{ $startPeriod == 'PM' ? 'selected' : '' }}>PM</option>
                            </select>
                        </div>
                        <input type="hidden" name="start_time" id="start_time">
                    </div>

                    <!-- End Time -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">{{ __('End Time') }}</label>
                        <div class="flex items-center gap-2">
                            <select id="end_hour" required
                                class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                        {{ $endHour12 == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <span class="text-slate-500 font-semibold">:</span>
                            <select id="end_minute" required
                                class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                                @for ($i = 0; $i <= 55; $i += 5)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                        {{ $endMinute == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <select id="end_period" required
                                class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                                <option value="AM" {{ $endPeriod == 'AM' ? 'selected' : '' }}>AM</option>
                                <option value="PM" {{ $endPeriod == 'PM' ? 'selected' : '' }}>PM</option>
                            </select>
                        </div>
                        <input type="hidden" name="end_time" id="end_time">
                    </div>

                    <!-- Duration -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="avg_consultation_time"
                            class="block text-sm font-medium text-slate-700">{{ __('Session Duration (min)') }}</label>
                        <input type="number" name="avg_consultation_time" id="avg_consultation_time"
                            value="{{ old('avg_consultation_time', $schedule->avg_consultation_time) }}" min="5"
                            max="120" required
                            class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-slate-700">
                        <p class="text-sm text-slate-500">
                            {{ __('Enter the duration of each consultation slot in minutes (5-120).') }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('doctor.availability.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 border border-transparent rounded-lg font-semibold text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Update Schedule') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Convert 12-hour format to 24-hour format
            function convertTo24Hour(hour, minute, period) {
                let h = parseInt(hour);
                if (period === 'PM' && h !== 12) {
                    h += 12;
                } else if (period === 'AM' && h === 12) {
                    h = 0;
                }
                return String(h).padStart(2, '0') + ':' + minute;
            }

            // Update hidden inputs when selects change
            function updateTimeInputs() {
                const startHour = document.getElementById('start_hour').value;
                const startMinute = document.getElementById('start_minute').value;
                const startPeriod = document.getElementById('start_period').value;
                document.getElementById('start_time').value = convertTo24Hour(startHour, startMinute, startPeriod);

                const endHour = document.getElementById('end_hour').value;
                const endMinute = document.getElementById('end_minute').value;
                const endPeriod = document.getElementById('end_period').value;
                document.getElementById('end_time').value = convertTo24Hour(endHour, endMinute, endPeriod);
            }

            // Add event listeners
            ['start_hour', 'start_minute', 'start_period', 'end_hour', 'end_minute', 'end_period'].forEach(id => {
                document.getElementById(id).addEventListener('change', updateTimeInputs);
            });

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateTimeInputs();
            });
        </script>
    @endpush
@endsection
