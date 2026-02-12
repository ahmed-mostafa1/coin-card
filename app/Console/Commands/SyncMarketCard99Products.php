<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\MarketCard99Client;
use Illuminate\Console\Command;

class SyncMarketCard99Products extends Command
{
    protected $signature = 'marketcard99:sync-products';
    protected $description = 'Fetch and display MarketCard99 products for manual mapping';

    public function handle(MarketCard99Client $client): int
    {
        $this->info('Fetching products from MarketCard99...');

        $productsResponse = $client->getProducts();
        $products = data_get($productsResponse, 'data.data.products', []);

        if (!($productsResponse['ok'] ?? false) || empty($products)) {
            $this->error('No products found or API request failed.');
            if (!empty($productsResponse['error_message'])) {
                $this->warn($productsResponse['error_message']);
            }
            return self::FAILURE;
        }

        $this->info('Found ' . count($products) . ' products:');
        $this->newLine();

        foreach ($products as $product) {
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line('ID: ' . ($product['id'] ?? 'N/A'));
            $this->line('Name: ' . ($product['name'] ?? 'N/A'));
            $this->line('Type: ' . ($product['type'] ?? 'N/A'));
            $this->line('Price: ' . ($product['price'] ?? $product['unit_price'] ?? 'N/A'));
            $this->line('Available: ' . (($product['is_available'] ?? false) ? 'Yes' : 'No'));
            
            if (isset($product['info'])) {
                $this->line('Info: ' . $product['info']);
            }
            
            $this->newLine();
        }

        $this->newLine();
        $this->info('Product sync completed!');
        $this->newLine();
        $this->comment('To map a product to a service:');
        $this->comment('1. Go to Admin > Services > Edit Service');
        $this->comment('2. Set the External Product ID field');
        $this->comment('3. The system will auto-detect required fields based on product type');
        $this->newLine();

        // Optionally show services that are already mapped
        $mappedServices = Service::whereNotNull('external_product_id')->get();
        
        if ($mappedServices->isNotEmpty()) {
            $this->info('Currently mapped services:');
            $this->newLine();
            
            foreach ($mappedServices as $service) {
                $this->line("• {$service->name} → Product ID: {$service->external_product_id} (Type: {$service->external_type})");
            }
        }

        return self::SUCCESS;
    }
}
