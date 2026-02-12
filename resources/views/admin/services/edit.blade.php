@extends('layouts.app')

@section('title', __('messages.edit_service'))

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm lg:col-span-2 transition-colors duration-200">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.edit_service') }}</h1>
                <a href="{{ route('admin.services.index') }}" class="text-sm text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300">{{ __('messages.back_to_services') }}</a>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="category_id" :value="__('messages.category')" />
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach ($categories as $category)
                            @php
                                $label = $category->parent ? $category->parent->name . ' › ' . $category->name : $category->name;
                            @endphp
                            <option value="{{ $category->id }}" @selected(old('category_id', $service->category_id) == $category->id)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>

                <div>
                    <x-input-label for="name" :value="__('messages.service_name_ar')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $service->name)" required />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="name_en" :value="__('messages.service_name_en')" />
                    <x-text-input id="name_en" name="name_en" type="text" :value="old('name_en', $service->name_en)" />
                    <x-input-error :messages="$errors->get('name_en')" />
                </div>

                <div>
                    <x-input-label for="slug" :value="__('messages.slug')" />
                    <x-text-input id="slug" name="slug" type="text" :value="old('slug', $service->slug)" />
                    <x-input-error :messages="$errors->get('slug')" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('messages.description_ar')" />
                    <textarea id="description" name="description" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $service->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                <div>
                    <x-input-label for="description_en" :value="__('messages.description_en')" />
                    <textarea id="description_en" name="description_en" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">{{ old('description_en', $service->description_en) }}</textarea>
                    <x-input-error :messages="$errors->get('description_en')" />
                </div>

                <div>
                    <x-input-label for="additional_rules" value="قواعد إضافية (عربي)" />
                    <textarea id="additional_rules" name="additional_rules" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">{{ old('additional_rules', $service->additional_rules) }}</textarea>
                    <x-input-error :messages="$errors->get('additional_rules')" />
                </div>

                <div>
                    <x-input-label for="additional_rules_en" value="قواعد إضافية (English)" />
                    <textarea id="additional_rules_en" name="additional_rules_en" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500" dir="ltr">{{ old('additional_rules_en', $service->additional_rules_en) }}</textarea>
                    <x-input-error :messages="$errors->get('additional_rules_en')" />
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 space-y-4">
                    <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                        <input id="is_quantity_based" name="is_quantity_based" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $service->is_quantity_based ? 'checked' : '' }} onchange="toggleQuantityFields(this)">
                        <label for="is_quantity_based">خدمة بالكمية (سعر ثابت للقطعة)</label>
                    </div>
                    <div id="quantity-fields" class="{{ $service->is_quantity_based ? '' : 'hidden' }} space-y-4">
                        <div>
                            <x-input-label for="price_per_unit" value="السعر لكل قطعة" />
                            <x-text-input id="price_per_unit" name="price_per_unit" type="number" step="any" min="0.000000000001" lang="en" dir="ltr" :value="old('price_per_unit', $service->price_per_unit ? rtrim(rtrim(number_format($service->price_per_unit, 12, '.', ''), '0'), '.') : '')" />
                            <x-input-error :messages="$errors->get('price_per_unit')" />
                            <p class="mt-1 text-xs text-slate-500">سيتم حساب السعر الإجمالي تلقائياً بناءً على الكمية</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                            <x-input-label for="min_quantity" :value="__('messages.min_quantity')" />
                                <x-text-input id="min_quantity" name="min_quantity" type="number" min="1" lang="en" dir="ltr" :value="old('min_quantity', $service->min_quantity ?? 1)" />
                                <x-input-error :messages="$errors->get('min_quantity')" />
                            </div>
                            <div>
                                <x-input-label for="max_quantity" :value="__('messages.max_quantity')" />
                                <x-text-input id="max_quantity" name="max_quantity" type="number" min="1" lang="en" dir="ltr" :value="old('max_quantity', $service->max_quantity)" />
                                <x-input-error :messages="$errors->get('max_quantity')" />
                                <p class="mt-1 text-xs text-slate-500">{{ __('messages.any_quantity') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="price" :value="__('messages.price')" />
                    <x-text-input id="price" name="price" type="number" step="0.01" min="0.01" :value="old('price', $service->price)" />
                    <x-input-error :messages="$errors->get('price')" />
                    <p class="mt-1 text-xs text-slate-500">السعر الافتراضي (يتم تجاهله إذا كانت الخدمة بالكمية أو لديها باقات)</p>
                </div>

                <div>
                    <x-input-label for="image" :value="__('messages.image_optional')" />
                    <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 dark:file:bg-emerald-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:text-emerald-300">
                    <x-input-error :messages="$errors->get('image')" />
                    <p class="mt-1 text-xs text-slate-500">{{ __('messages.recommended_size') }}: 500x500 px</p>
                    @if ($service->image_path)
                    @endif
                </div>

                <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                    <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $service->is_active ? 'checked' : '' }}>
                    <label for="is_active">{{ __('messages.activate_service') }}</label>
                </div>

                <div class="mt-4 border-t border-slate-100 dark:border-slate-700 pt-4">
                     <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400 mb-4">{{ __('messages.offer_settings') ?? (app()->getLocale() == 'ar' ? 'إعدادات العرض' : 'Offer Settings') }}</h2>
                     
                     <div class="space-y-4">
                        <div>
                            <x-input-label for="offer_image" :value="__('messages.offer_image') ?? (app()->getLocale() == 'ar' ? 'صورة العرض' : 'Offer Image')" />
                            <input id="offer_image" name="offer_image" type="file" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 dark:file:bg-emerald-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:text-emerald-300">
                            <x-input-error :messages="$errors->get('offer_image')" />
                            
                            @if ($service->offer_image_path)
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('messages.current_offer_image') ?? (app()->getLocale() == 'ar' ? 'صورة العرض الحالية:' : 'Current Offer Image:') }}</p>
                                    <img src="{{ asset('storage/' . $service->offer_image_path) }}" alt="Offer Image" class="mt-2 h-20 rounded-lg object-cover">
                                </div>
                            @endif
                            <p class="mt-1 text-xs text-slate-500">{{ __('messages.recommended_size') }}: 500x500 px</p>
                        </div>

                        <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                            <input id="is_offer_active" name="is_offer_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $service->is_offer_active ? 'checked' : '' }}>
                            <label for="is_offer_active">{{ __('messages.activate_offer') ?? (app()->getLocale() == 'ar' ? 'تفعيل العرض' : 'Activate Offer') }}</label>
                        </div>
                     </div>
                </div>

                <div class="rounded-2xl border border-rose-200 dark:border-rose-800/60 bg-rose-50/40 dark:bg-rose-900/10 p-4 space-y-4">
                    <h2 class="text-lg font-semibold text-rose-700 dark:text-rose-300">{{ __('messages.limited_time_offer') }}</h2>

                    <div class="space-y-2">
                        <label for="is_limited_offer_label_active" class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                            <input id="is_limited_offer_label_active" name="is_limited_offer_label_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-rose-600 focus:ring-rose-500" @checked(old('is_limited_offer_label_active', $service->is_limited_offer_label_active))>
                            {{ __('messages.activate_limited_offer_label') }}
                        </label>
                        <div>
                            <x-input-label for="limited_offer_label" :value="__('messages.limited_offer_label_text')" />
                            <x-text-input id="limited_offer_label" name="limited_offer_label" type="text" :value="old('limited_offer_label', $service->limited_offer_label)" />
                            <x-input-error :messages="$errors->get('limited_offer_label')" />
                        </div>
                        <div>
                            <x-input-label for="limited_offer_label_en" :value="__('messages.limited_offer_label_text_en')" />
                            <x-text-input id="limited_offer_label_en" name="limited_offer_label_en" type="text" :value="old('limited_offer_label_en', $service->limited_offer_label_en)" />
                            <x-input-error :messages="$errors->get('limited_offer_label_en')" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="is_limited_offer_countdown_active" class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                            <input id="is_limited_offer_countdown_active" name="is_limited_offer_countdown_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-rose-600 focus:ring-rose-500" @checked(old('is_limited_offer_countdown_active', $service->is_limited_offer_countdown_active))>
                            {{ __('messages.activate_limited_offer_countdown') }}
                        </label>
                        <div>
                            <x-input-label for="limited_offer_ends_at" :value="__('messages.limited_offer_ends_at')" />
                            <input id="limited_offer_ends_at" name="limited_offer_ends_at" type="datetime-local" value="{{ old('limited_offer_ends_at', $service->limited_offer_ends_at?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-rose-500 focus:ring-rose-500">
                            <x-input-error :messages="$errors->get('limited_offer_ends_at')" />
                            <p class="mt-1 text-xs text-slate-500">{{ __('messages.limited_offer_auto_deactivate_hint') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $service->sort_order)" />
                </div>

                @if (($service->source ?? 'manual') === 'marketcard99')
                    <div class="rounded-2xl border border-sky-200 dark:border-sky-800/60 bg-sky-50/50 dark:bg-sky-900/10 p-4 space-y-4">
                        <h2 class="text-base font-semibold text-sky-800 dark:text-sky-300">إعدادات مزود MarketCard99</h2>

                        <div class="grid gap-4 sm:grid-cols-2 text-xs text-slate-600 dark:text-slate-300">
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                                <p class="font-semibold">مصدر الخدمة</p>
                                <p class="mt-1">MarketCard99</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                                <p class="font-semibold">External Product ID</p>
                                <p class="mt-1">{{ $service->external_product_id ?? '-' }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                                <p class="font-semibold">Provider Status</p>
                                <p class="mt-1">{{ $service->provider_is_available ? 'Available' : 'Unavailable' }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                                <p class="font-semibold">Last Synced</p>
                                <p class="mt-1">{{ $service->provider_last_synced_at?->format('Y-m-d H:i') ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="sync_rule_mode" value="وضع قواعد المزامنة" />
                                <select id="sync_rule_mode" name="sync_rule_mode" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="auto" @selected(old('sync_rule_mode', $service->sync_rule_mode) === 'auto')>تلقائي</option>
                                    <option value="manual" @selected(old('sync_rule_mode', $service->sync_rule_mode) === 'manual')>يدوي</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" name="requires_customer_id" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" @checked(old('requires_customer_id', $service->requires_customer_id))>
                                    يتطلب معرف المستخدم
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" name="requires_amount" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" @checked(old('requires_amount', $service->requires_amount))>
                                    يتطلب مبلغ خارجي
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" name="requires_purchase_password" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" @checked(old('requires_purchase_password', $service->requires_purchase_password))>
                                    كلمة سر الشراء مطلوبة
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex gap-3">
                    <x-primary-button>{{ __('messages.update') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.variants') }}</h2>
                    <a href="{{ route('admin.services.variants.index', $service) }}" class="text-sm text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300">{{ __('messages.manage_variants') }}</a>
                </div>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ __('messages.variants_hint') }}</p>
            </div>

            <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.form_fields') }}</h2>
                <a href="{{ route('admin.services.fields.create', $service) }}" class="text-sm text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300">{{ __('messages.add_field') }}</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($service->formFields->sortBy('sort_order') as $field)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $field->label }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $field->name_key }} - {{ $field->type }}</p>
                            </div>
                            <a href="{{ route('admin.services.fields.edit', [$service, $field]) }}" class="text-xs text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300">{{ __('messages.edit') }}</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('messages.no_fields_yet') }}</p>
                @endforelse
            </div>
            </div>
        </div>
    </div>

    <script>
        function toggleQuantityFields(checkbox) {
            const quantityFields = document.getElementById('quantity-fields');
            if (checkbox.checked) {
                quantityFields.classList.remove('hidden');
            } else {
                quantityFields.classList.add('hidden');
            }
        }
    </script>
@endsection
