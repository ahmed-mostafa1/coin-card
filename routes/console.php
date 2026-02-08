<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Services\VipService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('vip:recalculate {--user_id=}', function (VipService $vipService) {
    $userId = $this->option('user_id');

    if ($userId) {
        $user = User::find($userId);
        if (! $user) {
            $this->error('User not found.');
            return 1;
        }

        $vipService->updateUserVipStatus($user);
        $this->info('VIP recalculated for user #'.$userId);
        return 0;
    }

    $count = 0;
    User::query()->chunkById(200, function ($users) use ($vipService, &$count) {
        foreach ($users as $user) {
            $vipService->updateUserVipStatus($user);
            $count++;
        }
    });

    $this->info('VIP recalculated for '.$count.' users.');
    return 0;
})->purpose('Recalculate VIP status for one user or all users');

use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncMarketCard99BillStatus;

// Schedule MarketCard99 bill status sync every 5 minutes
Schedule::job(new SyncMarketCard99BillStatus)->everyFiveMinutes();
