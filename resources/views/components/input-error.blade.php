@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-2 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @endforeach
    </div>
@endif
