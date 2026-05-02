@props([
    'id',
    'name',
    'value' => '',
    'dir' => 'auto',
    'placeholder' => '',
])

<div {{ $attributes->merge(['class' => 'cc-rich-editor']) }}
     data-rich-text-editor
     data-upload-url="{{ route('admin.services.description-images.store') }}"
     data-placeholder="{{ $placeholder }}">
    <textarea id="{{ $id }}" name="{{ $name }}" class="hidden" data-rich-text-input>{{ $value }}</textarea>
    <div class="cc-rich-editor__area" data-rich-text-area dir="{{ $dir }}"></div>
</div>
