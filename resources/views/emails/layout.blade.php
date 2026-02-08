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
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            direction: rtl;
            text-align: right;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8c 100%);
            color: #ffffff;
            padding: 24px 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 35px 30px;
            background-color: #ffffff;
        }
        .intro-text {
            font-size: 16px;
            color: #333333;
            line-height: 1.8;
            margin-bottom: 25px;
        }
        .details-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 25px 0;
            background-color: #fafafa;
            border-radius: 6px;
            overflow: hidden;
        }
        .details-table tr {
            border-bottom: 1px solid #e8e8e8;
        }
        .details-table tr:last-child {
            border-bottom: none;
        }
        .details-table td {
            padding: 14px 18px;
            font-size: 15px;
        }
        .details-table .label {
            color: #666666;
            font-weight: 600;
            text-align: right;
            width: 40%;
        }
        .details-table .value {
            color: #1a1a1a;
            font-weight: 700;
            text-align: left;
            direction: ltr;
        }
        .details-table .value.rtl {
            text-align: right;
            direction: rtl;
        }
        .highlight-value {
            color: #2d5a8c;
            font-size: 18px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-processing {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-done {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-text {
            font-size: 15px;
            color: #555555;
            line-height: 1.7;
            margin-top: 20px;
            padding: 18px;
            background-color: #f8f9fa;
            border-right: 4px solid #2d5a8c;
            border-radius: 4px;
        }
        .footer {
            background-color: #f8f9fa;
            color: #6c757d;
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            border-top: 1px solid #e9ecef;
        }
        .footer-text {
            margin: 0;
            line-height: 1.6;
        }
        a {
            color: #2d5a8c;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header h1 {
                font-size: 18px;
            }
            .content {
                padding: 25px 20px;
            }
            .details-table td {
                padding: 12px 14px;
                font-size: 14px;
            }
            .details-table .label,
            .details-table .value {
                display: block;
                width: 100%;
                text-align: right;
            }
            .details-table .value {
                margin-top: 4px;
                padding-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>{{ $title ?? 'Coin7Card - كوين7كارد' }}</h1>
        </div>

        <!-- Content -->
        <div class="content">
            {!! $slot !!}
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                © Coin7Card {{ date('Y') }} - {{ $footerText ?? 'جميع الحقوق محفوظة' }}
            </p>
        </div>
    </div>
</body>
</html>
