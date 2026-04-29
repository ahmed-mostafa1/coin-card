@extends('layouts.app')
@section('title', 'تعديل حقل توثيق')
@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="تعديل حقل توثيق" subtitle="تحديث إعدادات الحقل." />
        <form method="POST" action="{{ route('admin.verification-fields.update', $field) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            @include('admin.verification-fields.form')
        </form>
    </x-card>
@endsection
