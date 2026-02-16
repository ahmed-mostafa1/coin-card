<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncMarketCard99Products extends Command
{
    protected $signature = 'marketcard99:sync-products';
    protected $description = 'Disabled: MarketCard99 integration has been removed';

    public function handle(): int
    {
        $this->error('This command is disabled because MarketCard99 integration has been removed.');
        return self::FAILURE;
    }
}
