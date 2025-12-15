@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('doctor.patients.index') }}" class="text-slate-500 hover:text-slate-800 flex items-center gap-2 mb-2 transition-colors">
            <i class="ph ph-arrow-left"></i> {{ __('Back to Patients') }}
        </a>
        <h1 class="text-2xl font-bold text-slate-800">{{ __('Edit Patient') }}</h1>
        <p class="text-slate-500">{{ __('Update patient information.') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('doctor.patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Personal Info -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">{{ __('Personal Information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $patient->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $patient->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="birthdate" :value="__('Date of Birth')" />
                        <x-text-input id="birthdate" class="block mt-1 w-full" type="date" name="birthdate" :value="old('birthdate', $patient->patientProfile->birthdate ? $patient->patientProfile->birthdate->format('Y-m-d') : '')" />
                        <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="gender" :value="__('Gender')" />
                        <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Select Gender') }}</option>
                            <option value="male" {{ old('gender', $patient->patientProfile->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                            <option value="female" {{ old('gender', $patient->patientProfile->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                            <option value="other" {{ old('gender', $patient->patientProfile->gender) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                    <a href="{{ route('doctor.patients.index') }}" class="text-slate-600 hover:text-slate-800 font-medium">{{ __('Cancel') }}</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm">
                        {{ __('Update Patient') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection