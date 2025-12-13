<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('FAQs Management') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        isModalOpen: false,
        isEditMode: false,
        activeLang: 'en',
        formAction: '{{ route('admin.faqs.store') }}',
        reorderStatus: '',
        formData: {
            question: { en: '', ar: '' },
            answer: { en: '', ar: '' },
            is_active: true
        },
        editFaq(faq) {
            this.isEditMode = true;
            this.formAction = '{{ url('admin/faqs') }}/' + faq.id;
            this.formData = {
                question: { en: faq.question?.en || '', ar: faq.question?.ar || '' },
                answer: { en: faq.answer?.en || '', ar: faq.answer?.ar || '' },
                is_active: Boolean(faq.is_active)
            };
            this.isModalOpen = true;
        },
        openCreateModal() {
            this.isEditMode = false;
            this.formAction = '{{ route('admin.faqs.store') }}';
            this.formData = { question: { en: '', ar: '' }, answer: { en: '', ar: '' }, is_active: true };
            this.isModalOpen = true;
        },
        closeModal() { this.isModalOpen = false; },
        draggedItem: null,
        dragStart(e) { this.draggedItem = e.target; e.target.classList.add('opacity-50'); },
        dragOver(e) { e.preventDefault(); },
        drop(e) {
            e.stopPropagation();
            this.draggedItem.classList.remove('opacity-50');
            let target = e.target.closest('li');
            if (this.draggedItem !== target && target) {
                const rect = target.getBoundingClientRect();
                if (e.clientY > rect.y + rect.height / 2) target.after(this.draggedItem);
                else target.before(this.draggedItem);
                this.saveOrder();
            }
        },
        saveOrder() {
            let order = [...document.querySelectorAll('#faq-list li')].map((el, i) => ({ id: el.dataset.id, order: i + 1 }));
            fetch('{{ route('admin.faqs.reorder') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ order })
            }).then(r => r.json()).then(d => { this.reorderStatus = 'Order saved!'; setTimeout(() => this.reorderStatus = '', 3000); });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Manage FAQs</h3>
                <button @click="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New Question
                </button>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($faqs->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            <p class="text-lg">No FAQs added yet.</p>
                            <p class="text-sm">Click "Add New Question" to get started.</p>
                        </div>
                    @else
                        <ul class="space-y-3" id="faq-list">
                            @foreach($faqs as $faq)
                                <li data-id="{{ $faq->id }}" class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex items-center justify-between group hover:shadow-md cursor-move" draggable="true" @dragstart="dragStart($event)" @dragover="dragOver($event)" @drop="drop($event)">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="text-gray-400 cursor-grab p-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-800">{{ $faq->question['en'] ?? 'N/A' }}</h3>
                                            <p class="text-sm text-gray-500 truncate max-w-md">{{ $faq->answer['en'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $faq->is_active ? 'Active' : 'Inactive' }}</span>
                                        <button type="button" @click="editFaq({{ json_encode($faq) }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</button>
                                        <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button></form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div x-show="reorderStatus" class="mt-4 text-sm text-green-600 font-medium text-right" x-text="reorderStatus"></div>
                        <div class="mt-4">{{ $faqs->links() }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="isModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeModal()"></div>
                <div x-show="isModalOpen" class="bg-white rounded-lg shadow-xl transform sm:max-w-2xl sm:w-full relative z-10">
                    <form :action="formAction" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="_method" :value="isEditMode ? 'PUT' : 'POST'">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-medium" x-text="isEditMode ? 'Edit FAQ' : 'Add New FAQ'"></h3>
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button type="button" @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-white shadow' : ''" class="px-3 py-1 rounded text-sm">EN</button>
                                <button type="button" @click="activeLang = 'ar'" :class="activeLang === 'ar' ? 'bg-white shadow' : ''" class="px-3 py-1 rounded text-sm">AR</button>
                            </div>
                        </div>
                        <div x-show="activeLang === 'en'" class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700">Question (English)</label><input type="text" name="question[en]" x-model="formData.question.en" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                            <div><label class="block text-sm font-medium text-gray-700">Answer (English)</label><textarea name="answer[en]" x-model="formData.answer.en" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></textarea></div>
                        </div>
                        <div x-show="activeLang === 'ar'" class="space-y-4" dir="rtl">
                            <div><label class="block text-sm font-medium text-gray-700">السؤال (العربية)</label><input type="text" name="question[ar]" x-model="formData.question.ar" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></div>
                            <div><label class="block text-sm font-medium text-gray-700">الإجابة (العربية)</label><textarea name="answer[ar]" x-model="formData.answer.ar" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea></div>
                        </div>
                        <div class="mt-4 flex items-center">
                            <input type="checkbox" name="is_active" value="1" x-model="formData.is_active" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label class="ml-2 text-sm text-gray-600">Active</label>
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