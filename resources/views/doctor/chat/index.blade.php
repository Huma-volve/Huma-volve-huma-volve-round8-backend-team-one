@php
    $locale = request('lang', app()->getLocale());
    $isRtl = $locale === 'ar';
@endphp

@extends('layouts.doctor')

@section('content')
<div class="flex h-[calc(100vh-theme(spacing.32))] bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

    @include('doctor.chat.partials.sidebar')

    @include('doctor.chat.partials.main-chat-area')

</div>
@endsection

@push('styles')
<style>
    .filter-tab.active {
        background-color: rgb(var(--color-primary-50));
        color: rgb(var(--color-primary-700));
    }
</style>
@endpush

@push('scripts')
@vite(['resources/js/app.js'])
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.initDoctorChat) {
            window.initDoctorChat({
                baseUrl: '/doctor/chat',
                doctorId: {{ auth()->id() }},
                isRtl: {{ $isRtl ? 'true' : 'false' }}
            });
        }
    });
</script>
@endpush