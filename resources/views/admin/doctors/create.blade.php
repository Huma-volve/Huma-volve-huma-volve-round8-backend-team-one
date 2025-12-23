<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Doctor') }}
            </h2>
            <a href="{{ route('admin.doctors.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">



            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.doctors.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Account Information') }}</h3>

                                <div>
                                    <x-input-label for="name" :value="__('Full Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password"
                                        name="password" required />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
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
                                            <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                                {{ $specialty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('specialty_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="experience_length" :value="__('Experience (Years)')" />
                                    <x-text-input id="experience_length" class="block mt-1 w-full" type="number"
                                        name="experience_length" :value="old('experience_length')" min="0" required />
                                    <x-input-error :messages="$errors->get('experience_length')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="license_number" :value="__('License Number')" />
                                    <x-text-input id="license_number" class="block mt-1 w-full" type="text"
                                        name="license_number" :value="old('license_number')" required />
                                    <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="session_price" :value="__('Session Price (EGP)')" />
                                    <x-text-input id="session_price" class="block mt-1 w-full" type="number"
                                        name="session_price" :value="old('session_price')" step="0.01" min="0"
                                        required />
                                </div>

                                <div>
                                    <x-input-label for="clinic_address" :value="__('Clinic Address')" />
                                    <x-text-input id="clinic_address" class="block mt-1 w-full" type="text"
                                        name="clinic_address" :value="old('clinic_address')" required />
                                    <x-input-error :messages="$errors->get('clinic_address')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Create Doctor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>