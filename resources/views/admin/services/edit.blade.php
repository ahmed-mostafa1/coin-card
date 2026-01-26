@extends('layouts.app')

@section('title', 'تعديل خدمة')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-emerald-700">تعديل خدمة</h1>
                <a href="{{ route('admin.services.index') }}" class="text-sm text-emerald-700">عودة للخدمات</a>
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
                    <x-input-label for="category_id" value="التصنيف" />
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" required>
                        @foreach ($categories as $category)
                            @php
                                $label = $category->parent ? $category->parent->name . ' ? ' . $category->name : $category->name;
                            @endphp
                            <option value="{{ $category->id }}" @selected(old('category_id', $service->category_id) == $category->id)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>

                <div>
                    <x-input-label for="name" value="اسم الخدمة" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $service->name)" required />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="slug" value="المعرف المختصر" />
                    <x-text-input id="slug" name="slug" type="text" :value="old('slug', $service->slug)" />
                    <x-input-error :messages="$errors->get('slug')" />
                </div>

                <div>
                    <x-input-label for="description" value="الوصف" />
                    <textarea id="description" name="description" rows="4" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">{{ old('description', $service->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                <div>
                    <x-input-label for="price" value="السعر" />
                    <x-text-input id="price" name="price" type="number" step="0.01" min="1" :value="old('price', $service->price)" required />
                    <x-input-error :messages="$errors->get('price')" />
                </div>

                <div>
                    <x-input-label for="image" value="صورة (اختياري)" />
                    <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                    <x-input-error :messages="$errors->get('image')" />
                    @if ($service->image_path)
                        <p class="mt-2 text-xs text-slate-500">الصورة الحالية محفوظة.</p>
                    @endif
                </div>

                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ $service->is_active ? 'checked' : '' }}>
                    <label for="is_active">تفعيل الخدمة</label>
                </div>

                <div>
                    <x-input-label for="sort_order" value="ترتيب العرض" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $service->sort_order)" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button>تحديث</x-primary-button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700">الباقات</h2>
                    <a href="{{ route('admin.services.variants.index', $service) }}" class="text-sm text-emerald-700">إدارة الباقات</a>
                </div>
                <p class="mt-3 text-sm text-slate-600">يمكنك إضافة خيارات متعددة بسعر مختلف لكل باقة.</p>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-emerald-700">حقول النموذج</h2>
                <a href="{{ route('admin.services.fields.create', $service) }}" class="text-sm text-emerald-700">إضافة حقل</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($service->formFields->sortBy('sort_order') as $field)
                    <div class="rounded-2xl border border-slate-200 p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ $field->label }}</p>
                                <p class="text-xs text-slate-500">{{ $field->name_key }} - {{ $field->type }}</p>
                            </div>
                            <a href="{{ route('admin.services.fields.edit', [$service, $field]) }}" class="text-xs text-emerald-700">تعديل</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">لم يتم إضافة حقول بعد.</p>
                @endforelse
            </div>
            </div>
        </div>
    </div>
@endsection