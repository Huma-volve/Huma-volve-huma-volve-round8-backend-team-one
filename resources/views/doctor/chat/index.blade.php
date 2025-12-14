@php
    $locale = request('lang', app()->getLocale());
    $isRtl = $locale === 'ar';
@endphp

@extends('layouts.doctor')

@section('content')
<div x-data="{ showMobileChat: false }" @open-chat.window="showMobileChat = true" class="flex h-[calc(100dvh-theme(spacing.32))] bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden relative">

    <div :class="showMobileChat ? 'hidden md:flex' : 'flex w-full'" class="md:w-1/3 md:flex md:flex-col border-e border-slate-100 bg-white z-10 transition-all">
        @include('doctor.chat.partials.sidebar')
    </div>

    <div :class="showMobileChat ? 'flex w-full' : 'hidden md:flex'" class="md:w-2/3 flex-col relative w-full bg-slate-50 transition-all">
        <div class="md:hidden bg-white border-b border-slate-100 p-2 flex items-center shadow-sm z-20 sticky top-0 h-14">
            <button @click="showMobileChat = false" class="text-slate-500 hover:text-primary-600 flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-50">
                <i class="ph ph-arrow-right text-lg"></i>
                <span class="text-sm font-medium">Back</span>
            </button>
        </div>
        <div class="flex-1 flex flex-col overflow-hidden h-full">
            @include('doctor.chat.partials.main-chat-area')
        </div>
    </div>

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