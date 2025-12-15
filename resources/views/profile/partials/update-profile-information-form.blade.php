<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div>
            <x-input-label for="photo" :value="__('Profile Photo')" />

            <div class="mt-2 flex items-center gap-4">

                <!-- Current Photo -->
                @if($user->profile_photo_path)
                    <img
                        src="{{ asset('storage/' . $user->profile_photo_path) }}"
                        alt="Profile Photo"
                        class="h-20 w-20 rounded-full object-cover"
                    >
                @else
                    <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm">
                        No Photo
                    </div>
                @endif

                <!-- Upload Input -->
                <input
                    id="photo"
                    name="photo"
                    type="file"
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100"
                    accept="image/*"
                />
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <x-text-input id="bio" name="bio" type="text" class="mt-1 block w-full" value="{{old('bio', $user->doctorProfile->bio ?? '')}}" autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                        @endif
                    </div>
                    @endif
                </div>

                <div>
                    <x-input-label for="clinic_address" :value="__('Clinic Address')" />
                    <x-text-input id="clinic_address" name="clinic_address" type="text" class="mt-1 block w-full" :value="old('clinical-address', $user->doctorProfile->clinic_address)"   />
                    <x-input-error class="mt-2" :messages="$errors->get('clinic_address')" />
                </div>

                <div>
                    <x-input-label for="experience" :value="__('Experience')" />
                    <x-text-input id="experience" name="experience" type="number" class="mt-1 block w-full" :value="old('experience', $user->doctorProfile->experience_length)"  />
                    <x-input-error class="mt-2" :messages="$errors->get('experience')" />
                </div>

                <div>
                    <x-input-label for="session_price" :value="__('Session Price')" />
                    <x-text-input id="session_price" name="session_price" type="number" step='0.01' class="mt-1 block w-full" :value="old('session_price', $user->doctorProfile->session_price)"  />
                    <x-input-error class="mt-2" :messages="$errors->get('session_price')" />
                </div>

                <div>
                    <x-input-label for="license_number" :value="__('License Number')" />
                    <x-text-input id="license_number" name="license_number" type="text"  class="mt-1 block w-full" :value="old('license_number', $user->doctorProfile->license_number)"  />
                    <x-input-error class="mt-2" :messages="$errors->get('license_number')" />
                </div>

                <div>
                    <x-input-label for="speciality" :value="__('Speciality')" />
                    <x-text-input id="speciality" name="speciality" type="text"  class="mt-1 block w-full" value="{{$user->doctorProfile->speciality->name}}" readonly />
                    <x-input-error class="mt-2" :messages="$errors->get('speciality')" />
                </div>

                <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition.opacity.duration.500ms
                    x-init="setTimeout(() => show = false, 2000)"
                    class="block w-full text-sm text-white bg-green-500 px-4 py-2 rounded-md shadow-md text-center"
                >
                    {{ __('Your profile updated successfully!') }}
                </p>
            @endif
        </div>
    </form>
</section>
