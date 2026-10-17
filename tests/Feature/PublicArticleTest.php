<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test published articles are visible on public index and show pages.
     */
    public function test_published_articles_are_accessible(): void
    {
        $published = Article::create([
            'title' => 'Panduan Lengkap P3K Pabrik',
            'slug' => 'panduan-lengkap-p3k-pabrik',
            'excerpt' => 'Ringkasan standar keselamatan industri.',
            'content' => '<h2>Prinsip K3</h2><p>Penjelasan Bantuan Hidup Dasar.</p>',
            'status' => 'published',
            'published_at' => now(),
            'target_city_slug' => 'surabaya',
            'featured_image' => 'uploads/articles/cpr-sample.jpg',
            'image_brief' => [
                'concept' => 'Foto rahasia internal tim prompt',
                'english_prompt' => 'Secret prompt for Google Flow that should NEVER appear publicly',
                'aspect_ratio' => '16:9',
            ],
        ]);

        $indexResponse = $this->get(route('articles.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Panduan Lengkap P3K Pabrik');
        $indexResponse->assertSee('/uploads/articles/cpr-sample.jpg');

        $showResponse = $this->get(route('articles.show', $published->slug));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Panduan Lengkap P3K Pabrik');
        $showResponse->assertSee('Prinsip K3');
        $showResponse->assertSee('/uploads/articles/cpr-sample.jpg');

        // CRITICAL CHECK: image_brief and english_prompt MUST NOT be visible on public HTML!
        $showResponse->assertDontSee('Secret prompt for Google Flow');
        $showResponse->assertDontSee('Foto rahasia internal');
    }

    /**
     * Test draft articles return 404 on public route.
     */
    public function test_draft_articles_return_404_publicly(): void
    {
        $draft = Article::create([
            'title' => 'Artikel Masih Draf',
            'slug' => 'artikel-masih-draf',
            'content' => '<p>Konten belum terbit</p>',
            'status' => 'draft',
        ]);

        $indexResponse = $this->get(route('articles.index'));
        $indexResponse->assertDontSee('Artikel Masih Draf');

        $showResponse = $this->get(route('articles.show', $draft->slug));
        $showResponse->assertStatus(404);
    }
}
