<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="flex flex-wrap gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or message..."
                        class="input-text w-full max-w-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    
                    <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">All Messages</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                    </select>

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Search') }}
                    </button>

                    @if (request('search') || request('status'))
                        <a href="{{ route('admin.contact-messages.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            @if ($messages->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($messages as $message)
                        <a href="{{ route('admin.contact-messages.show', $message) }}"
                            class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 {{ !$message->is_read ? 'border-l-4 border-indigo-500' : '' }}">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3 text-indigo-600 uppercase font-bold">
                                            {{ substr($message->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $message->name }}</h3>
                                            <p class="text-sm text-gray-500">{{ $message->email }}</p>
                                        </div>
                                    </div>
                                    @if (!$message->is_read)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            New
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($message->message, 100) }}</p>
                                <p class="text-xs text-gray-400 mt-3">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-slate-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900">No Messages Found</h3>
                    <p class="mt-1 text-slate-500">There are no contact messages matching your search.</p>
                </div>
            @endif

            <div class="mt-6">
                {{ $messages->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
