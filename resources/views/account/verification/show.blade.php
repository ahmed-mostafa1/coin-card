@extends('layouts.app')

@section('title', 'توثيق الحساب')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="توثيق الحساب" subtitle="أرسل البيانات المطلوبة لمراجعة التوثيق من الإدارة." />

        @if (session('status'))
            <div
                class="mt-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-slate-50 p-4 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <x-user-badge :user="auth()->user()" class="h-7 w-7" />
                <div>
                    <p class="font-semibold text-slate-800 dark:text-white">
                        {{ auth()->user()->is_verified ? 'حسابك موثق' : 'حسابك غير موثق' }}
                    </p>
                    @if ($verificationRequest)
                        <p class="text-sm text-slate-900 dark:text-slate-100">آخر طلب: {{ $verificationRequest->status }}</p>
                        @if ($verificationRequest->review_note)
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">ملاحظة الإدارة:
                                {{ $verificationRequest->review_note }}
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        @if ($fields->isEmpty())
            <div
                class="mt-6 rounded-2xl border border-dashed border-slate-50 dark:border-slate-700 p-8 text-center text-sm text-slate-900 dark:text-slate-100">
                نموذج التوثيق غير متاح حالياً.</div>
        @else
            <form method="POST" action="{{ route('account.verification.store') }}" enctype="multipart/form-data"
                class="mt-6 space-y-4">
                @csrf
                @foreach ($fields as $field)
                    <div class="space-y-2">
                        <x-input-label :for="$field->name_key" :value="$field->localized_label" />
                        @if ($field->type === 'textarea')
                            <textarea id="{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" rows="3" class="store-input"
                                placeholder="{{ $field->localized_placeholder }}"
                                @required($field->is_required)>{{ old('fields.' . $field->name_key) }}</textarea>
                        @elseif ($field->type === 'select')
                            <x-select id="{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" :required="$field->is_required">
                                <option value="">اختر</option>
                                @foreach (($field->options ?? []) as $option)
                                    <option value="{{ $option }}" @selected(old('fields.' . $field->name_key) === $option)>{{ $option }}
                                    </option>
                                @endforeach
                            </x-select>
                        @elseif ($field->type === 'file' || $field->type === 'image')
                            <input id="{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" type="file" class="store-input"
                                accept="{{ $field->type === 'image' ? 'image/*' : '.jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf' }}"
                                @required($field->is_required)>
                        @elseif ($field->type === 'camera')
                            <div class="rounded-2xl border border-slate-50 p-3 dark:border-slate-700" data-camera-field>
                                <input id="{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" type="hidden" data-camera-input
                                    @required($field->is_required)>
                                <video class="hidden w-full rounded-xl" autoplay playsinline data-camera-video></video>
                                <canvas class="hidden" data-camera-canvas></canvas>
                                <img class="mt-3 hidden max-h-64 rounded-xl border border-slate-50 object-contain" data-camera-preview
                                    alt="camera preview">
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button"
                                        class="rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-900 dark:text-emerald-50"
                                        data-camera-start>فتح الكاميرا</button>
                                    <button type="button" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white"
                                        data-camera-capture>التقاط الصورة</button>
                                </div>
                                <p class="mt-2 text-xs text-slate-700 dark:text-slate-50">هذا الحقل يتطلب التقاط صورة مباشرة ولا يقبل
                                    رفع صورة من المعرض.</p>
                            </div>
                        @else
                            <x-text-input :id="$field->name_key" name="fields[{{ $field->name_key }}]" :type="$field->type === 'date' ? 'date' : ($field->type === 'number' ? 'number' : 'text')" :value="old('fields.' . $field->name_key)"
                                :placeholder="$field->localized_placeholder" :required="$field->is_required" />
                        @endif
                        <x-input-error :messages="$errors->get('fields.' . $field->name_key)" />
                    </div>
                @endforeach
                <x-primary-button class="w-full">إرسال طلب التوثيق</x-primary-button>
            </form>
        @endif
    </x-card>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-camera-field]').forEach((field) => {
                const video = field.querySelector('[data-camera-video]');
                const canvas = field.querySelector('[data-camera-canvas]');
                const input = field.querySelector('[data-camera-input]');
                const preview = field.querySelector('[data-camera-preview]');
                let stream = null;
                field.querySelector('[data-camera-start]')?.addEventListener('click', async () => {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    video.srcObject = stream;
                    video.classList.remove('hidden');
                });
                field.querySelector('[data-camera-capture]')?.addEventListener('click', () => {
                    if (!stream) return;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    input.value = dataUrl;
                    preview.src = dataUrl;
                    preview.classList.remove('hidden');
                    stream.getTracks().forEach((track) => track.stop());
                    video.classList.add('hidden');
                    stream = null;
                });
            });
        });
    </script>
@endsection