<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $policy->title['en'] }} - {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">
    
    <div class="min-h-screen pt-6 sm:pt-12 pb-12">
        <div class="flex justify-center mb-8">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 sm:p-12">
                
                @auth
                <div class="mb-6 flex justify-end">
                    <a href="{{ route('admin.policies.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Admin Editor
                    </a>
                </div>
                @endauth

                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-8 border-b border-gray-200 pb-4">
                    {{ $policy->title['en'] }}
                </h1>

                <div class="trix-content prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {!! $policy->content['en'] !!}
                </div>
            </div>

            <div class="mt-8 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>

    <style>
        .trix-content h1 { font-size: 1.8em; font-weight: 800; margin-top: 1.5em; margin-bottom: 0.8em; color: #111827; }
        .trix-content h2 { font-size: 1.5em; font-weight: 700; margin-top: 1.5em; margin-bottom: 0.8em; color: #374151; }
        .trix-content h3 { font-size: 1.25em; font-weight: 600; margin-top: 1.2em; margin-bottom: 0.6em; }
        
        .trix-content ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1.5em; }
        .trix-content ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1.5em; }
        .trix-content li { margin-bottom: 0.5em; }
        
        .trix-content a { color: #4f46e5; text-decoration: underline; font-weight: 500; }
        .trix-content blockquote { border-left: 4px solid #e5e7eb; padding-left: 1em; font-style: italic; color: #6b7280; margin-bottom: 1.5em; background: #f9fafb; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0; }
        
        .trix-content strong { font-weight: 700; color: #111827; }
    </style>
</body>
</html>