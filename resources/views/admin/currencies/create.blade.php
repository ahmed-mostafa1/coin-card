@extends('layouts.app')

@section('title', 'إضافة عملة')

@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="إضافة عملة" subtitle="أدخل بيانات العملة وسعر تحويل وحدة واحدة منها إلى USD." />
        <form method="POST" action="{{ route('admin.currencies.store') }}" class="mt-6 space-y-4">
            @csrf
            @include('admin.currencies.form', ['currency' => null])
        </form>
    </x-card>
@endsection
