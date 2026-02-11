@props(['helperText' => null, 'fallbackUrl' => null])

@if ($helperText)
<table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td>
            <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6B7280;">
                {{ $helperText }}
                @if($fallbackUrl)
                    <a href="{{ $fallbackUrl }}" class="hover-underline" style="color: #111827; text-decoration: none; word-break: break-all;">{{ $fallbackUrl }}</a>
                @endif
            </p>
        </td>
    </tr>
</table>
@endif