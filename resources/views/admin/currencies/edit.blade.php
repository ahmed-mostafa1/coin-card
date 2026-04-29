@extends('layouts.app')

@section('title', 'تعديل عملة')

@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="تعديل عملة" subtitle="تحديث بيانات العملة وسعر التحويل." />
        <form method="POST" action="{{ route('admin.currencies.update', $currency) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            @include('admin.currencies.form', ['currency' => $currency])
        </form>
    </x-card>
@endsection
