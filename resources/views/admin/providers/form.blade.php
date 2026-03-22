@php
    use App\Models\ApiProvider;
    $isEdit = $provider !== null;
    $title  = $isEdit ? 'تعديل المزود: ' . $provider->name : 'إضافة مزود جديد';
    $action = $isEdit
        ? route('admin.providers.update', $provider)
        : route('admin.providers.store');
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <x-card :hover="false">
        <x-page-header :title="$title">
            <a href="{{ route('admin.providers.index') }}"
               class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                ← العودة للمزودين
            </a>
        </x-page-header>

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $action }}" id="provider-form" class="mt-6 space-y-6">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- ═══ 1. Basic Info ════════════════════════════════════════════ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h2 class="text-base font-semibold text-slate-700 dark:text-white mb-4">المعلومات الأساسية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم المزود *</label>
                        <x-text-input name="name" :value="old('name', $provider?->name)" class="w-full" required />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">المعرّف (Slug) *</label>
                        <x-text-input name="slug" :value="old('slug', $provider?->slug)" dir="ltr" class="w-full" required />
                        <x-input-error :messages="$errors->get('slug')" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">الرابط الأساسي *</label>
                        <x-text-input name="base_url" type="url" :value="old('base_url', $provider?->base_url)" dir="ltr" placeholder="https://example.com/api" class="w-full" required />
                        <x-input-error :messages="$errors->get('base_url')" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مهلة الاتصال (ثانية)</label>
                        <x-text-input name="timeout" type="number" min="5" max="120" :value="old('timeout', $provider?->timeout ?? 25)" class="w-full" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">ملاحظات داخلية</label>
                        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('notes', $provider?->notes) }}</textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $provider?->is_active) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-emerald-600">
                        <label for="is_active" class="text-sm font-medium text-slate-600 dark:text-slate-300">مفعّل</label>
                    </div>
                </div>
            </div>

            {{-- ═══ 2. Authentication ══════════════════════════════════════════ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h2 class="text-base font-semibold text-slate-700 dark:text-white mb-4">المصادقة (Authentication)</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">نوع المصادقة *</label>
                    <x-select name="auth_type" id="auth_type" class="w-full" required>
                        @foreach($authTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('auth_type', $provider?->auth_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div id="auth-apikey" class="grid grid-cols-1 md:grid-cols-2 gap-4 auth-fields">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">API Key</label>
                        <x-text-input name="cred_key" dir="ltr" class="w-full" placeholder="{{ $isEdit ? '(فارغ = الإبقاء على القيمة الحالية)' : '' }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">API Secret</label>
                        <x-text-input name="cred_secret" dir="ltr" class="w-full" placeholder="{{ $isEdit ? '(فارغ = الإبقاء على القيمة الحالية)' : '' }}" />
                    </div>
                </div>
                <div id="auth-basic" class="grid grid-cols-1 md:grid-cols-2 gap-4 auth-fields hidden">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم المستخدم</label>
                        <x-text-input name="cred_username" dir="ltr" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">كلمة المرور</label>
                        <x-text-input name="cred_password" type="password" dir="ltr" class="w-full" />
                    </div>
                </div>
                <div id="auth-bearer" class="auth-fields hidden">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Bearer Token</label>
                    <x-text-input name="cred_token" dir="ltr" class="w-full" />
                </div>
            </div>

            {{-- ═══ 3. Catalog Config ══════════════════════════════════════════ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h2 class="text-base font-semibold text-slate-700 dark:text-white mb-4">إعدادات الكتالوج</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مسار قائمة المنتجات *</label>
                        <x-text-input name="catalog_endpoint" dir="ltr" :value="old('catalog_endpoint', $provider?->catalog_endpoint)" placeholder="/api/products/" class="w-full" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">طريقة الطلب</label>
                        <x-select name="catalog_method" class="w-full">
                            <option value="GET" {{ old('catalog_method', $provider?->catalog_method ?? 'GET') === 'GET' ? 'selected' : '' }}>GET</option>
                            <option value="POST" {{ old('catalog_method', $provider?->catalog_method) === 'POST' ? 'selected' : '' }}>POST</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                            مسار قائمة المنتجات في الاستجابة
                            <span class="text-xs text-slate-400">(مثال: results أو data.items)</span>
                        </label>
                        <x-text-input name="catalog_response_path" dir="ltr" :value="old('catalog_response_path', $provider?->catalog_response_path)" placeholder="results" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                            مسار الإجمالي في الاستجابة
                            <span class="text-xs text-slate-400">(مثال: count)</span>
                        </label>
                        <x-text-input name="catalog_count_path" dir="ltr" :value="old('catalog_count_path', $provider?->catalog_count_path)" placeholder="count" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                            مسار رابط الصفحة التالية
                            <span class="text-xs text-slate-400">(مثال: next)</span>
                        </label>
                        <x-text-input name="catalog_next_path" dir="ltr" :value="old('catalog_next_path', $provider?->catalog_next_path)" placeholder="next" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">نوع التصفح</label>
                        <x-select name="catalog_pagination_type" id="pagination_type" class="w-full">
                            <option value="none" {{ old('catalog_pagination_type', $provider?->catalog_pagination_type ?? 'none') === 'none' ? 'selected' : '' }}>بدون تصفح</option>
                            <option value="page_number" {{ old('catalog_pagination_type', $provider?->catalog_pagination_type) === 'page_number' ? 'selected' : '' }}>رقم الصفحة</option>
                            <option value="offset" {{ old('catalog_pagination_type', $provider?->catalog_pagination_type) === 'offset' ? 'selected' : '' }}>Offset</option>
                        </x-select>
                    </div>
                    <div id="pagination-params" class="{{ old('catalog_pagination_type', $provider?->catalog_pagination_type ?? 'none') === 'none' ? 'hidden' : '' }} md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم معامل الصفحة</label>
                            <x-text-input name="catalog_page_param" dir="ltr" :value="old('catalog_page_param', $provider?->catalog_page_param)" placeholder="page" class="w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم معامل الحجم</label>
                            <x-text-input name="catalog_page_size_param" dir="ltr" :value="old('catalog_page_size_param', $provider?->catalog_page_size_param)" placeholder="page_size" class="w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">حجم الصفحة</label>
                            <x-text-input name="catalog_page_size" type="number" min="1" max="1000" :value="old('catalog_page_size', $provider?->catalog_page_size ?? 50)" class="w-full" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ 4. Field Map ════════════════════════════════════════════════ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h2 class="text-base font-semibold text-slate-700 dark:text-white mb-1">خريطة حقول المنتج</h2>
                <p class="text-xs text-slate-400 mb-4">اسم الحقل كما يرد في استجابة الـ API. يدعم النقاط للوصول المتداخل (مثال: <code>data.title</code>).</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم المنتج (name) *</label>
                        <x-text-input name="field_map_name" dir="ltr" :value="old('field_map_name', $provider?->field_map_name)" placeholder="name" class="w-full" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">السعر (price) *</label>
                        <x-text-input name="field_map_price" dir="ltr" :value="old('field_map_price', $provider?->field_map_price)" placeholder="price" class="w-full" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">المعرّف الخارجي</label>
                        <x-text-input name="field_map_external_id" dir="ltr" :value="old('field_map_external_id', $provider?->field_map_external_id)" placeholder="id" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">الوصف</label>
                        <x-text-input name="field_map_description" dir="ltr" :value="old('field_map_description', $provider?->field_map_description)" placeholder="description" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">النوع</label>
                        <x-text-input name="field_map_type" dir="ltr" :value="old('field_map_type', $provider?->field_map_type)" placeholder="product_type" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">التوفر</label>
                        <x-text-input name="field_map_available" dir="ltr" :value="old('field_map_available', $provider?->field_map_available)" placeholder="available" class="w-full" />
                    </div>
                </div>
            </div>

            {{-- ═══ 5. Order Placement ══════════════════════════════════════════ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h2 class="text-base font-semibold text-slate-700 dark:text-white mb-1">تنفيذ الطلبات</h2>
                <p class="text-xs text-slate-400 mb-4">اتركه فارغاً إذا كان تنفيذ الطلبات يدوياً.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مسار إنشاء الطلب</label>
                        <x-text-input name="order_endpoint" dir="ltr" :value="old('order_endpoint', $provider?->order_endpoint)" placeholder="/api/orders/create/" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">طريقة الطلب</label>
                        <x-select name="order_method" class="w-full">
                            <option value="POST" {{ old('order_method', $provider?->order_method ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                            <option value="GET" {{ old('order_method', $provider?->order_method) === 'GET' ? 'selected' : '' }}>GET</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم حقل رقم طلبنا <span class="text-xs text-slate-400">(مثال: client_order_id)</span></label>
                        <x-text-input name="order_our_ref_field" dir="ltr" :value="old('order_our_ref_field', $provider?->order_our_ref_field)" placeholder="client_order_id" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم حقل المنتج في الـ API <span class="text-xs text-slate-400">(مثال: product)</span></label>
                        <x-text-input name="order_product_field" dir="ltr" :value="old('order_product_field', $provider?->order_product_field)" placeholder="product" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم حقل رقم حساب المستخدم في الـ API <span class="text-xs text-slate-400">(مثال: account_id)</span></label>
                        <x-text-input name="order_account_id_field" dir="ltr" :value="old('order_account_id_field', $provider?->order_account_id_field)" placeholder="account_id" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مفتاح رقم الحساب في نموذج الطلب <span class="text-xs text-slate-400">(مثال: customer_identifier)</span></label>
                        <x-text-input name="order_account_id_source" dir="ltr" :value="old('order_account_id_source', $provider?->order_account_id_source)" placeholder="customer_identifier" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم حقل الكمية في الـ API <span class="text-xs text-slate-400">(فارغ = دائماً 1)</span></label>
                        <x-text-input name="order_quantity_field" dir="ltr" :value="old('order_quantity_field', $provider?->order_quantity_field)" placeholder="quantity" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مفتاح الكمية في نموذج الطلب <span class="text-xs text-slate-400">(فارغ = دائماً 1)</span></label>
                        <x-text-input name="order_quantity_source" dir="ltr" :value="old('order_quantity_source', $provider?->order_quantity_source)" placeholder="external_amount" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مسار معرّف المعاملة في الاستجابة</label>
                        <x-text-input name="order_transaction_id_path" dir="ltr" :value="old('order_transaction_id_path', $provider?->order_transaction_id_path)" placeholder="transaction_id" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مسار حالة التنفيذ في الاستجابة</label>
                        <x-text-input name="order_exec_status_path" dir="ltr" :value="old('order_exec_status_path', $provider?->order_exec_status_path)" placeholder="execution_status" class="w-full" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                            حقول إضافية ثابتة (JSON)
                            <span class="text-xs text-slate-400">(مثال: {{"{"}} "currency": "SAR" {{"}"}})</span>
                        </label>
                        <textarea name="order_extra_fields" rows="2" dir="ltr"
                                  class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-mono text-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('order_extra_fields', $provider?->order_extra_fields ? json_encode($provider->order_extra_fields) : '') }}</textarea>
                        <x-input-error :messages="$errors->get('order_extra_fields')" />
                    </div>
                </div>
            </div>

            {{-- ═══ 6. Order Status ════════════════════════════════════════════ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h2 class="text-base font-semibold text-slate-700 dark:text-white mb-1">مزامنة حالة الطلب</h2>
                <p class="text-xs text-slate-400 mb-4">اتركه فارغاً إذا كانت المزامنة تتم يدوياً.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مسار استعلام الحالة</label>
                        <x-text-input name="status_endpoint" dir="ltr" :value="old('status_endpoint', $provider?->status_endpoint)" placeholder="/api/orders/status/" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">طريقة الطلب</label>
                        <x-select name="status_method" class="w-full">
                            <option value="GET" {{ old('status_method', $provider?->status_method ?? 'GET') === 'GET' ? 'selected' : '' }}>GET</option>
                            <option value="POST" {{ old('status_method', $provider?->status_method) === 'POST' ? 'selected' : '' }}>POST</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم معامل رقم الطلب</label>
                        <x-text-input name="status_id_param" dir="ltr" :value="old('status_id_param', $provider?->status_id_param)" placeholder="client_order_id" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">مسار حالة التنفيذ في الاستجابة</label>
                        <x-text-input name="status_exec_path" dir="ltr" :value="old('status_exec_path', $provider?->status_exec_path)" placeholder="execution_status" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">قيم النجاح (مفصولة بفواصل)</label>
                        <x-text-input name="status_success_values" dir="ltr"
                                      :value="old('status_success_values', $provider?->status_success_values ? implode(',', $provider->status_success_values) : '')"
                                      placeholder="success,completed,done" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">قيم الفشل (مفصولة بفواصل)</label>
                        <x-text-input name="status_failed_values" dir="ltr"
                                      :value="old('status_failed_values', $provider?->status_failed_values ? implode(',', $provider->status_failed_values) : '')"
                                      placeholder="failed,error,cancelled" class="w-full" />
                    </div>
                </div>
            </div>

            {{-- ═══ Test Connection ════════════════════════════════════════════ --}}
            @if($isEdit)
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 p-5">
                    <h2 class="text-base font-semibold text-slate-700 dark:text-white mb-2">اختبار الاتصال</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">سيتم استدعاء نقطة الكتالوج بالإعدادات المحفوظة حالياً وعرض أول 3 منتجات.</p>
                    <button type="button" id="test-btn"
                            class="rounded-xl border border-emerald-300 dark:border-emerald-700 px-4 py-2 text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition"
                            data-url="{{ route('admin.providers.test', $provider) }}">
                        اختبار الاتصال الآن
                    </button>
                    <div id="test-result" class="mt-4 hidden">
                        <p id="test-message" class="text-sm font-medium mb-2"></p>
                        <pre id="test-preview" class="bg-slate-100 dark:bg-slate-800 text-xs p-3 rounded-xl overflow-x-auto hidden"></pre>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex gap-3">
                <x-primary-button type="submit">{{ $isEdit ? 'حفظ التعديلات' : 'إضافة المزود' }}</x-primary-button>
                <a href="{{ route('admin.providers.index') }}"
                   class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </x-card>
@endsection

@push('scripts')
<script>
    // ── Auth type toggling ──────────────────────────────────────────────
    const authSelect = document.getElementById('auth_type');
    function toggleAuthFields() {
        document.querySelectorAll('.auth-fields').forEach(el => el.classList.add('hidden'));
        const v = authSelect.value;
        if (v === 'api_key_header' || v === 'api_key_query') {
            document.getElementById('auth-apikey').classList.remove('hidden');
        } else if (v === 'basic_auth_header' || v === 'basic_auth_query') {
            document.getElementById('auth-basic').classList.remove('hidden');
        } else if (v === 'bearer_header') {
            document.getElementById('auth-bearer').classList.remove('hidden');
        }
    }
    authSelect.addEventListener('change', toggleAuthFields);
    toggleAuthFields();

    // ── Pagination visibility ──────────────────────────────────────────
    const paginationSelect = document.getElementById('pagination_type');
    const paginationParams = document.getElementById('pagination-params');
    paginationSelect?.addEventListener('change', () => {
        paginationParams.classList.toggle('hidden', paginationSelect.value === 'none');
    });

    // ── Test connection ────────────────────────────────────────────────
    const testBtn = document.getElementById('test-btn');
    if (testBtn) {
        testBtn.addEventListener('click', async () => {
            testBtn.disabled = true;
            testBtn.textContent = 'جارٍ الاختبار...';
            const resultBox = document.getElementById('test-result');
            const messageEl = document.getElementById('test-message');
            const previewEl = document.getElementById('test-preview');
            resultBox.classList.remove('hidden');
            try {
                const res = await fetch(testBtn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });
                const data = await res.json();
                messageEl.textContent = data.message;
                messageEl.className = 'text-sm font-medium mb-2 ' + (data.ok ? 'text-emerald-600' : 'text-red-600');
                if (data.ok && data.preview?.length) {
                    previewEl.textContent = JSON.stringify(data.preview, null, 2);
                    previewEl.classList.remove('hidden');
                } else {
                    previewEl.classList.add('hidden');
                }
            } catch (e) {
                messageEl.textContent = 'خطأ: ' + e.message;
                messageEl.className = 'text-sm font-medium mb-2 text-red-600';
            } finally {
                testBtn.disabled = false;
                testBtn.textContent = 'اختبار الاتصال الآن';
            }
        });
    }
</script>
@endpush
