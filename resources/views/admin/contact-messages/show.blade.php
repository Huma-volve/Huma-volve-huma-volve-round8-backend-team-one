<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Message Details') }}
            </h2>
            <a href="{{ route('admin.contact-messages.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Sender Information -->
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div
                                class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center mr-4 text-indigo-600 text-2xl uppercase font-bold">
                                {{ substr($message->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $message->name }}</h3>
                                <a href="mailto:{{ $message->email }}" class="text-indigo-600 hover:text-indigo-800">
                                    {{ $message->email }}
                                </a>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex items-center gap-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $message->is_read ? 'bg-gray-100 text-gray-600' : 'bg-indigo-100 text-indigo-800' }}">
                                {{ $message->is_read ? 'Read' : 'Unread' }}
                            </span>
                        </div>
                    </div>

                    <!-- Message Meta -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Received:</span>
                            {{ $message->created_at->format('F d, Y \a\t h:i A') }}
                            <span class="text-gray-400">({{ $message->created_at->diffForHumans() }})</span>
                        </p>
                    </div>

                    <!-- Message Content -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Message</h4>
                        <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="mailto:{{ $message->email }}?subject=Re: Your message to us"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            Reply via Email
                        </a>

                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
                            onsubmit="return confirm('Are you sure you want to delete this message? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Delete Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
