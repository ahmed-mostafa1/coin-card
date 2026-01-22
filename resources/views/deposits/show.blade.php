@extends('layouts.app')

@section('title', __('messages.deposit_method_details_title'))

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center gap-4">
                @if ($paymentMethod->icon_path)
                    <img src="{{ asset('storage/' . $paymentMethod->icon_path) }}" alt="{{ $paymentMethod->name }}"
                        class="h-14 w-14 rounded-xl object-cover">
                @else
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        {{ mb_substr($paymentMethod->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">{{ $paymentMethod->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ __('messages.deposit_instruction_desc') }}</p>
                </div>
            </div>

            <div
                class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm text-slate-700 whitespace-pre-line">
                {{ $paymentMethod->instructions }}
            </div>

            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500">{{ __('messages.account_number_label') }}</p>
                        <p class="mt-2 text-lg font-semibold text-slate-700" data-account-number>
                            {{ $paymentMethod->account_number }}</p>
                        <p class="mt-1 text-xs text-emerald-600 hidden" data-copy-feedback>
                            {{ __('messages.copied_feedback') }}</p>
                    </div>
                    <button type="button"
                        class="rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50"
                        data-copy-button>{{ __('messages.copy_button') }}</button>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">{{ __('messages.submit_deposit_request') }}</h2>

            <form method="POST" action="{{ route('deposit.store', $paymentMethod->slug) }}" enctype="multipart/form-data"
                class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="amount" :value="__('messages.transfer_amount')" />
                    <x-text-input id="amount" name="amount" type="number" step="0.01"
                        min="{{ config('coin-card.deposit_min_amount') }}"
                        max="{{ config('coin-card.deposit_max_amount') }}" :value="old('amount')" required />
                    <x-input-error :messages="$errors->get('amount')" />
                </div>

                <div>
                    <x-input-label for="proof" :value="__('messages.transfer_proof')" />
                    <input id="proof" name="proof" type="file" required
                        class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                    <x-input-error :messages="$errors->get('proof')" />
                </div>

                <x-primary-button class="w-full">{{ __('messages.confirm_deposit') }}</x-primary-button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const button = document.querySelector('[data-copy-button]');
            const value = document.querySelector('[data-account-number]');
            const feedback = document.querySelector('[data-copy-feedback]');

            if (!button || !value) {
                return;
            }

            const showFeedback = () => {
                if (!feedback) {
                    return;
                }
                feedback.classList.remove('hidden');
                setTimeout(() => feedback.classList.add('hidden'), 2000);
            };

            button.addEventListener('click', async () => {
                const textToCopy = value.textContent?.trim() ?? '';
                if (!textToCopy) {
                    return;
                }

                try {
                    await navigator.clipboard.writeText(textToCopy);
                    showFeedback();
                } catch (error) {
                    const temp = document.createElement('textarea');
                    temp.value = textToCopy;
                    temp.style.position = 'fixed';
                    temp.style.opacity = '0';
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    document.body.removeChild(temp);
                    showFeedback();
                }
            });
        });
    </script>

@endsection