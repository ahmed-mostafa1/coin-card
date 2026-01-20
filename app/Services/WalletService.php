<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(Wallet $wallet, string $amount, array $meta = [], bool $useTransaction = true): WalletTransaction
    {
        $operation = function () use ($wallet, $amount, $meta) {
            $lockedWallet = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            DB::statement('update wallets set balance = balance + ? where id = ?', [$amount, $lockedWallet->id]);

            $transaction = $lockedWallet->transactions()->create([
                'type' => $meta['type'] ?? WalletTransaction::TYPE_DEPOSIT,
                'status' => $meta['status'] ?? WalletTransaction::STATUS_APPROVED,
                'amount' => $amount,
                'reference_type' => $meta['reference_type'] ?? null,
                'reference_id' => $meta['reference_id'] ?? null,
                'note' => $meta['note'] ?? null,
                'created_by_user_id' => $meta['created_by_user_id'] ?? null,
                'approved_by_user_id' => $meta['approved_by_user_id'] ?? null,
                'approved_at' => $meta['approved_at'] ?? null,
            ]);

            $lockedWallet->refresh();

            return $transaction;
        };

        return $useTransaction ? DB::transaction($operation) : $operation();
    }

    public function debit(Wallet $wallet, string $amount, array $meta = [], bool $useTransaction = true): WalletTransaction
    {
        $operation = function () use ($wallet, $amount, $meta) {
            $lockedWallet = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            DB::statement('update wallets set balance = balance - ? where id = ?', [$amount, $lockedWallet->id]);

            $transaction = $lockedWallet->transactions()->create([
                'type' => $meta['type'] ?? 'purchase',
                'status' => $meta['status'] ?? WalletTransaction::STATUS_APPROVED,
                'amount' => '-'.$amount,
                'reference_type' => $meta['reference_type'] ?? null,
                'reference_id' => $meta['reference_id'] ?? null,
                'note' => $meta['note'] ?? null,
                'created_by_user_id' => $meta['created_by_user_id'] ?? null,
                'approved_by_user_id' => $meta['approved_by_user_id'] ?? null,
                'approved_at' => $meta['approved_at'] ?? null,
            ]);

            $lockedWallet->refresh();

            return $transaction;
        };

        return $useTransaction ? DB::transaction($operation) : $operation();
    }
}
