<x-emails.app
  :subject="__('Your Login Code')"
  :title="__('Your Login Code')"
  :introLines="[__('Use the code below to complete your login. This code is valid for 10 minutes.')]"
  :outroLines="[__('If you did not request this code, you can safely ignore this email.')]"
>
  <div style="margin: 24px 0; text-align: center;">
    <p style="display: inline-block; padding: 12px 24px; background-color: #F3F4F6; border-radius: 8px; font-size: 28px; font-weight: 700; letter-spacing: 4px; color: #111827;">
      {{ $otp }}
    </p>
  </div>
</x-emails.app>
