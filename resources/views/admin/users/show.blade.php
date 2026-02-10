@extends('layouts.app')

@section('title', 'ملف المستخدم')


@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ $user->name }}</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $user->email }}</p>
                </div>
                <div class="text-sm text-slate-600 dark:text-slate-300">
                    <p>تاريخ الإنشاء: {{ $user->created_at->format('Y-m-d') }}</p>
                    <p>الأدوار: {{ $user->roles->pluck('name')->implode('، ') ?: 'بدون دور' }}</p>
                </div>
            </div>


            @if (session('status'))
                <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mt-6 rounded-lg border border-rose-200 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-rose-200 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                @if ($user->is_banned)
                    <span class="rounded-full bg-rose-100 dark:bg-rose-900/40 px-3 py-1 text-rose-700 dark:text-rose-300">محظور</span>
                @endif
                @if ($user->is_frozen)
                    <span class="rounded-full bg-amber-100 dark:bg-amber-900/40 px-3 py-1 text-amber-700 dark:text-amber-300">مجمّد</span>
                @endif
            </div>

            <div class="mt-6 flex flex-wrap gap-3 text-sm">
                <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                    @csrf
                    <button type="submit" class="rounded-full border border-rose-200 dark:border-rose-700 px-4 py-2 font-semibold text-rose-600 dark:text-rose-300">{{ $user->is_banned ? 'إلغاء الحظر' : 'حظر المستخدم' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.users.freeze', $user) }}">
                    @csrf
                    <button type="submit" class="rounded-full border border-amber-200 dark:border-amber-700 px-4 py-2 font-semibold text-amber-700 dark:text-amber-300">{{ $user->is_frozen ? 'إلغاء التجميد' : 'تجميد الحساب' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full border border-slate-200 dark:border-slate-700 px-4 py-2 font-semibold text-slate-600 dark:text-slate-300">حذف المستخدم</button>
                </form>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 items-start">
            <!-- Right Column (RTL) / Left Column (LTR) -->
            <div class="space-y-6">
                <!-- Wallet Card -->
                <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">المحفظة</h2>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">الرصيد المتاح</span>
                            <span class="font-semibold text-slate-700">{{ number_format($wallet->balance, 2) }} USD</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">الرصيد المعلّق</span>
                            <span class="font-semibold text-slate-700">{{ number_format($wallet->held_balance, 2) }} USD</span>
                        </div>
                    </div>
                    <div class="mt-6 border-t border-slate-100 dark:border-slate-700 pt-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-300">مستوى VIP</span>
                            <span class="font-semibold text-emerald-700 dark:text-emerald-400">{{ $vipSummary['current_tier']?->name ?? 'بدون مستوى' }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-300">إجمالي المشتريات</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($vipSummary['spent'] ?? 0, 2) }} USD</span>
                        </div>
                    </div>
                </div>

                <!-- Send Notification Card -->
                <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">إرسال إشعار</h2>
                    <form method="POST" action="{{ route('admin.users.send-notification', $user) }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="notif_title_ar" value="العنوان (عربي)" />
                            <x-text-input id="notif_title_ar" name="title_ar" type="text" class="w-full" :value="old('title_ar')" required />
                        </div>
                        <div>
                            <x-input-label for="notif_title_en" value="العنوان (إنجليزي)" />
                            <x-text-input id="notif_title_en" name="title_en" type="text" class="w-full" :value="old('title_en')" required />
                        </div>
                        <div>
                            <x-input-label for="notif_content_ar" value="المحتوى (عربي)" />
                            <textarea id="notif_content_ar" name="content_ar" rows="2" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white" required>{{ old('content_ar') }}</textarea>
                        </div>
                        <div>
                            <x-input-label for="notif_content_en" value="المحتوى (إنجليزي)" />
                            <textarea id="notif_content_en" name="content_en" rows="2" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white" required>{{ old('content_en') }}</textarea>
                        </div>
                        <x-primary-button class="w-full">إرسال إشعار</x-primary-button>
                    </form>
                </div>
            </div>

            <!-- Left Columns (RTL) / Right Columns (LTR) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Top Row -->
                <div class="grid gap-6 lg:grid-cols-2 items-start">
                    <!-- Add/Deduct Balance Card -->
                    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">إضافة/خصم رصيد</h2>
                        
                        <div class="mt-4 border-b border-slate-100 dark:border-slate-700 pb-4">
                            <h3 class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mb-2">إضافة رصيد</h3>
                            <form method="POST" action="{{ route('admin.users.credit', $user) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="credit_amount" value="المبلغ" />
                                    <x-text-input id="credit_amount" name="amount" type="number" step="0.01" min="0.01" max="100000" :value="old('amount')" required />
                                </div>
                                <div>
                                    <x-input-label for="credit_note" value="ملاحظة (اختياري)" />
                                    <textarea id="credit_note" name="note" rows="2" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white">{{ old('note') }}</textarea>
                                </div>
                                <x-primary-button class="w-full">إضافة الرصيد</x-primary-button>
                            </form>
                        </div>

                        <div class="mt-4 pt-2">
                            <h3 class="text-sm font-bold text-rose-600 dark:text-rose-400 mb-2">خصم رصيد</h3>
                            <form method="POST" action="{{ route('admin.users.debit', $user) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="debit_amount" value="المبلغ" />
                                    <x-text-input id="debit_amount" name="amount" type="number" step="0.01" min="0.01" max="100000" :value="old('amount')" required />
                                </div>
                                <div>
                                    <x-input-label for="debit_note" value="ملاحظة (اختياري)" />
                                    <textarea id="debit_note" name="note" rows="2" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white">{{ old('note') }}</textarea>
                                </div>
                                <button type="submit" class="w-full rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">خصم الرصيد</button>
                            </form>
                        </div>
                    </div>

                    <!-- Send Email Card -->
                    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">إرسال بريد إلكتروني</h2>
                        <form method="POST" action="{{ route('admin.users.send-email', $user) }}" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="email_subject" value="الموضوع" />
                                <x-text-input id="email_subject" name="subject" type="text" class="w-full" :value="old('subject')" required />
                            </div>
                            <div>
                                <x-input-label for="email_message" value="الرسالة" />
                                <textarea id="email_message" name="message" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white" required>{{ old('message') }}</textarea>
                            </div>
                            <x-primary-button class="w-full">إرسال بريد</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Transactions Table -->
            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">آخر حركات الرصيد</h2>
                <x-table class="mt-4">
                    <thead class="border-b border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-300">
                        <tr>
                            <th class="py-2">النوع</th>
                            <th class="py-2">المبلغ</th>
                            <th class="py-2">المرجع</th>
                            <th class="py-2">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="py-3 text-slate-700 dark:text-slate-200">
                                    @if ($transaction->type === 'deposit')
                                        شحن
                                    @elseif ($transaction->type === 'hold')
                                        تعليق
                                    @elseif ($transaction->type === 'settle')
                                        تسوية
                                    @elseif ($transaction->type === 'release')
                                        إرجاع
                                    @else
                                        {{ $transaction->type }}
                                    @endif
                                </td>
                                <td class="py-3 text-slate-700 dark:text-slate-200">{{ number_format($transaction->amount, 2) }} USD</td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">
                                    {{ $transaction->reference_type }} #{{ $transaction->reference_id ?? '-' }}
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-500 dark:text-slate-400">لا توجد حركات حتى الآن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <!-- Deposits Table -->
            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">آخر طلبات الشحن</h2>
                <x-table class="mt-4">
                    <thead class="border-b border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-300">
                        <tr>
                            <th class="py-2">الحالة</th>
                            <th class="py-2">الطريقة</th>
                            <th class="py-2">المبلغ</th>
                            <th class="py-2">المعتمد</th>
                            <th class="py-2">تاريخ المراجعة</th>
                            <th class="py-2">التاريخ</th>
                            <th class="py-2">عرض</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($deposits as $deposit)
                            <tr>
                                <td class="py-3">
                                    @if ($deposit->status === 'pending')
                                        <span class="rounded-full bg-amber-100 dark:bg-amber-900/40 px-3 py-1 text-xs text-amber-700 dark:text-amber-300">قيد المراجعة</span>
                                    @elseif ($deposit->status === 'approved')
                                        <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-300">معتمد</span>
                                    @else
                                        <span class="rounded-full bg-rose-100 dark:bg-rose-900/40 px-3 py-1 text-xs text-rose-700 dark:text-rose-300">مرفوض</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-700 dark:text-slate-200">{{ $deposit->paymentMethod->name }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-200">{{ number_format($deposit->user_amount, 2) }} USD</td>
                                <td class="py-3 text-slate-700 dark:text-slate-200">
                                    {{ $deposit->approved_amount ? number_format($deposit->approved_amount, 2) : '-' }} USD
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">{{ $deposit->reviewed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">{{ $deposit->created_at->format('Y-m-d') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('admin.deposits.show', $deposit) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-slate-500 dark:text-slate-400">لا توجد طلبات شحن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <!-- Orders Table -->
            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">آخر الطلبات</h2>
                <x-table class="mt-4">
                    <thead class="border-b border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-300">
                        <tr>
                            <th class="py-2">الحالة</th>
                            <th class="py-2">الخدمة</th>
                            <th class="py-2">الباقة</th>
                            <th class="py-2">المبلغ</th>
                            <th class="py-2">تسوية/إرجاع</th>
                            <th class="py-2">التاريخ</th>
                            <th class="py-2">عرض</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="py-3">
                                    @if ($order->status === 'new')
                                        <span class="rounded-full bg-amber-100 dark:bg-amber-900/40 px-3 py-1 text-xs text-amber-700 dark:text-amber-300">جديد</span>
                                    @elseif ($order->status === 'processing')
                                        <span class="rounded-full bg-blue-100 dark:bg-blue-900/40 px-3 py-1 text-xs text-blue-700 dark:text-blue-300">قيد التنفيذ</span>
                                    @elseif ($order->status === 'done')
                                        <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-300">تم التنفيذ</span>
                                    @else
                                        <span class="rounded-full bg-rose-100 dark:bg-rose-900/40 px-3 py-1 text-xs text-rose-700 dark:text-rose-300">مرفوض</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-700 dark:text-slate-200">{{ $order->service->name }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-200">{{ $order->variant?->name ?? 'السعر الأساسي' }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-200">{{ number_format($order->amount_held, 2) }} USD</td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">{{ $order->settled_at?->format('Y-m-d H:i') ?? $order->released_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-slate-500 dark:text-slate-400">لا توجد طلبات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>
        </div>
    </div>
@endsection
