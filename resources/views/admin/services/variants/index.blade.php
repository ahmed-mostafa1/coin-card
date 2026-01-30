@extends('layouts.app')

@section('title', 'باقات الخدمة')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">باقات {{ $service->name }}</h1>
                <p class="mt-2 text-sm text-slate-600">إدارة الباقات والأسعار المختلفة للخدمة.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.services.edit', $service) }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">عودة للخدمة</a>
                <a href="{{ route('admin.services.variants.create', $service) }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">إضافة باقة</a>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="border-b border-slate-200 text-slate-500">
                <tr>
                    <th class="py-2">الاسم</th>
                    <th class="py-2">السعر</th>
                    <th class="py-2">الحالة</th>
                    <th class="py-2">الترتيب</th>
                    <th class="py-2">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($variants as $variant)
                    <tr>
                        <td class="py-3 text-slate-700">{{ $variant->name }}</td>
                        <td class="py-3 text-slate-700">{{ number_format($variant->price, 2) }} USD</td>
                        <td class="py-3">
                            @if ($variant->is_active)
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">مفعلة</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700">متوقفة</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500">{{ $variant->sort_order }}</td>
                        <td class="py-3">
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('admin.services.variants.edit', [$service, $variant]) }}" class="text-emerald-700">تعديل</a>
                                <form method="POST" action="{{ route('admin.services.variants.destroy', [$service, $variant]) }}" onsubmit="return confirm('هل أنت متأكد من حذف الباقة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">لا توجد باقات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
@endsection
