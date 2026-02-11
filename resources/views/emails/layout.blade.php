@php
    $appName = config('app.name', 'Arab 8bp.in');
    $title = $title ?? $appName;
    $subtitle = $subtitle ?? null;
    $preheader = $preheader ?? strip_tags((string) $title);
    $direction = $direction ?? 'rtl';
@endphp

<!DOCTYPE html>
<html lang="ar" dir="{{ $direction }}">
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
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            line-height: 1.6;
        }
        .email-wrap {
            width: 100%;
            padding: 24px 0;
        }
        .email-card {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
        }
        .email-head {
            padding: 26px 24px 14px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%);
        }
        .brand {
            font-weight: 700;
            font-size: 15px;
            color: #065f46;
            margin: 0 0 10px;
        }
        h1 {
            margin: 0;
            font-size: 20px;
            line-height: 1.4;
            color: #111827;
        }
        .subtitle {
            margin: 8px 0 0;
            color: #475569;
            font-size: 14px;
        }
        .content {
            padding: 22px 24px;
            color: #111827;
            font-size: 14px;
        }
        .content p {
            margin: 0 0 12px;
        }
        .intro-text {
            margin-bottom: 14px;
            color: #1f2937;
            font-weight: 600;
        }
        .section-title {
            margin: 18px 0 8px;
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .details-table tr:nth-child(odd) {
            background: #f8fafc;
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
            width: 42%;
            color: #475569;
            font-weight: 600;
            white-space: nowrap;
        }
        .details-table .value {
            color: #111827;
            font-weight: 500;
            word-break: break-word;
        }
        .details-table .highlight-value {
            color: #065f46;
            font-weight: 700;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-processing {
            background: #dbeafe;
            color: #1e3a8a;
        }
        .status-done {
            background: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        .action-text {
            margin-top: 14px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #334155;
        }
        a {
            color: #0f766e;
            text-decoration: none;
        }
        .rtl {
            direction: rtl;
            text-align: right;
        }
        .email-foot {
            padding: 14px 24px 18px;
            border-top: 1px solid #f1f5f9;
            color: #64748b;
            font-size: 12px;
            text-align: center;
            background: #ffffff;
        }
        @media only screen and (max-width: 640px) {
            .email-wrap {
                padding: 0;
            }
            .email-card {
                border-radius: 0;
                border-left: 0;
                border-right: 0;
            }
            .email-head,
            .content,
            .email-foot {
                padding-left: 16px;
                padding-right: 16px;
            }
            .details-table .label {
                width: 44%;
                white-space: normal;
            }
        }
    </style>
</head>
<body>
<div style="display: none; max-height: 0; overflow: hidden; opacity: 0;">{{ $preheader }}</div>

<div class="email-wrap">
    <div class="email-card">
        <div class="email-head">
            <p class="brand">{{ $appName }}</p>
            @if(!empty($title))
                <h1>{{ $title }}</h1>
            @endif
            @if(!empty($subtitle))
                <p class="subtitle">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="content">
            {!! $slot ?? '' !!}
        </div>

        <div class="email-foot">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</div>
</body>
</html>
