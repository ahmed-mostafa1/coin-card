@extends('layouts.app')

@section('title', $service->name)

@section('content')
    @php
        $availableBalance = $wallet?->balance ?? 0;
        $basePrice = $service->variants->count() ? $service->variants->min('price') : $service->price;
        $isBaseInsufficient = $availableBalance < $basePrice;
        $heroImage = $service->category->image_path
            ? asset('storage/' . $service->category->image_path)
            : ($service->image_path
                ? asset('storage/' . $service->image_path)
                : null);
    @endphp

    <div class="store-shell space-y-6">
        <x-store.hero :image="$heroImage" :alt="$service->name" />

        <x-store.notice :text="__('messages.wholesale_notice')" />

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                @if (session('status'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->has('balance'))
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        {{ $errors->first('balance') }}
                    </div>
                @endif

                <div class="store-card p-6">
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 pb-4">
                        @if ($service->image_path)
                            <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->name }}"
                                class="h-16 w-16 rounded-xl object-cover">
                        @endif
                        <div class="space-y-1">
                            <h1 class="text-xl font-bold text-slate-900">{{ $service->name }}</h1>
                            <p class="text-sm text-slate-600">{{ $service->category->name }}</p>
                            @auth
                                <p class="text-xs text-slate-500">
                                    {{ __('messages.available_balance_text') }}:
                                    <span class="font-semibold text-emerald-700">{{ number_format($availableBalance, 2) }}
                                        USD</span>
                                </p>
                            @endauth
                        </div>
                    </div>

                    <form method="POST" action="{{ route('services.purchase', $service->slug) }}" class="mt-4 space-y-4">
                        @csrf

                        @if ($service->variants->count())
                            <div class="space-y-2">
                                <p class="text-sm font-semibold text-slate-800">{{ __('messages.choose_package') }}</p>
                                <div class="space-y-2">
                                    @foreach ($service->variants->sortBy('sort_order') as $variant)
                                        <label
                                            class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 transition hover:border-emerald-200">
                                            <span class="flex items-center gap-2">
                                                <input type="radio" name="variant_id" value="{{ $variant->id }}"
                                                    data-price="{{ $variant->price }}"
                                                    class="text-emerald-600 focus:ring-emerald-500"
                                                    @checked(old('variant_id') == $variant->id) required>
                                                <span>{{ $variant->name }}</span>
                                            </span>
                                            <span class="font-semibold text-emerald-700">${{ number_format($variant->price, 2) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('variant_id')" />
                            </div>
                        @endif

                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <p>{{ __('messages.current_price') }}:
                                <span id="current-price" class="font-semibold text-emerald-700">
                                    {{ number_format($service->variants->count() ? $service->variants->min('price') : $service->price, 2) }}
                                </span> USD
                            </p>
                            <p id="insufficient-message"
                                class="mt-2 text-xs text-rose-600 {{ $isBaseInsufficient ? '' : 'hidden' }}">
                                {{ __('messages.insufficient_balance_msg') }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ __('messages.held_amount_notice') }}</p>
                        </div>

                        @forelse ($service->formFields->sortBy('sort_order') as $field)
                            <div class="space-y-1">
                                <label for="field_{{ $field->name_key }}"
                                    class="block text-sm font-semibold text-slate-800">{{ $field->label }}</label>
                                @if ($field->type === 'text')
                                    <input id="field_{{ $field->name_key }}"
                                        name="fields[{{ $field->name_key }}]"
                                        type="text"
                                        value="{{ old('fields.' . $field->name_key) }}"
                                        placeholder="{{ $field->placeholder }}"
                                        class="store-input"
                                        {{ $field->is_required ? 'required' : '' }}>
                                @else
                                    <textarea id="field_{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" rows="3"
                                        placeholder="{{ $field->placeholder }}" class="store-input"
                                        {{ $field->is_required ? 'required' : '' }}>{{ old('fields.' . $field->name_key) }}</textarea>
                                @endif
                                <x-input-error :messages="$errors->get('fields.' . $field->name_key)" />
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('messages.no_required_fields') }}</p>
                        @endforelse

                        @auth
                            <button id="purchase-button" type="submit"
                                class="w-full rounded-lg bg-[#f2a900] px-5 py-3 text-sm font-semibold text-slate-900 transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-60">
                                {{ __('messages.buy_now') }}
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:brightness-105">
                                {{ __('messages.login_to_purchase') }}
                            </a>
                        @endauth
                    </form>
                </div>
            </div>

            <div>
                <div class="store-card relative overflow-hidden p-4">
                    @php
                        $displayPrice = $service->variants->count()
                            ? $service->variants->min('price')
                            : $service->price;
                    @endphp
                    <span
                        class="absolute right-3 top-3 rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold text-white shadow">
                        ${{ number_format($displayPrice, 2) }}
                    </span>

                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        @if ($service->image_path)
                            <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->name }}"
                                class="h-48 w-full object-cover">
                        @endif
                    </div>

                    <div class="pt-4 text-center">
                        <p class="text-base font-semibold text-slate-900">{{ $service->name }}</p>
                        <p class="mt-1 text-xs text-slate-600">
                            {{ $service->description ?: __('messages.default_delivery_eta') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const availableBalance = parseFloat({{ json_encode($availableBalance) }} || 0);
                const priceElement = document.getElementById('current-price');
                const insufficientMessage = document.getElementById('insufficient-message');
                const purchaseButton = document.getElementById('purchase-button');
                const variantInputs = document.querySelectorAll('input[name="variant_id"]');

                const getSelectedPrice = () => {
                    const checked = document.querySelector('input[name="variant_id"]:checked');
                    if (checked && checked.dataset.price) {
                        return parseFloat(checked.dataset.price);
                    }
                    return parseFloat(priceElement?.textContent || 0);
                };

                const updateState = () => {
                    const price = getSelectedPrice();
                    if (priceElement && !Number.isNaN(price)) {
                        priceElement.textContent = price.toFixed(2);
                    }

                    if (!purchaseButton) {
                        return;
                    }

                    if (availableBalance < price) {
                        purchaseButton.setAttribute('disabled', 'disabled');
                        if (insufficientMessage) {
                            insufficientMessage.classList.remove('hidden');
                        }
                    } else {
                        purchaseButton.removeAttribute('disabled');
                        if (insufficientMessage) {
                            insufficientMessage.classList.add('hidden');
                        }
                    }
                };

                variantInputs.forEach((input) => {
                    input.addEventListener('change', updateState);
                });

                updateState();
            });
        </script>
    @endauth
@endsection
