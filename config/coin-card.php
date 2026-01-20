<?php

return [
    'max_pending_deposits' => (int) env('MAX_PENDING_DEPOSITS', 3),
    'deposit_min_amount' => (int) env('DEPOSIT_MIN_AMOUNT', 1),
    'deposit_max_amount' => (int) env('DEPOSIT_MAX_AMOUNT', 100000),
];
