@php
    $slot = $slot ?? '';
@endphp

<x-emails.app
  :subject="$subject ?? ''"
  :title="$title ?? null"
  :introLines="$introLines ?? []"
  :outroLines="$outroLines ?? []"
  :actionText="$actionText ?? null"
  :actionUrl="$actionUrl ?? null"
  :helperText="$helperText ?? null"
  :fallbackUrl="$fallbackUrl ?? null"
  :signatureName="$signatureName ?? 'Arab 8BP'"
  :showDivider="$showDivider ?? true"
  :direction="$direction ?? 'ltr'"
>
  {!! $slot !!}
</x-emails.app>
