<x-emails.app
  :subject="__('Verify Your Email Address')"
  :arTitle="'تأكيد البريد الإلكتروني'"
  :enTitle="'Verify Your Email Address'"
  :arIntroLines="[
      'يرجى الضغط على الزر التالي لتأكيد بريدك الإلكتروني.',
      'ستنتهي صلاحية رابط التأكيد خلال 60 دقيقة.',
  ]"
  :enIntroLines="[
      'Please click the button below to verify your email address.',
      'This verification link will expire in 60 minutes.',
  ]"
  :arActionText="'تأكيد البريد الإلكتروني'"
  :enActionText="'Verify Email Address'"
  :actionUrl="$verificationUrl"
  :arHelperText="'إذا لم يعمل الزر، انسخ الرابط التالي والصقه في المتصفح:'"
  :enHelperText="'If the button does not work, copy and paste this URL into your browser:'"
  :fallbackUrl="$verificationUrl"
/>
