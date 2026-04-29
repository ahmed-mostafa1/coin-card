<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Facades\URL as UrlGenerator;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--base-url=https://s7sh.com : Absolute base URL used for sitemap links.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $baseUrl = rtrim((string) $this->option('base-url'), '/') ?: 'https://s7sh.com';
        $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'https';

        UrlGenerator::forceRootUrl($baseUrl);
        UrlGenerator::forceScheme($scheme);

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

        Category::active()->chunkById(200, function ($categories) use ($sitemap) {
            $categories->each(function (Category $category) use ($sitemap) {
                $sitemap->add(Url::create(route('categories.show', $category->slug))
                    ->setLastModificationDate($category->updated_at ?? now())
                    ->setPriority(0.9)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            });
        });

        Service::where('is_active', true)->providerAvailable()->whereHas('category', function ($query) {
            $query->active();
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

        return self::SUCCESS;
    }
}
