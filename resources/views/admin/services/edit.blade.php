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
                            <x-text-input id="price_per_unit" name="price_per_unit" type="number" step="any" min="0.000000001" :value="old('price_per_unit', $service->price_per_unit)" />
                            <x-input-error :messages="$errors->get('price_per_unit')" />
                            <p class="mt-1 text-xs text-slate-500">سيتم حساب السعر الإجمالي تلقائياً بناءً على الكمية</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                            <x-input-label for="min_quantity" :value="__('messages.min_quantity')" />
                                <x-text-input id="min_quantity" name="min_quantity" type="number" min="1" :value="old('min_quantity', $service->min_quantity ?? 1)" />
                                <x-input-error :messages="$errors->get('min_quantity')" />
                            </div>
                            <div>
                                <x-input-label for="max_quantity" :value="__('messages.max_quantity')" />
                                <x-text-input id="max_quantity" name="max_quantity" type="number" min="1" :value="old('max_quantity', $service->max_quantity)" />
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

                <div>
                    <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $service->sort_order)" />
                </div>

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