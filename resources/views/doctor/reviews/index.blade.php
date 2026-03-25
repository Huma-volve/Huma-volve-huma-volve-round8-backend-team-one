@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('My Reviews') }}</h2>
    </div>

    <!-- Reviews List -->
    @if($reviews->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reviews as $review)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 h-full flex flex-col">
                    <div class="p-5 flex-1">
                        <!-- Patient Info -->
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-lg mr-4 uppercase">
                                {{ substr($review->patient?->user?->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $review->patient?->user?->name ?? __('Unknown Patient') }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $review->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="flex items-center mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.037 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.037 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>

                        <!-- Comment -->
                        @if($review->comment)
                            <p class="text-sm text-gray-600 line-clamp-3 mb-2">
                                {{ $review->comment }}
                            </p>
                        @endif

                        <!-- Doctor Response or Reply Button -->
                        @if($review->doctor_response)
                            <div class="mt-2 p-3 bg-gray-50 rounded-lg border-l-4 border-indigo-500">
                                <p class="text-sm text-gray-800 font-medium">{{ __('Your Response:') }}</p>
                                <p class="text-sm text-gray-600">{{ $review->doctor_response }}</p>
                            </div>
                        @else
                            <div class="mt-2">
                                <a href="{{ route('doctor.reviews.reply', $review->id) }}"
                                   class="inline-block px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                   {{ __('Reply to Review') }}
                                </a>
                            </div>
                        @endif

                    </div>

                    <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{-- Pagination لو حابة تستخدميه --}}
            {{-- {{ $reviews->links() }} --}}
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No reviews found') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('You have not received any reviews yet.') }}</p>
        </div>
    @endif
</div>
@endsection
