@props([
    'placeholder' => '',
    'target' => null,
    'name' => 'q',
    'value' => '',
])

<input type="text"
    name="{{ $name }}"
    value="{{ $value }}"
    placeholder="{{ $placeholder }}"
    @if ($target) data-filter-target="{{ $target }}" @endif
    class="store-search" />
