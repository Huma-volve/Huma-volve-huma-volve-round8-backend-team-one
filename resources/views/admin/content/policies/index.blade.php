<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Policies Management') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: '{{ $policies->first()?->slug }}', activeLang: 'en' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-6">
                
                <div class="w-full md:w-1/4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700">
                            Pages
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @forelse($policies as $policy)
                                <li>
                                    <button 
                                        @click="activeTab = '{{ $policy->slug }}'"
                                        :class="{ 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-500': activeTab === '{{ $policy->slug }}', 'text-gray-600 hover:bg-gray-50': activeTab !== '{{ $policy->slug }}' }"
                                        class="w-full text-left px-4 py-3 transition duration-150 ease-in-out flex items-center justify-between"
                                    >
                                        <span>{{ $policy->title['en'] ?? $policy->slug }}</span>
                                        
                                        <span class="h-2.5 w-2.5 rounded-full {{ $policy->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    </button>
                                </li>
                            @empty
                                <li class="p-4 text-gray-500 text-sm text-center">No policies found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="w-full md:w-3/4">
                    @foreach($policies as $policy)
                        <div x-show="activeTab === '{{ $policy->slug }}'" x-cloak class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            
                            <form action="{{ route('admin.policies.update', $policy->slug) }}" method="POST" class="p-6">
                                @csrf
                                @method('PUT')

                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Edit Content</h3>
                                        <p class="text-sm text-gray-500">Update the {{ $policy->slug }} page details.</p>
                                    </div>
                                    
                                    <div class="flex bg-gray-100 p-1 rounded-lg mt-4 sm:mt-0">
                                        <button type="button" @click="activeLang = 'en'" :class="{ 'bg-white shadow text-gray-900': activeLang === 'en', 'text-gray-500 hover:text-gray-700': activeLang !== 'en' }" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all">
                                            English
                                        </button>
                                        <button type="button" @click="activeLang = 'ar'" :class="{ 'bg-white shadow text-gray-900': activeLang === 'ar', 'text-gray-500 hover:text-gray-700': activeLang !== 'ar' }" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all">
                                            العربية
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $policy->is_active ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Page is Active (Visible in App)</span>
                                    </label>
                                </div>

                                <div x-show="activeLang === 'en'" class="space-y-4">
                                    <div>
                                        <x-input-label for="title_en_{{ $policy->id }}" value="Page Title (English)" />
                                        <x-text-input id="title_en_{{ $policy->id }}" name="title[en]" type="text" class="mt-1 block w-full" :value="old('title.en', $policy->title['en'] ?? '')" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('title.en')" />
                                    </div>

                                    <div>
                                        <x-input-label for="content_en_{{ $policy->id }}" value="Content (English)" />
                                        <textarea id="content_en_{{ $policy->id }}" name="content[en]" rows="10" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('content.en', $policy->content['en'] ?? '') }}</textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('content.en')" />
                                    </div>
                                </div>

                                <div x-show="activeLang === 'ar'" class="space-y-4" dir="rtl">
                                    <div>
                                        <x-input-label for="title_ar_{{ $policy->id }}" value="عنوان الصفحة (العربية)" />
                                        <x-text-input id="title_ar_{{ $policy->id }}" name="title[ar]" type="text" class="mt-1 block w-full" :value="old('title.ar', $policy->title['ar'] ?? '')" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('title.ar')" />
                                    </div>

                                    <div>
                                        <x-input-label for="content_ar_{{ $policy->id }}" value="المحتوى (العربية)" />
                                        <textarea id="content_ar_{{ $policy->id }}" name="content[ar]" rows="10" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('content.ar', $policy->content['ar'] ?? '') }}</textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('content.ar')" />
                                    </div>
                                </div>

                                <div class="mt-8 flex items-center justify-end gap-4 border-t pt-4">
                                    <x-primary-button>
                                        {{ __('Save Changes') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>