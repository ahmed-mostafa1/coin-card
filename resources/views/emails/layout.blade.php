<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
            color: #0f172a;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        img {
            border: 0;
            height: auto;
            max-width: 100%;
            display: block;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }
        p {
            margin: 0 0 14px;
            line-height: 1.8;
        }
        .preheader {
            display: none !important;
            visibility: hidden;
            opacity: 0;
            color: transparent;
            height: 0;
            width: 0;
            overflow: hidden;
            mso-hide: all;
        }
        .email-body {
            width: 100%;
            background-color: #f1f5f9;
        }
        .email-wrapper {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            padding: 28px 16px;
        }
        .email-card {
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        }
        .header {
            background-color: #0f766e;
            background-image: linear-gradient(135deg, #0f766e 0%, #115e59 45%, #0f4c5c 100%);
            color: #ffffff;
            padding: 30px 32px 24px;
            text-align: center;
        }
        .header-title {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .header-subtitle {
            font-size: 13px;
            margin-top: 6px;
            color: rgba(255, 255, 255, 0.85);
        }
        .content {
            padding: 32px 34px;
            background-color: #ffffff;
        }
        .intro-text {
            font-size: 16px;
            color: #0f172a;
            margin-bottom: 18px;
        }
        .body-text {
            font-size: 15px;
            color: #1e293b;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f766e;
            margin: 22px 0 12px;
        }
        .details-table {
            width: 100%;
            margin: 0 0 18px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .details-table td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .details-table .label {
            color: #64748b;
            font-weight: 600;
            text-align: right;
            width: 38%;
        }
        .details-table .value {
            color: #0f172a;
            font-weight: 700;
            text-align: left;
            direction: ltr;
        }
        .details-table .value.rtl {
            text-align: right;
            direction: rtl;
        }
        .details-table .value.muted {
            color: #64748b;
            font-weight: 600;
        }
        .highlight-value {
            color: #0f766e;
            font-size: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-processing {
            background-color: #e0f2fe;
            color: #075985;
        }
        .status-done {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .action-text {
            font-size: 14px;
            color: #0f172a;
            margin-top: 16px;
            padding: 16px 18px;
            background-color: #f0fdfa;
            border-right: 4px solid #14b8a6;
            border-radius: 12px;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 22px 0;
        }
        .footer {
            background-color: #f8fafc;
            color: #64748b;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            margin: 0;
            line-height: 1.7;
        }
        a {
            color: #0f766e;
            text-decoration: none;
            font-weight: 700;
        }
        a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .header {
                padding: 24px 18px 18px;
            }
            .header-title {
                font-size: 18px;
            }
            .content {
                padding: 24px 20px;
            }
            .details-table td {
                padding: 10px 12px;
                font-size: 13px;
            }
            .details-table .label,
            .details-table .value {
                display: block;
                width: 100%;
                text-align: right;
            }
            .details-table .value {
                margin-top: 4px;
            }
            .details-table .value.rtl {
                direction: rtl;
            }
            .email-wrapper {
                padding: 16px 10px;
            }
            .email-card {
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="preheader">{{ $preheader ?? $title ?? '' }}</div>
    <table class="email-body" role="presentation" width="100%">
        <tr>
            <td align="center">
                <table class="email-wrapper" role="presentation" width="100%">
                    <tr>
                        <td>
                            <table class="email-card" role="presentation" width="100%">
                                <tr>
                                    <td class="header">
                                        <div class="header-title">{{ $title ?? 'Arab 8bp.in - كوين7كارد' }}</div>
                                        <div class="header-subtitle">{{ $subtitle ?? 'رسالة نظام تلقائية' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content">
                                        {!! $slot !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="footer">
                                        <p class="footer-text">
                                            © Arab 8bp.in {{ date('Y') }} - {{ $footerText ?? 'جميع الحقوق محفوظة' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
