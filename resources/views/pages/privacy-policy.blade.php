@extends('layouts.app')

@section('title', __('messages.privacy_policy'))

@section('content')
    <x-card :hover="false">
        <x-page-header :title="__('messages.privacy_policy')" :center="true" />
        <div class="mt-6 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-white text-center">
            @if(app()->getLocale() == 'ar')
                @if(!empty($sharedPrivacyAr))
                     {!! nl2br(e($sharedPrivacyAr)) !!}
                @else
                    سياسة الخصوصية – Arab 8bp
    
                    في ماركت كارد (Arab 8bp) نُقدّر خصوصيتك ونحترم ثقتك الغالية.
                    نلتزم التزامًا تامًا بحماية بياناتك الشخصية والحفاظ على سريّتها ضمن أعلى معايير الأمان والشفافية.
                    تهدف هذه السياسة إلى توضيح كيفية جمع بياناتك واستخدامها وحمايتها أثناء تفاعلك مع موقعنا وخدماتنا الإلكترونية عبر
                    الرابط الرسمي:
                    🔗 www.8bp.in
    
                    وباستخدامك لموقعنا، فإنك تؤكد موافقتك الكاملة على بنود هذه السياسة وشروطها.
    
                    📌 أولاً: المعلومات التي نجمعها
                    حرصًا على تقديم تجربة مميزة وآمنة، قد نقوم بجمع بعض البيانات الأساسية عند التسجيل أو تنفيذ الطلبات، وتشمل:
                    الاسم الكامل أو اسم المستخدم
                    البريد الإلكتروني
                    رقم الهاتف
                    بيانات تسجيل الدخول (مثل كلمة المرور)
                    سجل الطلبات والمعاملات السابقة
                    معرفات الحساب أو التفاصيل الخاصة بالخدمات أو الألعاب
    
                    نُجمع هذه البيانات فقط بالقدر اللازم لتقديم خدماتنا بأعلى كفاءة وجودة.
    
                    ⚙️ ثانيًا: كيفية استخدام المعلومات
    
                    نستخدم بياناتك الشخصية حصريًا للأغراض التالية:
    
                    تنفيذ الطلبات وتوفير الخدمات الرقمية المطلوبة بدقة وسرعة
                    التواصل معك بخصوص الحساب أو الطلبات أو الدعم الفني
                    ضمان تجربة استخدام آمنة وموثوقة
                    تطوير وتحسين خدماتنا ومنصتنا الإلكترونية
                    الحماية من أي نشاط احتيالي أو غير مصرح به
    
                    🚫 نؤكد أننا لا نشارك بياناتك الشخصية مع أي جهة خارجية غير موثوقة.
    
                    🛡️ ثالثًا: حماية وأمان المعلومات
                    يتبع موقع ماركت كارد بروتوكولات حماية متقدمة وتقنيات تشفير (SSL) لضمان سرّية وأمان البيانات.
                    جميع المعلومات تُخزَّن في بيئة آمنة محمية ضد الوصول غير المصرح به أو الاستخدام غير القانوني.
    
                    👤 رابعًا: الحساب والمسؤولية
                    يُعتبر المستخدم مسؤولًا عن الحفاظ على سرّية بيانات الدخول الخاصة بحسابه.
    
                    لا يتحمل موقع Arab 8bp أي مسؤولية عن أي أضرار أو خسائر ناتجة عن إهمال المستخدم، أو مشاركة بياناته مع الآخرين،
                    أو عدم تفعيل خاصية المصادقة الثنائية لحسابه لضمان أعلى درجات الأمان.
    
                    لا نتحمل مسؤولية الأمان في الحسابات أو المنصات الخارجية (مثل حسابات الألعاب أو التطبيقات)، ويُنصح باستخدامها
                    بحذر.
    
                    🚫 خامسًا: الشروط العامة
                    باستخدامك خدمات ماركت كارد، فإنك تقر وتوافق على الالتزام بشروط الخدمة وسياسات الاستخدام، بما في ذلك:
    
                    جميع عمليات الشحن الرقمية تعتبر نهائية وغير قابلة للإلغاء أو الاسترداد بعد تنفيذها بنجاح.
    
                    تحتفظ إدارة المنصة بحق تعليق أو إيقاف أي حساب، أو تجميد الرصيد كليًا أو جزئيًا، في حال الاشتباه بنشاط مخالف
                    للأنظمة، أو إساءة استخدام للخدمات، أو أي محاولة تحايل أو تلاعب بأنظمة الموقع، وذلك حفاظًا على أمان المنصة وحقوق
                    جميع المستخدمين.
    
                    أي خطأ في إدخال بيانات الحساب أو المعرف أثناء الطلب يُعد مسؤولية المستخدم بالكامل.
    
                    قد تتغير الأسعار أو الخدمات دون إشعار مسبق وفقًا لسياسات المزودين أو ظروف السوق.
    
                    🔄 سادسًا: تحديثات السياسة
                    قد نقوم بمراجعة أو تحديث هذه السياسة من حينٍ لآخر بما يتناسب مع التطورات القانونية أو التقنية.
                    يُعد استمرارك في استخدام خدماتنا بعد أي تعديل موافقةً ضمنية على النسخة الأحدث من السياسة.
                    نوصي بمراجعة هذه الصفحة بشكل دوري للبقاء على اطلاع دائم.
    
                    📬 سابعًا: تواصل معنا
                    لأي استفسار أو ملاحظة تتعلق بسياسة الخصوصية أو إدارة بياناتك، يُسعدنا تواصلك معنا عبر:
                    📧 البريد الإلكتروني: marketcard99@gmail.com
    
                    📞 رقم الإدارة وتساب : +963991195136
                    🌐 الموقع الرسمي :
    
                    https://8bp.in
    
                    ✅ ثامنًا :
                    إن ثقتك بنا هي أساس نجاحنا.
                    وباستخدامك ماركت كارد، فإنك تؤكد إدراكك التام والتزامك ببنود هذه السياسة،
                    ونتعهد بدورنا بتقديم تجربة رقمية راقية، آمنة، وسريعة تليق بعالم الفخامة الرقمية.
                @endif
            @else
                @if(!empty($sharedPrivacyEn))
                    {!! nl2br(e($sharedPrivacyEn)) !!}
                @else
                    Privacy Policy – Arab 8bp
    
                    At Arab 8bp, we value your privacy and respect your trust.
                    We are fully committed to protecting your personal data and maintaining its confidentiality within the highest
                    standards of security and transparency.
                    This policy aims to clarify how we collect, use, and protect your data while you interact with our website and
                    electronic services via the official link:
                    🔗 www.8bp.in
    
                    By using our website, you confirm your full agreement to the terms and conditions of this policy.
    
                    📌 First: Information We Collect
                    To provide a distinct and safe experience, we may collect some basic data when registering or executing orders,
                    including:
                    Full name or username
                    Email
                    Phone number
                    Login data (such as password)
                    Order history and previous transactions
                    Account identifiers or details specific to services or games
    
                    We collect this data only to the extent necessary to provide our services with the highest efficiency and
                    quality.
    
                    ⚙️ Second: How We Use Information
    
                    We use your personal data exclusively for the following purposes:
    
                    Executing orders and providing required digital services accurately and quickly
                    Communicating with you regarding the account, orders, or technical support
                    Ensuring a safe and reliable user experience
                    Developing and improving our services and electronic platform
                    Protection against any fraudulent or unauthorized activity
    
                    🚫 We confirm that we do not share your personal data with any unreliable third party.
    
                    🛡️ Third: Protection and Security of Information
                    Arab 8bp follows advanced security protocols and encryption technologies (SSL) to ensure the confidentiality
                    and security of data.
                    All information is stored in a secure environment protected against unauthorized access or illegal use.
    
                    👤 Fourth: Account and Responsibility
                    The user is responsible for maintaining the confidentiality of their account login data.
    
                    Arab 8bp bears no responsibility for any damages or losses resulting from user negligence, sharing data with
                    others, or not activating two-factor authentication for their account to ensure the highest levels of security.
    
                    We do not bear responsibility for security in external accounts or platforms (such as game accounts or apps),
                    and it is advised to use them with caution.
    
                    🚫 Fifth: General Terms
                    By using Arab 8bp services, you acknowledge and agree to abide by the terms of service and usage policies,
                    including:
    
                    All digital top-up operations are considered final and non-cancellable or refundable after successful execution.
    
                    The platform management reserves the right to suspend or stop any account, or freeze the balance wholly or
                    partially, in case of suspected activity violating regulations, misuse of services, or any attempt to defraud or
                    manipulate the site systems, to preserve the security of the platform and the rights of all users.
    
                    Any error in entering account data or ID during the order is the user's full responsibility.
    
                    Prices or services may change without prior notice according to provider policies or market conditions.
    
                    🔄 Sixth: Policy Updates
                    We may review or update this policy from time to time to suit legal or technical developments.
                    Your continued use of our services after any modification is an implicit agreement to the latest version of the
                    policy.
                    We recommend reviewing this page periodically to stay informed.
    
                    📬 Seventh: Contact Us
                    For any inquiry or note related to the privacy policy or managing your data, we are happy to communicate with
                    you via:
                    📧 Email: marketcard99@gmail.com
    
                    📞 Admin WhatsApp: +963991195136
                    🌐 Official Website:
    
                    https://8bp.in
    
                    ✅ Eighth:
                    Your trust in us is the foundation of our success.
                    By using Arab 8bp, you confirm your full awareness and commitment to the terms of this policy,
                    and we pledge in turn to provide a sophisticated, safe, and fast digital experience befitting the world of
                    digital luxury.
                @endif
            @endif
        </div>
    </x-card>
@endsection