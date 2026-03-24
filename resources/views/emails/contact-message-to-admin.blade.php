@php
    $locale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $payload['subject'] }}</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;padding:24px;">
        <h1 style="margin:0 0 18px;font-size:22px;color:#0f172a;">{{ __('contact.email_content.heading') }}</h1>

        <table style="width:100%;border-collapse:collapse;">
            <tbody>
                @if ($submittedBy)
                    <tr>
                        <td style="padding:10px 0;font-weight:700;width:180px;">{{ __('contact.email_content.submitted_by') }}</td>
                        <td style="padding:10px 0;">{{ $submittedBy->name }} (#{{ $submittedBy->id }})</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:10px 0;font-weight:700;width:180px;">{{ __('contact.email_content.name') }}</td>
                    <td style="padding:10px 0;">{{ $payload['name'] }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0;font-weight:700;">{{ __('contact.email_content.email') }}</td>
                    <td style="padding:10px 0;">{{ $payload['email'] }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0;font-weight:700;">{{ __('contact.email_content.subject') }}</td>
                    <td style="padding:10px 0;">{{ $payload['subject'] }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top:18px;border-top:1px solid #e2e8f0;padding-top:18px;">
            <p style="margin:0 0 10px;font-weight:700;">{{ __('contact.email_content.message') }}</p>
            <div style="white-space:pre-line;line-height:1.8;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;">{{ $payload['message'] }}</div>
        </div>
    </div>
</body>
</html>
