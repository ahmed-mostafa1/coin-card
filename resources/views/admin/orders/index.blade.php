@extends('layouts.app')

@section('title', 'الطلبات')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <x-card :hover="false">
        <x-page-header title="الطلبات" subtitle="متابعة طلبات الخدمات.">
            <x-slot:actions>
                <form method="POST" action="{{ route('admin.orders.sync-all-providers') }}" onsubmit="return confirm('هل أنت متأكد من مزامنة جميع الطلبات المعلقة؟ قد يستغرق هذا بعض الوقت.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.665l.314.313a7 7 0 0011.712-3.138.75.75 0 00-1.453-.157zM4.688 8.576a5.5 5.5 0 019.201-2.466l.312.311H11.77a.75.75 0 000 1.5h4.243a.75.75 0 00.75-.75V2.929a.75.75 0 00-1.5 0v2.665l-.314-.313a7 7 0 00-11.712 3.138.75.75 0 001.453.157z" clip-rule="evenodd" />
                        </svg>
                        مزامنة المعلق
                    </button>
                </form>
            </x-slot:actions>
        </x-page-header>

        <form class="mt-6 flex flex-wrap gap-3" method="GET">
            <x-select name="status">
                <option value="">كل الحالات</option>
                <option value="new" @selected(request('status') === 'new')>جديد</option>
                <option value="processing" @selected(request('status') === 'processing')>قيد التنفيذ</option>
                <option value="done" @selected(request('status') === 'done')>تم التنفيذ</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
            </x-select>
            <x-text-input name="q" value="{{ request('q') }}" placeholder=".." />
            <x-button type="submit">تصفية</x-button>
        </form>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="py-2">المستخدم</th>
                        <th class="py-2">الخدمة</th>
                        <th class="py-2">السعر</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($orders as $order)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="py-3 text-slate-700 dark:text-white">{{ $order->user?->name ?? 'مستخدم محذوف' }}<div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->user?->email }}</div></td>
                            <td class="py-3 text-slate-700 dark:text-white">{{ $order->service?->name ?? 'خدمة محذوفة' }}</td>
                            <td class="py-3 text-slate-700 dark:text-white">{{ number_format($order->price_at_purchase, 2) }} USD</td>
                            <td class="py-3">
                                @if ($order->status === 'new')
                                    <x-badge type="new">جديد</x-badge>
                                @elseif ($order->status === 'processing')
                                    <x-badge type="processing">قيد التنفيذ</x-badge>
                                @elseif ($order->status === 'done')
                                    <x-badge type="done">تم التنفيذ</x-badge>
                                @elseif ($order->status === 'rejected')
                                    <x-badge type="rejected">مرفوض</x-badge>
                                @else
                                    <x-badge>ملغي</x-badge>
                                @endif
                            </td>
                            <td class="py-3 text-slate-500 dark:text-slate-400">{{ $order->created_at->format('Y-m-d') }}</td>
                            <td class="py-3">
                                <div class="flex flex-wrap gap-3 items-center text-sm">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">عرض</a>
                                    
                                    @if ($order->service && $order->service->source === \App\Models\Service::SOURCE_DAILYCARD && $order->status === 'processing')
                                        <form method="POST" action="{{ route('admin.orders.sync-provider', $order) }}" title="تحديث الحالة من المزود">
                                            @csrf
                                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">مزامنة</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد طلبات.</td>
                        </tr>
                    @endforelse
                </tbody>
        </x-table>

        <div class="mt-6">{{ $orders->links() }}</div>
    </x-card>
@endsection
