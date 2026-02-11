@php
    $appName = config('app.name', 'Arab 8BP');
    $title = $title ?? $appName;
    $subtitle = $subtitle ?? null;
    $preheader = $preheader ?? strip_tags((string) $title);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.6;
        }
        .email-shell {
            width: 100%;
            padding: 28px 0;
        }
        .email-container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
        }
        .brand {
            margin: 0 0 18px;
            text-align: center;
            font-size: 44px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }
        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }
        .card-head {
            padding: 24px 32px 8px;
        }
        .card-title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }
        .card-subtitle {
            margin: 8px 0 0;
            font-size: 14px;
            color: #6b7280;
        }
        .card-body {
            padding: 24px 32px 28px;
            font-size: 16px;
            color: #374151;
        }
        .section {
            margin: 0;
        }
        .lang-title {
            margin: 0 0 14px;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
        .section p {
            margin: 0 0 16px;
        }
        .separator {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 22px 0;
        }
        .btn {
            display: inline-block;
            background: #111827;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 24px;
        }
        .btn-wrap {
            margin: 18px 0;
            text-align: center;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            font-size: 14px;
        }
        .details-table tr:nth-child(odd) {
            background: #f9fafb;
        }
        .details-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .details-table tr:last-child td {
            border-bottom: 0;
        }
        .details-table .label {
            width: 40%;
            font-weight: 600;
            color: #4b5563;
        }
        .details-table .value {
            color: #111827;
            font-weight: 500;
            word-break: break-word;
        }
        .details-table .highlight-value {
            color: #111827;
            font-weight: 700;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            line-height: 1.4;
            font-weight: 700;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-processing {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .status-done {
            background: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        .muted {
            color: #6b7280;
            font-size: 14px;
        }
        .action-text {
            margin: 16px 0 0;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            color: #374151;
            font-size: 14px;
        }
        .signature {
            margin-top: 10px;
            color: #374151;
        }
        .rtl {
            direction: rtl;
            text-align: right;
        }
        .ltr {
            direction: ltr;
            text-align: left;
        }
        .card-foot {
            padding: 18px 32px 24px;
            border-top: 1px solid #f3f4f6;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
        @media only screen and (max-width: 640px) {
            .email-shell {
                padding: 0;
            }
            .card {
                border-radius: 0;
                border-left: 0;
                border-right: 0;
            }
            .card-head,
            .card-body,
            .card-foot {
                padding-left: 16px;
                padding-right: 16px;
            }
            .brand {
                margin: 18px 0;
                font-size: 34px;
            }
            .details-table .label {
                width: 44%;
            }
        }
    </style>
</head>
<body>
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">{{ $preheader }}</div>
    <div class="email-shell">
        <div class="email-container">
            <h1 class="brand">Arab 8BP</h1>
            <div class="card">
                @if(!empty($title) || !empty($subtitle))
                    <div class="card-head">
                        @if(!empty($title))
                            <h2 class="card-title">{{ $title }}</h2>
                        @endif
                        @if(!empty($subtitle))
                            <p class="card-subtitle">{{ $subtitle }}</p>
                        @endif
                    </div>
                @endif
                <div class="card-body">
                    {!! $slot ?? '' !!}
                </div>
                <div class="card-foot">
                    &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
