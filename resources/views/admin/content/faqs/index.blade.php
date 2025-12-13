<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('FAQs Management') }}
            </h2>
            <button @click="$dispatch('open-faq-modal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Question
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="faqManager({
        routes: {
            store: '{{ route('admin.faqs.store') }}',
            update: '{{ url('admin/faqs') }}',
            reorder: '{{ route('admin.faqs.reorder') }}'
        },
        csrfToken: '{{ csrf_token() }}'
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($faqs->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            <p class="text-lg">No FAQs added yet.</p>
                            <p class="text-sm">Click "Add New Question" to get started.</p>
                        </div>
                    @else
                        <ul class="space-y-3" id="faq-list">
                            @foreach($faqs as $faq)
                                <li 
                                    data-id="{{ $faq->id }}" 
                                    class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex items-center justify-between group hover:shadow-md transition-all cursor-move"
                                    draggable="true"
                                    @dragstart="dragStart($event)"
                                    @dragover="dragOver($event)"
                                    @drop="drop($event)"
                                >
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="text-gray-400 cursor-grab active:cursor-grabbing p-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                        </div>

                                        <div>
                                            <h3 class="font-bold text-gray-800">{{ $faq->question['en'] ?? 'N/A' }}</h3>
                                            <p class="text-sm text-gray-500 truncate max-w-md">{{ $faq->answer['en'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        
                                        <button @click="editFaq({{ json_encode($faq) }})" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Edit</button>
                                        
                                        <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm">Delete</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div x-show="reorderStatus" x-transition class="mt-4 text-sm text-green-600 font-medium text-right" x-text="reorderStatus"></div>
                        
                        <div class="mt-4">
                            {{ $faqs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="isModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    
                    <form :action="formAction" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="_method" :value="isEditMode ? 'PUT' : 'POST'">

                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-medium text-gray-900" x-text="isEditMode ? 'Edit FAQ' : 'Add New FAQ'"></h3>
                            
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button type="button" @click="activeLang = 'en'" :class="{ 'bg-white shadow': activeLang === 'en' }" class="px-3 py-1 rounded text-sm transition-all">EN</button>
                                <button type="button" @click="activeLang = 'ar'" :class="{ 'bg-white shadow': activeLang === 'ar' }" class="px-3 py-1 rounded text-sm transition-all">AR</button>
                            </div>
                        </div>

                        <div x-show="activeLang === 'en'" class="space-y-4">
                            <div>
                                <x-input-label value="Question (English)" />
                                <x-text-input name="question[en]" x-model="formData.question.en" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label value="Answer (English)" />
                                <textarea name="answer[en]" x-model="formData.answer.en" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                            </div>
                        </div>

                        <div x-show="activeLang === 'ar'" class="space-y-4" dir="rtl">
                            <div>
                                <x-input-label value="السؤال (العربية)" />
                                <x-text-input name="question[ar]" x-model="formData.question.ar" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label value="الإجابة (العربية)" />
                                <textarea name="answer[ar]" x-model="formData.answer.ar" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" x-model="formData.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="is_active" class="ms-2 text-sm text-gray-600">Active (Visible in App)</label>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>