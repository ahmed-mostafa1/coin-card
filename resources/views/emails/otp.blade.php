@component('emails.layout', [
    'title' => 'Arab 8bp.in - كود التفعيل',
    'subtitle' => 'تأكيد البريد الإلكتروني',
    'preheader' => 'كود التفعيل الخاص بك'
])

<p class="intro-text">
    مرحباً بك في Arab 8bp.in! شكراً لتسجيلك معنا.
</p>

<p class="intro-text">
    يرجى استخدام كود التفعيل التالي لتفعيل حسابك:
</p>

<div style="text-align: center; margin: 30px 0;">
    <div style="display: inline-block; background: #f8fafc; padding: 20px 40px; border-radius: 12px; border: 2px dashed #0f766e;">
        <div style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #0f766e; font-family: 'Courier New', monospace;">
            {{ $code }}
        </div>
    </div>
</div>

<table class="details-table">
    <tr>
        <td class="label">تاريخ الإرسال:</td>
        <td class="value">{{ now()->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">صلاحية الكود:</td>
        <td class="value rtl">10 دقائق فقط</td>
    </tr>
</table>

<div class="action-text">
    <strong>ملاحظة:</strong> هذا الكود صالح لمدة 10 دقائق فقط. إذا لم تقم بإنشاء حساب، يرجى تجاهل هذه الرسالة.
</div>

@endcomponent
