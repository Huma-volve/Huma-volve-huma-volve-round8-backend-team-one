<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Policies Management') }}
        </h2>
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
        <style>
            trix-toolbar [data-trix-button-group="file-tools"] {
                display: none;
            }

            trix-editor {
                min-height: 350px;
                background-color: white;
                border-radius: 0.375rem;
            }

            .trix-content h1 {
                font-size: 1.5rem;
                font-weight: bold;
                margin-bottom: 0.5rem;
            }

            .trix-content h2 {
                font-size: 1.25rem;
                font-weight: bold;
                margin-bottom: 0.5rem;
            }

            .trix-content ul {
                list-style-type: disc;
                margin-left: 1rem;
            }

            .trix-content ol {
                list-style-type: decimal;
                margin-left: 1rem;
            }
        </style>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: '{{ $policies->first()?->slug }}', showCreateModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-6">

                <div class="w-full md:w-1/4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div
                            class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 flex justify-between items-center">
                            Pages
                            <button @click="showCreateModal = true"
                                class="text-xs bg-indigo-500 hover:bg-indigo-700 text-white py-1 px-2 rounded">New</button>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @forelse($policies as $policy)
                                <li>
                                    <button @click="activeTab = '{{ $policy->slug }}'"
                                        :class="{ 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-500': activeTab === '{{ $policy->slug }}', 'text-gray-600 hover:bg-gray-50': activeTab !== '{{ $policy->slug }}' }"
                                        class="w-full text-left px-4 py-3 transition duration-150 ease-in-out flex items-center justify-between">
                                        <span>{{ $policy->title['en'] ?? $policy->slug }}</span>
                                        <span
                                            class="h-2.5 w-2.5 rounded-full {{ $policy->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    </button>
                                </li>
                            @empty
                                <li class="p-4 text-gray-500 text-sm text-center">No policies found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="w-full md:w-3/4">
                    @foreach ($policies as $policy)
                        <div x-show="activeTab === '{{ $policy->slug }}'" x-cloak
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                            <form id="delete-form-{{ $policy->slug }}"
                                action="{{ route('admin.policies.destroy', $policy->slug) }}" method="POST"
                                style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <form action="{{ route('admin.policies.update', $policy->slug) }}" method="POST"
                                class="p-6">
                                @csrf
                                @method('PUT')

                                <div class="mb-6 border-b pb-4 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Edit Content</h3>
                                        <p class="text-sm text-gray-500">Update the {{ $policy->slug }} page details.
                                        </p>
                                    </div>
                                    <button type="submit" form="delete-form-{{ $policy->slug }}"
                                        onclick="return confirm('Are you sure you want to delete this policy?');"
                                        class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1 rounded text-sm font-medium transition-colors">
                                        Delete Policy
                                    </button>
                                </div>

                                <div class="mb-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                            {{ $policy->is_active ? 'checked' : '' }}>
                                        <div
                                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                        </div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">Page is Active (Visible in
                                            App)</span>
                                    </label>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <x-input-label for="title_en_{{ $policy->id }}" value="Page Title" />
                                        <x-text-input id="title_en_{{ $policy->id }}" name="title[en]" type="text"
                                            class="mt-1 block w-full" :value="old('title.en', $policy->title['en'] ?? '')" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('title.en')" />
                                    </div>

                                    <div>
                                        <x-input-label for="content_en_{{ $policy->id }}" value="Content" />

                                        <input id="content_en_{{ $policy->id }}" type="hidden" name="content[en]"
                                            value="{{ old('content.en', $policy->content['en'] ?? '') }}">

                                        <trix-editor input="content_en_{{ $policy->id }}"
                                            class="prose max-w-none block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></trix-editor>

                                        <x-input-error class="mt-2" :messages="$errors->get('content.en')" />
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
        <!-- Create Policy Modal -->
        <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    @click="showCreateModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.policies.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Create New Policy
                            </h3>
                            <div class="mt-4">
                                <label for="create_slug" class="block text-sm font-medium text-gray-700">Slug</label>
                                <input type="text" name="slug" id="create_slug"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                                <p class="text-xs text-gray-500 mt-1">URL-friendly identifier (e.g., privacy-policy)
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="create_title_en" class="block text-sm font-medium text-gray-700">Title
                                    (EN)</label>
                                <input type="text" name="title[en]" id="create_title_en"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>
                            <!-- Hidden inputs for required fields not in this modal -->
                            <input type="hidden" name="content[en]" value="<p>Draft Content</p>">
                            <input type="hidden" name="is_active" value="0">
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Create</button>
                            <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="showCreateModal = false">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
