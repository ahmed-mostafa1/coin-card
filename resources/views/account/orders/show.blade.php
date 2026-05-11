@extends('layouts.app')

@section('title', __('messages.order_details_title'))

@section('content')
    <div class="grid gap-4 lg:grid-cols-3">
        <x-card class="p-4 sm:p-6 lg:col-span-2" :hover="false">
            @if (session('status'))
                <div
                    class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    @if ($order->service?->image_path)
                        <img src="{{ asset('storage/' . $order->service->image_path) }}" alt="{{ $order->service->name }}"
                            class="h-14 w-14 rounded-xl object-cover">
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white">
                            {{ __('messages.order_id_title', ['id' => $order->id]) }}</h1>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-50 truncate">
                            {{ $order->service->name }}</p>
                    </div>
                </div>
                @php
                    $shareText = __('messages.order_id_title', ['id' => $order->id]) . "\n";
                    $shareText .= (app()->getLocale() === 'ar' ? "الخدمة: " : "Service: ") . $order->service->name . "\n";
                    $shareText .= (app()->getLocale() === 'ar' ? "السعر: " : "Price: ") . number_format($order->price_at_purchase, 2) . " USD\n";
                    $statusName = [
                        'new' => __('messages.status_new'),
                        'processing' => __('messages.status_processing'),
                        'done' => __('messages.status_done'),
                        'rejected' => __('messages.status_rejected'),
                        'cancelled' => __('messages.status_cancelled'),
                    ][$order->status] ?? $order->status;
                    $shareText .= (app()->getLocale() === 'ar' ? "الحالة: " : "Status: ") . $statusName . "\n";

                    if (count($order->payload)) {
                        $shareText .= "\n--- " . (app()->getLocale() === 'ar' ? "تفاصيل الطلب" : "Order Details") . " ---\n";
                        foreach ($order->payload as $key => $value) {
                            $displayValue = is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);
                            $label = $fieldLabels[$key] ?? \Illuminate\Support\Str::headline((string) $key);
                            $shareText .= $label . ": " . trim($displayValue) . "\n";
                        }
                    }
                @endphp
                <div class="flex space-between gap-3 mt-4">
                    <button type="button" data-share-order data-share-text="{{ $shareText }}"
                        class="flex items-center gap-1 text-sm  text-emerald-50 hover:text-emerald-100 transition">
                        <i class="fa-solid fa-share-nodes"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مشاركة' : 'Share' }}</span>
                    </button>
                    <a href="{{ route('account.orders') }}"
                        class="text-sm text-emerald-50 hover:text-emerald-100 transition">{{ __('messages.back_to_orders') }}</a>
                </div>
            </div>

            <div
                class="mt-4 overflow-hidden rounded-2xl border border-slate-50 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/80">
                <table class="w-full table-fixed text-right text-sm">
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        <tr>
                            <th scope="row"
                                class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-slate-900 dark:text-slate-50">
                                {{ __('messages.price_label') }}</th>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                                @if ($order->discount_percentage > 0)
                                    <span
                                        class="block text-xs font-medium text-slate-100 line-through">{{ number_format($order->original_price, 2) }}
                                        USD</span>
                                    <span class="mt-1 flex flex-wrap items-center gap-2">
                                        <span
                                            class="text-emerald-900 dark:text-emerald-100">{{ number_format($order->price_at_purchase, 2) }}
                                            USD</span>
                                        <span
                                            class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-900 dark:bg-emerald-900/30 dark:text-emerald-100">
                                            -{{ number_format($order->discount_percentage, 0) }}%
                                        </span>
                                    </span>
                                @else
                                    {{ number_format($order->price_at_purchase, 2) }} USD
                                @endif
                            </td>
                        </tr>
                        @if ($order->discount_percentage > 0)
                            <tr>
                                <th scope="row"
                                    class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-emerald-700 dark:text-emerald-100">
                                    {{ app()->getLocale() == 'ar' ? 'المبلغ الموفر' : 'Amount Saved' }}</th>
                                <td class="px-4 py-3 text-sm font-semibold text-emerald-900 dark:text-emerald-100">
                                    {{ number_format($order->discount_amount, 2) }} USD
                                    <span
                                        class="mt-1 block text-xs font-medium text-emerald-700 dark:text-emerald-100">{{ app()->getLocale() == 'ar' ? 'خصم الحساب' : 'Account Discount' }}</span>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th scope="row"
                                class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-slate-900 dark:text-slate-50">
                                {{ __('messages.held_amount') }}</th>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                                {{ number_format($order->amount_held, 2) }} USD</td>
                        </tr>
                        <tr>
                            <th scope="row"
                                class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-slate-900 dark:text-slate-50">
                                {{ __('messages.status') }}</th>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                                @if ($order->status === 'new')
                                    <x-badge type="new">{{ __('messages.status_new') }}</x-badge>
                                @elseif ($order->status === 'processing')
                                    <x-badge type="processing">{{ __('messages.status_processing') }}</x-badge>
                                @elseif ($order->status === 'done')
                                    <x-badge type="done">{{ __('messages.status_done') }}</x-badge>
                                @elseif ($order->status === 'rejected')
                                    <x-badge type="rejected">{{ __('messages.status_rejected') }}</x-badge>
                                @else
                                    <x-badge>{{ __('messages.status_cancelled') }}</x-badge>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"
                                class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-slate-900 dark:text-slate-50">
                                {{ __('messages.package') }}</th>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                                {{ $order->variant?->name ?? __('messages.base_price_label') }}</td>
                        </tr>
                        <tr>
                            <th scope="row"
                                class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-slate-900 dark:text-slate-50">
                                {{ __('messages.date') }}</th>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                                {{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th scope="row"
                                class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-slate-900 dark:text-slate-50">
                                {{ __('messages.settled_at_label') }}</th>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                                {{ $order->settled_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row"
                                class="w-2/5 px-4 py-3 align-top text-xs font-semibold text-slate-900 dark:text-slate-50">
                                {{ __('messages.released_at_label') }}</th>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                                {{ $order->released_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 rounded-2xl border border-slate-50 dark:border-slate-700 p-4">
                <p class="text-xs text-slate-700 dark:text-slate-50">{{ __('messages.order_data_label') }}</p>
                @if (count($order->payload))
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        @foreach ($order->payload as $key => $value)
                            @php
                                $displayValue = is_scalar($value) || $value === null
                                    ? (string) $value
                                    : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                $displayValue = trim((string) $displayValue);

                                if ($key === 'service_discount_percent' && is_numeric($displayValue)) {
                                    $displayValue = number_format((float) $displayValue, 2) . '%';
                                }

                                if (in_array($key, ['offer_amount', 'payable_after_discount'], true) && is_numeric($displayValue)) {
                                    $displayValue = number_format((float) $displayValue, 2) . ' USD';
                                }

                                $isUrl = filter_var($displayValue, FILTER_VALIDATE_URL) !== false;
                                $isImageByExt = preg_match('/\.(jpg|jpeg|png|webp|gif|bmp|svg)(\?.*)?$/i', $displayValue) === 1;
                                $isImagePath = !$isUrl
                                    && $isImageByExt
                                    && !\Illuminate\Support\Str::contains($displayValue, [' ', "\n", "\r", "\t"]);

                                $imageUrl = null;
                                if ($isUrl && $isImageByExt) {
                                    $imageUrl = $displayValue;
                                } elseif ($isImagePath) {
                                    $imageUrl = asset('storage/' . ltrim($displayValue, '/'));
                                }
                            @endphp
                            <div
                                class="rounded-xl border border-slate-50 bg-slate-50/70 p-3 dark:border-slate-700 dark:bg-slate-800/50">
                                <p class="text-xs text-slate-700 dark:text-slate-50">
                                    {{ $fieldLabels[$key] ?? \Illuminate\Support\Str::headline((string) $key) }}</p>

                                @if ($imageUrl)
                                    <a href="{{ $imageUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block">
                                        <img src="{{ $imageUrl }}" alt="{{ $fieldLabels[$key] ?? (string) $key }}"
                                            class="h-40 w-auto max-w-full rounded-lg border border-slate-50 bg-white object-contain">
                                    </a>
                                @elseif ($isUrl)
                                    <a href="{{ $displayValue }}" target="_blank" rel="noopener noreferrer"
                                        class="mt-2 block break-all text-sm font-semibold text-emerald-900 hover:underline">
                                        {{ $displayValue }}
                                    </a>
                                @else
                                    <p class="mt-2 break-words text-sm font-semibold text-slate-900 dark:text-slate-50">
                                        {{ $displayValue !== '' ? $displayValue : '-' }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-900">{{ __('messages.no_additional_data') }}</p>
                @endif
            </div>

            @if ($order->admin_note)
                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-900 dark:text-emerald-50 dark:bg-emerald-900 dark:border-emerald-700">
                    {{ __('messages.admin_note_label', ['note' => $order->admin_note]) }}
                </div>
            @endif
        </x-card>

        <x-card class="p-8" :hover="false">
            <h2 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">
                {{ __('messages.order_history_title') }}</h2>
            @php
                $statusLabels = [
                    'new' => __('messages.status_new'),
                    'processing' => __('messages.status_processing'),
                    'done' => __('messages.status_done'),
                    'rejected' => __('messages.status_rejected'),
                    'cancelled' => __('messages.status_cancelled'),
                ];
            @endphp
            <div class="mt-4 space-y-4">
                @forelse ($order->events as $event)
                    @php
                        $actorLabel = __('messages.actor_system');
                        if ($event->actor) {
                            $actorLabel = $event->actor->id === auth()->id() ? __('messages.actor_you') : __('messages.actor_admin');
                        }
                    @endphp
                    <div class="rounded-2xl border border-slate-50 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">
                                {{ $event->message ?? __('messages.update_label') }}</p>
                            <span class="text-xs text-slate-100">{{ $event->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-700 dark:text-slate-50">
                            {{ __('messages.actor_label', ['actor' => $actorLabel]) }}</p>
                        @if ($event->old_status || $event->new_status)
                                    <p class="mt-1 text-xs text-slate-900 dark:text-slate-50">
                                        {{ __('messages.status_change_label', [
                                'old' => $statusLabels[$event->old_status] ?? '-',
                                'new' => $statusLabels[$event->new_status] ?? '-'
                            ]) }}
                                    </p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-900">{{ __('messages.no_updates_yet') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>

    @if (session('status'))
        <div id="order-status-toast" data-message="{{ session('status') }}"
            data-position="{{ app()->getLocale() === 'ar' ? 'top-start' : 'top-end' }}" hidden></div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toastElement = document.getElementById('order-status-toast');
                if (!toastElement) {
                    return;
                }

                Swal.fire({
                    toast: true,
                    position: toastElement.dataset.position || 'top-end',
                    icon: 'success',
                    title: toastElement.dataset.message || '',
                    showConfirmButton: false,
                    timer: 2600,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shareButton = document.querySelector('[data-share-order]');
            if (!shareButton) return;

            shareButton.addEventListener('click', async () => {
                const title = shareButton.dataset.shareTitle || document.title;
                const text = shareButton.dataset.shareText || '';

                try {
                    let shared = false;
                    if (navigator.share) {
                        try {
                            await navigator.share({ title, text });
                            shared = true;
                        } catch (err) {
                            if (err.name === 'AbortError') return;
                            // Silently ignore other errors and fallback to clipboard
                        }
                    }

                    if (!shared) {
                        await navigator.clipboard.writeText(text);
                        Swal.fire({ toast: true, position: 'top', icon: 'success', title: 'تم نسخ تفاصيل الطلب', showConfirmButton: false, timer: 1800 });
                    }
                } catch (error) {
                    Swal.fire({ toast: true, position: 'top', icon: 'error', title: 'تعذر مشاركة الطلب', showConfirmButton: false, timer: 1800 });
                }
            });
        });
    </script>
@endsection