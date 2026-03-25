@extends('layouts.doctor')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Reply to Review') }}</h2>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- Patient Info -->
        <div class="flex items-center mb-4">
            <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden mr-4">
                @if($review->patient->user->profile_photo)
                    <img src="{{ asset('storage/' . $review->patient->user->profile_photo) }}" alt="Patient Photo" class="h-full w-full object-cover">
                @else
                    <span class="text-indigo-500 font-bold text-lg uppercase">{{ substr($review->patient->user->name ?? 'U', 0, 1) }}</span>
                @endif
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $review->patient->user->name ?? __('Unknown Patient') }}</h3>
                <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Rating -->
        <div class="flex items-center mb-4">
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
            <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ $review->comment }}</p>
        @endif

        <!-- Doctor Response Form -->
        <form action="{{ route('doctor.reviews.saveReply', $review->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="doctor_response" class="block text-sm font-medium text-gray-700">{{ __('Your Response') }}</label>
                <textarea name="doctor_response" id="doctor_response" rows="4" required
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('doctor_response', $review->doctor_response) }}</textarea>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                {{ __('Send Reply') }}
            </button>
        </form>
    </div>
</div>
@endsection
