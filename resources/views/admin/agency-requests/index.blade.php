@extends('layouts.app')

@section('title', 'طلبات الوكالة')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">طلبات الوكالة</h1>
                <p class="mt-2 text-sm text-slate-600">متابعة الطلبات الواردة.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="border-b border-slate-200 text-slate-500">
                    <tr>
                        <th class="py-2">الاسم</th>
                        <th class="py-2">رقم التواصل</th>
                        <th class="py-2">المنطقة</th>
                        <th class="py-2">المبلغ</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">عرض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($requests as $request)
                        <tr>
                            <td class="py-3 text-slate-700">{{ $request->full_name }}</td>
                            <td class="py-3 text-slate-700">{{ $request->contact_number }}</td>
                            <td class="py-3 text-slate-700">{{ $request->region }}</td>
                            <td class="py-3 text-slate-700">{{ number_format($request->starting_amount, 2) }} USD</td>
                            <td class="py-3 text-slate-500">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3">
                                <a href="{{ route('admin.agency-requests.show', $request) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">لا توجد طلبات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $requests->links() }}</div>
    </div>
@endsection
