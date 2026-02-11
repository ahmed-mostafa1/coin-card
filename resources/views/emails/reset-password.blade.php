<x-emails.app
  :subject="__('Reset Your Password')"
  :arTitle="'إعادة تعيين كلمة المرور'"
  :enTitle="'Reset Your Password'"
  :arIntroLines="[
      'تلقّيت هذه الرسالة لأننا استلمنا طلبًا لإعادة تعيين كلمة المرور الخاصة بحسابك.',
      'ستنتهي صلاحية رابط إعادة التعيين خلال 60 دقيقة.',
      'إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة.',
  ]"
  :enIntroLines="[
      'You are receiving this email because we received a password reset request for your account.',
      'This password reset link will expire in 60 minutes.',
      'If you did not request a password reset, no further action is required.',
  ]"
  :arActionText="'إعادة تعيين كلمة المرور'"
  :enActionText="'Reset Password'"
  :actionUrl="$resetUrl"
  :arHelperText="'إذا لم يعمل الزر، انسخ الرابط التالي والصقه في المتصفح:'"
  :enHelperText="'If the button does not work, copy and paste this URL into your browser:'"
  :fallbackUrl="$resetUrl"
/>
