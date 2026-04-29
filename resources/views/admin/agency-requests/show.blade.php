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
            @php
                $payload = $agencyRequest->payload ?: array_filter([
                    'contact_number' => $agencyRequest->contact_number,
                    'full_name' => $agencyRequest->full_name,
                    'region' => $agencyRequest->region,
                    'starting_amount' => $agencyRequest->starting_amount,
                ], fn ($value) => filled($value));
                $fields = \App\Models\AgencyRequestField::orderBy('sort_order')->orderBy('id')->get()->keyBy('name_key');
            @endphp

            @forelse ($payload as $key => $value)
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ $fields[$key]->localized_label ?? $key }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $value }}</p>
                </div>
            @empty
                <p class="col-span-2 text-sm text-slate-500">لا توجد بيانات.</p>
            @endforelse
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
