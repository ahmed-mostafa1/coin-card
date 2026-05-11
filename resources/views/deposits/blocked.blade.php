@extends('layouts.app')

@section('title', 'تم إيقاف الإيداع')

@section('content')
    <div class="max-w-2xl mx-auto mt-10">
        <div class="rounded-3xl border border-rose-100 dark:border-rose-900 bg-white dark:bg-slate-800 p-8 shadow-sm text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/30">
                <i class="fa-solid fa-ban text-4xl text-rose-600 dark:text-rose-400"></i>
            </div>
            
            <h1 class="mt-6 text-2xl font-bold text-slate-900 dark:text-white">عذراً، الإيداع موقوف لحسابك</h1>
            
            <p class="mt-4 text-lg text-slate-700 dark:text-slate-300">
                {{ $message ?: 'تم إيقاف ميزة الإيداع لحسابك. يرجى التواصل مع الدعم الفني للمزيد من المعلومات.' }}
            </p>

            <div class="mt-8">
                <a href="{{ \App\Models\SiteSetting::get('whatsapp_link') ?? '#' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    <span>تواصل مع الدعم</span>
                </a>
            </div>
        </div>
    </div>
@endsection
