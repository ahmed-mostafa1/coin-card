@component('emails.layout', ['title' => 'Arab 8bp.in - كود التفعيل'])

<p class="intro-text">
    مرحباً بك في Arab 8bp.in! شكراً لتسجيلك معنا.
</p>

<p class="intro-text">
    يرجى استخدام كود التفعيل التالي لتفعيل حسابك:
</p>

<div style="text-align: center; margin: 30px 0;">
    <div style="display: inline-block; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px 40px; border-radius: 8px; border: 2px dashed #2d5a8c;">
        <div style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #2d5a8c; font-family: 'Courier New', monospace;">
            {{ $code }}
        </div>
    </div>
</div>

<div class="action-text">
    <strong>ملاحظة:</strong> هذا الكود صالح لمدة 10 دقائق فقط. إذا لم تقم بإنشاء حساب، يرجى تجاهل هذه الرسالة.
</div>

@endcomponent
