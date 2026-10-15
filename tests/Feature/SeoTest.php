<?php

namespace Tests\Feature;

use App\Enums\PortfolioStatus;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menjaga infrastruktur SEO halaman publik: meta canonical/OG/Twitter,
 * structured data JSON-LD, sitemap.xml, dan robots.txt. Ini yang dibaca
 * Google Search Console saat pendaftaran, jadi sekali rusak efeknya besar.
 */
class SeoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return list<string>
     */
    private function publicRoutes(): array
    {
        return ['home', 'services', 'about', 'contact', 'portfolio', 'portfolio.index', 'buy-ebook'];
    }

    public function test_public_pages_render_canonical_open_graph_and_twitter_metadata(): void
    {
        foreach ($this->publicRoutes() as $name) {
            $response = $this->get(route($name));

            $response->assertOk();
            $response->assertSee('<link rel="canonical"', escape: false);
            $response->assertSee('<meta property="og:title"', escape: false);
            $response->assertSee('<meta property="og:description"', escape: false);
            $response->assertSee('<meta property="og:url"', escape: false);
            $response->assertSee('<meta property="og:image"', escape: false);
            $response->assertSee('<meta property="og:site_name"', escape: false);
            $response->assertSee('<meta name="twitter:card" content="summary_large_image">', escape: false);
            $response->assertSee('<meta name="description"', escape: false);
            $response->assertSee('<meta name="robots" content="index, follow">', escape: false);
            $response->assertSee('application/ld+json', escape: false);
        }
    }

    public function test_public_pages_canonical_points_at_their_own_url(): void
    {
        foreach ($this->publicRoutes() as $name) {
            $this->get(route($name))
                ->assertSee('<link rel="canonical" href="'.route($name).'">', escape: false);
        }
    }

    public function test_pages_render_organization_and_website_structured_data(): void
    {
        $content = $this->get(route('home'))->getContent();

        $this->assertStringContainsString('"@type":"Organization"', $content);
        $this->assertStringContainsString('"@type":"WebSite"', $content);
        $this->assertStringContainsString('"@type":"BreadcrumbList"', $content);
    }

    public function test_portfolio_detail_exposes_creative_work_schema_and_its_canonical(): void
    {
        $portfolio = Portfolio::factory()->published()->create();

        $content = $this->get(route('portfolio.show', $portfolio->id))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('portfolio.show', $portfolio->id).'">', escape: false)
            ->getContent();

        $this->assertStringContainsString('"@type":"CreativeWork"', $content);
        $this->assertStringContainsString($portfolio->name, $content);
    }

    public function test_draft_portfolio_detail_is_noindex_for_its_author(): void
    {
        $author = User::factory()->create();
        $draft = Portfolio::factory()->create([
            'created_by' => $author->id,
            'status' => PortfolioStatus::DRAFT,
        ]);

        $this->actingAs($author)
            ->get(route('portfolio.show', $draft->id))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', escape: false);
    }

    public function test_sitemap_lists_static_pages_and_published_portfolios_only(): void
    {
        $published = Portfolio::factory()->published()->create();
        $draft = Portfolio::factory()->create(['status' => PortfolioStatus::DRAFT]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', escape: false);

        foreach (['home', 'services', 'about', 'contact', 'portfolio', 'portfolio.index', 'buy-ebook'] as $name) {
            $response->assertSee(route($name), escape: false);
        }

        $response->assertSee(route('portfolio.show', $published->id), escape: false);
        $response->assertDontSee(route('portfolio.show', $draft->id), escape: false);
    }

    public function test_robots_txt_points_to_the_sitemap_and_never_leaks_the_admin_path(): void
    {
        $content = $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->getContent();

        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Sitemap: '.route('sitemap'), $content);

        $prefix = trim((string) config('admin.path'), '/');

        if ($prefix !== '') {
            $this->assertStringNotContainsString($prefix, $content);
        }
    }

    public function test_google_site_verification_meta_renders_only_when_configured(): void
    {
        $this->get(route('home'))
            ->assertDontSee('google-site-verification', escape: false);

        config(['seo.google_site_verification' => 'gsc-token-123']);

        $this->get(route('home'))
            ->assertSee('<meta name="google-site-verification" content="gsc-token-123">', escape: false);
    }

    public function test_meta_description_is_collapsed_and_truncated(): void
    {
        $portfolio = Portfolio::factory()->published()->create([
            'description' => "Baris pertama.\n\n   Baris kedua   dengan spasi berlebih.",
        ]);

        $content = $this->get(route('portfolio.show', $portfolio->id))->getContent();

        $this->assertStringContainsString(
            '<meta name="description" content="Baris pertama. Baris kedua dengan spasi berlebih.">',
            $content,
        );
    }

    public function test_excerpt_drops_a_word_that_would_be_cut_in_half(): void
    {
        $excerpt = Seo::excerpt('Intro '.str_repeat('supercalifragilistic', 20));

        $this->assertSame('Intro…', $excerpt);
    }

    public function test_every_json_ld_block_is_valid_json_on_public_pages(): void
    {
        $portfolio = Portfolio::factory()->published()->create();

        $urls = array_map(fn (string $name) => route($name), $this->publicRoutes());
        $urls[] = route('portfolio.show', $portfolio->id);

        foreach ($urls as $url) {
            $content = $this->get($url)->assertOk()->getContent();

            preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);

            $this->assertNotEmpty($matches[1], "Tidak ada blok JSON-LD di {$url}.");

            foreach ($matches[1] as $json) {
                json_decode($json, true, flags: JSON_THROW_ON_ERROR);
            }
        }
    }
}
