@extends('layouts.app')

@section('title', 'تفاصيل طلب الوكالة')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">طلب وكالة #{{ $agencyRequest->id }}</h1>
                <p class="mt-2 text-sm text-slate-600">تم الإرسال في {{ $agencyRequest->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <a href="{{ route('admin.agency-requests.index') }}" class="text-sm text-emerald-700 hover:text-emerald-900">عودة للقائمة</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">الاسم</p>
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ $agencyRequest->full_name }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">رقم التواصل</p>
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ $agencyRequest->contact_number }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">المنطقة</p>
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ $agencyRequest->region }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">المبلغ المتاح</p>
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($agencyRequest->starting_amount, 2) }} ر.س</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.agency-requests.destroy', $agencyRequest) }}" class="mt-6">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50" onclick="return confirm('هل أنت متأكد من حذف الطلب؟')">
                حذف الطلب
            </button>
        </form>
    </div>
@endsection
