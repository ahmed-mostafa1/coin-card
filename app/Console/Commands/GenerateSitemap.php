<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Category;
use App\Models\Service;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create();

        $sitemap->add(Url::create(route('home'))
            ->setLastModificationDate(now())
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        $sitemap->add(Url::create(route('about'))
            ->setLastModificationDate(now())
            ->setPriority(0.3)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        $sitemap->add(Url::create(route('privacy-policy'))
            ->setLastModificationDate(now())
            ->setPriority(0.2)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        $sitemap->add(Url::create(route('terms-of-use'))
            ->setLastModificationDate(now())
            ->setPriority(0.2)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        $sitemap->add(Url::create(route('contact-us.show'))
            ->setLastModificationDate(now())
            ->setPriority(0.3)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        // Add Categories
        Category::active()->manual()->chunkById(200, function ($categories) use ($sitemap) {
            $categories->each(function (Category $category) use ($sitemap) {
            $sitemap->add(Url::create(route('categories.show', $category->slug))
                ->setLastModificationDate($category->updated_at ?? now())
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            });
        });

        // Add Services
        Service::where('is_active', true)->providerAvailable()->whereHas('category', function ($query) {
            $query->active()->manual();
        })->chunkById(200, function ($services) use ($sitemap) {
            $services->each(function (Service $service) use ($sitemap) {
            $sitemap->add(Url::create(route('services.show', $service->slug))
                ->setLastModificationDate($service->updated_at ?? now())
                ->setPriority(0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            });
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');
    }
}
