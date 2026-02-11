<?php

return [
    'email_subjects' => [
        'new_order_admin' => 'إشعار طلب جديد - :app_name',
        'deposit_request_created_user' => 'تم استلام طلب الشحن - :app_name',
        'new_deposit_request_admin' => 'إشعار طلب شحن رصيد جديد - :app_name',
        'deposit_status_changed_user' => 'تحديث حالة طلب الشحن - :app_name',
        'order_created_user' => 'تأكيد استلام الطلب - :app_name',
        'order_status_changed_user' => 'تحديث حالة الطلب - :app_name',
    ],

    'balance_notification_subject_credit' => 'تحديث رصيد المحفظة',
    'balance_notification_subject_debit' => 'تحديث رصيد المحفظة',
    'balance_notification_greeting' => 'مرحباً :name،',
    'view_wallet' => 'عرض المحفظة',
    'view_link' => 'عرض',

    'notifications_custom' => [
        'new_order_title' => 'طلب جديد',
        'new_order_desc' => 'طلب جديد من :user لخدمة :service بمبلغ :amount.',
        'order_processing_title' => 'بدء معالجة الطلب',
        'order_done_title' => 'اكتمل الطلب',
        'order_rejected_title' => 'تم رفض الطلب',
        'order_created_title' => 'تم إنشاء الطلب',
        'order_created_desc' => 'تم استلام طلبك لخدمة :service بمبلغ :amount.',
        'order_desc' => 'طلب رقم #:order_id (:service) بمبلغ :amount.',
        'deposit_approved_title' => 'تمت الموافقة على الإيداع',
        'deposit_rejected_title' => 'تم رفض الإيداع',
        'deposit_approved_desc' => 'تمت الموافقة على طلب الإيداع رقم #:deposit_id بمبلغ :amount.',
        'deposit_rejected_desc' => 'تم رفض طلب الإيداع رقم #:deposit_id.',
        'deposit_rejected_reason' => ' السبب: :reason',
        'new_deposit_request_title' => 'طلب إيداع جديد',
        'new_deposit_request_desc' => 'طلب إيداع جديد من :user بمبلغ :amount.',
        'deposit_created_title' => 'تم إنشاء طلب الإيداع',
        'deposit_created_desc' => 'تم استلام طلب الإيداع الخاص بك بمبلغ :amount.',
        'balance_credit_title' => 'تمت إضافة رصيد',
        'balance_credit_desc' => 'أضاف المشرف :amount إلى محفظتك. الرصيد الحالي: :balance.',
        'balance_credit_desc_with_note' => 'أضاف المشرف :amount إلى محفظتك. ملاحظة: :note. الرصيد الحالي: :balance.',
        'balance_debit_title' => 'تم خصم رصيد',
        'balance_debit_desc' => 'خصم المشرف :amount من محفظتك. الرصيد الحالي: :balance.',
        'balance_debit_desc_with_note' => 'خصم المشرف :amount من محفظتك. ملاحظة: :note. الرصيد الحالي: :balance.',
    ],
];