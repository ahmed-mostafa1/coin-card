<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Tests\TestCase;

class SeoRenderingTest extends TestCase
{
    public function test_login_page_is_noindex(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false)
            ->assertSee('<link rel="canonical" href="'.url('/login').'">', false);
    }

    public function test_home_view_renders_indexable_meta_and_structured_data(): void
    {
        $html = view('home', [
            'categories' => new Collection(),
        ])->render();

        $this->assertStringContainsString('<meta name="robots" content="index,follow">', $html);
        $this->assertStringContainsString('<link rel="canonical" href="'.route('home').'">', $html);
        $this->assertStringContainsString('application/ld+json', $html);
    }
}
