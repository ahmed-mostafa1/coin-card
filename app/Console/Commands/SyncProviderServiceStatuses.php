<?php

namespace App\Console\Commands;

use App\Models\ApiProvider;
use App\Services\ProviderServiceStatusSyncService;
use Illuminate\Console\Command;

class SyncProviderServiceStatuses extends Command
{
    protected $signature = 'services:sync-provider-statuses {--provider_id=}';

    protected $description = 'Sync imported service availability from provider catalogs.';

    public function handle(ProviderServiceStatusSyncService $syncService): int
    {
        $provider = null;

        if ($this->option('provider_id')) {
            $provider = ApiProvider::find((int) $this->option('provider_id'));
            if (! $provider) {
                $this->error('Provider not found.');
                return self::FAILURE;
            }
        }

        $result = $syncService->sync($provider);

        $this->info("Checked {$result['checked']} services, changed {$result['changed']}, failed {$result['failed']}.");

        return self::SUCCESS;
    }
}
