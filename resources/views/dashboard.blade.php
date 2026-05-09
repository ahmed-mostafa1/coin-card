@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-900 dark:text-emerald-100">لوحة التحكم</h1>
        <p class="mt-3 text-sm text-slate-900">مرحباً {{ auth()->user()->name }}، هذه لوحة التحكم الأساسية لحسابك.</p>
    </div>
@endsection
