@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
    @php
        $heroImage = $categories->first()?->image_path ? asset('storage/' . $categories->first()->image_path) : null;
    @endphp

    <div class="store-shell space-y-6">
        <x-store.hero :image="$heroImage" :alt="__('messages.home')" />

        <x-store.notice :text="__('messages.wholesale_notice')" />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-filter-list="categories">
            @forelse ($categories as $category)
                <x-store.category-card :title="$category->name" :href="route('categories.show', $category->slug)"
                    :image="$category->image_path ? asset('storage/' . $category->image_path) : null"
                    searchTarget="categories" />
            @empty
                <x-empty-state :message="__('messages.no_categories')" class="sm:col-span-2 lg:col-span-4" />
            @endforelse
        </div>

        <div class="grid gap-4 pt-4 text-center text-sm text-slate-700 sm:grid-cols-2 lg:grid-cols-4">
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-3-3v6m-7 6h12a2 2 0 0 0 2-2V7.828a2 2 0 0 0-.586-1.414l-2.828-2.828A2 2 0 0 0 14.172 3H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2Z" />
                </svg>
                <p class="font-semibold">خدمة تصميم وبرمجة</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="font-semibold">الدفع عند الاستلام</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 11.25 11.25 9M12 15l3 3 6-6m-9 6H9l-6 6V3a2.25 2.25 0 0 1 2.25-2.25h9A2.25 2.25 0 0 1 16.5 3v4.5" />
                </svg>
                <p class="font-semibold">فروع على مستوى دولة</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-rose-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="m3.75 9 7.5-6 7.5 6v9.75A1.25 1.25 0 0 1 17.5 20H6.5a1.25 1.25 0 0 1-1.25-1.25V9Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 20v-6h6v6" />
                </svg>
                <p class="font-semibold">تم معالجة الطلب بشكل سريع وآمن</p>
            </div>
        </div>
    </div>
@endsection
