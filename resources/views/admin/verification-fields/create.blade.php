@extends('layouts.app')
@section('title', 'إضافة حقل توثيق')
@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="إضافة حقل توثيق" subtitle="أنشئ حقلاً جديداً في نموذج التوثيق." />
        <form method="POST" action="{{ route('admin.verification-fields.store') }}" class="mt-6 space-y-4">
            @csrf
            @include('admin.verification-fields.form')
        </form>
    </x-card>
@endsection
