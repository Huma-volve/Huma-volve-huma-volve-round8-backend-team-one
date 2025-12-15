<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Doctor') }}
            </h2>
            <a href="{{ route('admin.doctors.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ __('There were errors with your submission') }}
                            </h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.doctors.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Account Information') }}</h3>

                                <div>
                                    <x-input-label for="name" :value="__('Full Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="old('name', $user->name)" required />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email', $user->email)" required />
                                </div>
                            </div>

                            <!-- Professional Info -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Professional Details') }}</h3>

                                <div>
                                    <x-input-label for="specialty_id" :value="__('Specialty')" />
                                    <select id="specialty_id" name="specialty_id"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                        <option value="">{{ __('Select Specialty') }}</option>
                                        @foreach($specialties as $specialty)
                                            <option value="{{ $specialty->id }}" {{ old('specialty_id', $user->doctorProfile->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                                {{ $specialty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="experience_length" :value="__('Experience (Years)')" />
                                    <x-text-input id="experience_length" class="block mt-1 w-full" type="number"
                                        name="experience_length" :value="old('experience_length', $user->doctorProfile->experience_length)" min="0" required />
                                </div>

                                <div>
                                    <x-input-label for="license_number" :value="__('License Number')" />
                                    <x-text-input id="license_number" class="block mt-1 w-full" type="text"
                                        name="license_number" :value="old('license_number', $user->doctorProfile->license_number)" required />
                                </div>

                                <div>
                                    <x-input-label for="session_price" :value="__('Session Price (EGP)')" />
                                    <x-text-input id="session_price" class="block mt-1 w-full" type="number"
                                        name="session_price" :value="old('session_price', $user->doctorProfile->session_price)" step="0.01" min="0" required />
                                </div>

                                <div>
                                    <x-input-label for="clinic_address" :value="__('Clinic Address')" />
                                    <x-text-input id="clinic_address" class="block mt-1 w-full" type="text"
                                        name="clinic_address" :value="old('clinic_address', $user->doctorProfile->clinic_address)" required />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Update Doctor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
