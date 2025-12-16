<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patients') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.patients.index') }}" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
                        class="input-text w-full max-w-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Search') }}
                    </button>
                    @if (request('search'))
                        <a href="{{ route('admin.patients.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($patients as $patient)
                    <a href="{{ route('admin.patients.show', $patient) }}"
                        class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                @if ($patient->profile_photo_path)
                                    <img src="{{ asset('storage/' . $patient->profile_photo_path) }}"
                                        alt="{{ $patient->name }}" class="h-12 w-12 rounded-full object-cover mr-4">
                                @else
                                    <div
                                        class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center mr-4 text-gray-500 uppercase font-bold">
                                        {{ substr($patient->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $patient->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $patient->email }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><span class="font-medium">Phone:</span> {{ $patient->phone ?? 'N/A' }}</p>
                                <p><span class="font-medium">Status:</span>
                                    @if ($patient->is_blocked)
                                        <span class="text-red-600 font-semibold">Blocked</span>
                                    @else
                                        <span class="text-green-600 font-semibold">Active</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
