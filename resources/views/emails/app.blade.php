@props([
    'subject' => '',
    'title' => null,
    'introLines' => [],
    'outroLines' => [],
    'actionText' => null,
    'actionUrl' => null,
    'helperText' => null,
    'fallbackUrl' => null,
    'signatureName' => 'Arab 8BP',
    'showDivider' => true,
    'direction' => 'ltr', // 'ltr' or 'rtl'
])

<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
  <title>{{ $subject }}</title>
  <style>
    .hover-underline:hover {
      text-decoration: underline !important;
    }
    @media (max-width: 600px) {
      .sm-w-full {
        width: 100% !important;
      }
      .sm-px-24 {
        padding-left: 24px !important;
        padding-right: 24px !important;
      }
      .sm-py-32 {
        padding-top: 32px !important;
        padding-bottom: 32px !important;
      }
    }
  </style>
</head>
<body style="margin: 0; width: 100%; padding: 0; word-break: break-word; -webkit-font-smoothing: antialiased; background-color: #F3F4F6;">
  <div style="display: none;">{{ $subject }}</div>
  <div role="article" aria-roledescription="email" aria-label="{{ $subject }}" lang="en">
    <table style="width: 100%; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;" cellpadding="0" cellspacing="0" role="presentation">
      <tr>
        <td align="center" style="background-color: #F3F4F6;">
          <table class="sm-w-full" style="width: 600px;" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td class="sm-py-32 sm-px-24" style="padding: 40px;">
                <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td style="border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
                      <!-- Main card table -->
                      <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                          <td style="padding: 40px 36px;" dir="{{ $direction }}">
                            <!-- Header -->
                            @include('emails.partials.header')

                            <!-- Title -->
                            @if ($title)
                              <h1 style="margin: 0 0 24px; font-size: 24px; font-weight: 700; color: #111827;">{{ $title }}</h1>
                            @endif

                            <!-- Intro Lines -->
                            @foreach ($introLines as $line)
                              <p style="margin: 0 0 24px; font-size: 16px; line-height: 1.75; color: #6B7280;">
                                {!! $line !!}
                              </p>
                            @endforeach

                            <!-- Slot for custom content like OTP or order details -->
                            {{ $slot }}

                            <!-- Action Button -->
                            @if ($actionUrl && $actionText)
                              @include('emails.partials.button', ['url' => $actionUrl, 'text' => $actionText])
                            @endif

                            <!-- Outro Lines -->
                            @foreach ($outroLines as $line)
                              <p style="margin: 24px 0 0; font-size: 16px; line-height: 1.75; color: #6B7280;">
                                {!! $line !!}
                              </p>
                            @endforeach

                            <!-- Signature -->
                            <p style="margin: 24px 0 0; font-size: 16px; line-height: 1.75; color: #6B7280;">
                              Regards,<br>The {{ $signatureName }} Team
                            </p>

                            <!-- Divider -->
                            @if ($showDivider)
                                @include('emails.partials.divider')
                            @endif

                            <!-- Footer / Helper -->
                            @include('emails.partials.footer', ['helperText' => $helperText, 'fallbackUrl' => $fallbackUrl])

                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <!-- Footer outside card -->
                  <tr>
                    <td style="padding: 32px; text-align: center; font-size: 12px; color: #6B7280;">
                      <p style="margin: 0 0 4px;">&copy; {{ date('Y') }} {{ $signatureName }}. All rights reserved.</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>