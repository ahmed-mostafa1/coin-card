<?php

return [
    'email_subjects' => [
        'new_order_admin' => 'New Order Notification - :app_name',
        'deposit_request_created_user' => 'Deposit Request Received - :app_name',
        'new_deposit_request_admin' => 'New Deposit Request Notification - :app_name',
        'deposit_status_changed_user' => 'Deposit Status Updated - :app_name',
        'order_created_user' => 'Order Confirmation - :app_name',
        'order_status_changed_user' => 'Order Status Updated - :app_name',
    ],

    'balance_notification_subject_credit' => 'Your wallet balance has been updated',
    'balance_notification_subject_debit' => 'Your wallet balance has been updated',
    'balance_notification_greeting' => 'Hello :name,',
    'view_wallet' => 'View Wallet',
    'view_link' => 'View',

    'notifications_custom' => [
        'new_order_title' => 'New Order',
        'new_order_desc' => 'New order from :user for service :service for :amount.',
        'order_processing_title' => 'Order Processing Started',
        'order_done_title' => 'Order Completed',
        'order_rejected_title' => 'Order Rejected',
        'order_created_title' => 'Order Created',
        'order_created_desc' => 'Your order for service :service for :amount has been received.',
        'order_desc' => 'Order #:order_id (:service) for :amount.',
        'deposit_approved_title' => 'Deposit Approved',
        'deposit_rejected_title' => 'Deposit Rejected',
        'deposit_approved_desc' => 'Deposit request #:deposit_id for :amount has been approved.',
        'deposit_rejected_desc' => 'Deposit request #:deposit_id has been rejected.',
        'deposit_rejected_reason' => ' Reason: :reason',
        'new_deposit_request_title' => 'New Deposit Request',
        'new_deposit_request_desc' => 'New deposit request from :user for :amount.',
        'deposit_created_title' => 'Deposit Request Created',
        'deposit_created_desc' => 'Your deposit request for :amount has been received.',
        'balance_credit_title' => 'Balance Credited',
        'balance_credit_desc' => 'Admin credited :amount to your wallet. Current balance: :balance.',
        'balance_credit_desc_with_note' => 'Admin credited :amount to your wallet. Note: :note. Current balance: :balance.',
        'balance_debit_title' => 'Balance Debited',
        'balance_debit_desc' => 'Admin debited :amount from your wallet. Current balance: :balance.',
        'balance_debit_desc_with_note' => 'Admin debited :amount from your wallet. Note: :note. Current balance: :balance.',
    ],
];