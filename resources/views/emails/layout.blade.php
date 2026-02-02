<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            overflow: hidden;
            font-family: Arial, sans-serif; /* Fallback */
        }
        .header {
            background-color: #0f1d28; /* Dark Blue from brand-900 or similar */
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        .content {
            padding: 30px 20px;
            color: #333333;
            line-height: 1.6;
        }
        .footer {
            background-color: #e5e7eb;
            color: #6b7280;
            padding: 15px;
            text-align: center;
            font-size: 12px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table td {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .label {
            color: #666;
            font-weight: bold;
        }
        .value {
            text-align: left;
            color: #000;
            font-weight: bold;
        }
        a {
            color: #3b82f6;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            {{ $title ?? config('app.name') }}
        </div>

        <!-- Content -->
        <div class="content">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} - {{ __('messages.all_rights_reserved') ?? 'All rights reserved.' }}
        </div>
    </div>
</body>
</html>
