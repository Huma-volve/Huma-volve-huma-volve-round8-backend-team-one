@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">{{ __('Settings') }}</h1>
        <p class="text-slate-500">{{ __('Manage your account settings and profile details.') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">

        @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 border-b border-green-100">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('doctor.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <!-- Profile Photo -->
                <div class="flex flex-col md:flex-row gap-8 items-start">
                    <div class="flex-shrink-0">
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Profile Photo') }}</label>
                        <div class="relative group w-32 h-32 rounded-full overflow-hidden border-4 border-slate-100 bg-slate-50">
                            @if($user->profile_photo_path)
                            <img id="preview-photo" src="{{ Storage::url($user->profile_photo_path) }}" class="w-full h-full object-cover">
                            @else
                            <div id="preview-placeholder" class="w-full h-full flex items-center justify-center text-slate-400">
                                <i class="ph ph-user text-4xl"></i>
                            </div>
                            @endif

                            <label for="photo" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <span class="text-xs font-bold uppercase">{{ __('Change') }}</span>
                            </label>
                        </div>
                        <input type="file" name="photo" id="photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                        <script>
                            function previewImage(input) {
                                if (input.files && input.files[0]) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        var img = document.getElementById('preview-photo');
                                        if (!img) {
                                            // Create img if it doesn't exist (replacing placeholder)
                                            // Simplification: just reload or use simple logic. 
                                            // For now assuming img tag exists or layout reflows.
                                        }
                                        if (img) img.src = e.target.result;
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                }
                            }
                        </script>
                    </div>

                    <div class="flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Phone Number')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $user->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="bio" :value="__('Bio / About Me')" />
                            <textarea name="bio" id="bio" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('bio', $profile->bio) }}</textarea>
                            <p class="text-sm text-slate-400 mt-1">{{ __('Write a short introduction about yourself for patients.') }}</p>
                            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="session_price" :value="__('Session Price ($)')" />
                                <x-text-input id="session_price" class="block mt-1 w-full" type="number" step="0.01" name="session_price" :value="old('session_price', $profile->session_price)" required />
                                <x-input-error :messages="$errors->get('session_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="clinic_address" :value="__('Clinic Address')" />
                                <x-text-input id="clinic_address" class="block mt-1 w-full" type="text" name="clinic_address" :value="old('clinic_address', $profile->clinic_address)" />
                                <x-input-error :messages="$errors->get('clinic_address')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-6 mt-6 border-t border-slate-100">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection