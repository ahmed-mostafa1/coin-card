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
  :signatureName="$signatureName ?? 'S7SH.com|شحنك شات'"
  :showDivider="$showDivider ?? true"
  :direction="$direction ?? 'ltr'"
  :arTitle="$arTitle ?? null"
  :enTitle="$enTitle ?? null"
  :arIntroLines="$arIntroLines ?? null"
  :enIntroLines="$enIntroLines ?? null"
  :arOutroLines="$arOutroLines ?? null"
  :enOutroLines="$enOutroLines ?? null"
  :arActionText="$arActionText ?? null"
  :enActionText="$enActionText ?? null"
  :arHelperText="$arHelperText ?? null"
  :enHelperText="$enHelperText ?? null"
>
  {!! $slot !!}
</x-emails.app>
