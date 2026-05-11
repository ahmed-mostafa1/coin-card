<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Services\SecurityLogger;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Repair negative held balances in wallets
            $wallets = DB::table('wallets')->where('held_balance', '<', 0)->get();
            foreach ($wallets as $wallet) {
                DB::table('wallets')->where('id', $wallet->id)->update([
                    'held_balance' => abs($wallet->held_balance)
                ]);
                
                \Illuminate\Support\Facades\Log::info('Repaired negative held balance', [
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'old_value' => $wallet->held_balance,
                    'new_value' => abs($wallet->held_balance)
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably reverse this repair.
    }
};