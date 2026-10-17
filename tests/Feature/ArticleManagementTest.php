<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\RegionalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@example.com',
            'password' => 'Password123!',
            'is_admin' => true,
        ]);
    }

    /**
     * Test admin can store new article as draft by default with sanitized HTML.
     */
    public function test_admin_can_store_article_sanitized(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.articles.store'), [
            'title' => 'Panduan P3K Pabrik Kimia',
            'slug' => 'panduan-p3k-pabrik-kimia',
            'excerpt' => 'Langkah penanganan luka bakar asam/basa.',
            'content' => '<h2>Langkah Dekontaminasi</h2><p>Irigasi air mengalir 15 menit.</p><script>alert("hack")</script>',
            'status' => 'draft',
            'target_city_slug' => 'kab-gresik',
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $article = Article::where('slug', 'panduan-p3k-pabrik-kimia')->first();
        $this->assertNotNull($article);
        $this->assertEquals('draft', $article->status);
        $this->assertEquals('kab-gresik', $article->target_city_slug);
        $this->assertStringContainsString('<h2>Langkah Dekontaminasi</h2>', $article->content);
        $this->assertStringNotContainsString('<script', $article->content);
    }

    /**
     * Test image upload endpoint rejects SVG files.
     */
    public function test_image_upload_rejects_svg(): void
    {
        $this->actingAs($this->admin);

        $svgFile = UploadedFile::fake()->create('malicious.svg', 10, 'image/svg+xml');

        $response = $this->postJson(route('admin.upload.image'), [
            'image' => $svgFile,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('SVG tidak diizinkan', $response->json('message'));
    }

    /**
     * Test image upload endpoint accepts valid JPG/PNG and stores in public/uploads/articles.
     */
    public function test_image_upload_accepts_valid_image_and_returns_relative_path(): void
    {
        $this->actingAs($this->admin);

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $tempPath = tempnam(sys_get_temp_dir(), 'test_upload_').'.png';
        file_put_contents($tempPath, $pngBytes);

        $imageFile = new UploadedFile($tempPath, 'training.png', 'image/png', null, true);

        $response = $this->postJson(route('admin.upload.image'), [
            'image' => $imageFile,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $url = $response->json('url');
        $path = $response->json('path');

        $this->assertStringStartsWith('/uploads/articles/', $url);
        $this->assertStringStartsWith('uploads/articles/', $path);

        // Verify the file actually exists in public/uploads/articles
        $fullPath = public_path($path);
        $this->assertFileExists($fullPath);

        // Clean up uploaded test file
        @unlink($fullPath);
    }

    /**
     * Test admin can update article.
     */
    public function test_admin_can_update_article(): void
    {
        $this->actingAs($this->admin);

        $article = Article::create([
            'title' => 'Judul Awal',
            'slug' => 'judul-awal',
            'content' => '<p>Konten awal</p>',
            'status' => 'draft',
        ]);

        $response = $this->put(route('admin.articles.update', $article->id), [
            'title' => 'Judul Diperbarui',
            'slug' => 'judul-diperbarui',
            'content' => '<p>Konten baru diperbarui</p>',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $fresh = $article->fresh();
        $this->assertEquals('Judul Diperbarui', $fresh->title);
        $this->assertEquals('published', $fresh->status);
        $this->assertNotNull($fresh->published_at);
    }

    /**
     * Test admin can delete article.
     */
    public function test_admin_can_delete_article(): void
    {
        $this->actingAs($this->admin);

        $article = Article::create([
            'title' => 'Artikel Mau Dihapus',
            'slug' => 'artikel-mau-dihapus',
            'content' => '<p>Konten</p>',
            'status' => 'draft',
        ]);

        $response = $this->delete(route('admin.articles.destroy', $article->id));
        $response->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /**
     * Test creating a draft with AI-generated JSON image_brief and faqs persists after redirect and refresh.
     */
    public function test_admin_can_save_article_with_generated_json_image_brief_and_faqs_as_draft(): void
    {
        $this->actingAs($this->admin);

        $imageBrief = [
            'concept' => 'Petugas P3K menangani korban di area pabrik',
            'recommended_alt' => 'Pelatihan P3K di Gresik',
            'english_prompt' => 'First aid responders assisting worker in manufacturing plant',
            'aspect_ratio' => '16:9',
        ];

        $faqs = [
            ['question' => 'Berapa lama pelatihan P3K?', 'answer' => 'Pelatihan berlangsung selama 3 hari kerja.'],
        ];

        $response = $this->post(route('admin.articles.store'), [
            'title' => 'Sertifikasi P3K Lingkungan Industri Gresik',
            'slug' => 'sertifikasi-p3k-lingkungan-industri-gresik',
            'excerpt' => 'Panduan lengkap sertifikasi petugas P3K.',
            'content' => '<p>Konten lengkap panduan sertifikasi P3K di wilayah industri.</p>',
            'status' => 'draft',
            'target_city_slug' => 'kab-gresik',
            'image_brief' => json_encode($imageBrief),
            'faqs' => json_encode($faqs),
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $response->assertSessionHas('success');

        // Verify persistence after refresh on the index page
        $indexResponse = $this->get(route('admin.articles.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Sertifikasi P3K Lingkungan Industri Gresik');

        $article = Article::where('slug', 'sertifikasi-p3k-lingkungan-industri-gresik')->first();
        $this->assertNotNull($article);
        $this->assertEquals('draft', $article->status);
        $this->assertNull($article->published_at);
        $this->assertEquals('kab-gresik', $article->target_city_slug);
        $this->assertIsArray($article->image_brief);
        $this->assertEquals('Petugas P3K menangani korban di area pabrik', $article->image_brief['concept']);
        $this->assertIsArray($article->faqs);
        $this->assertEquals('Berapa lama pelatihan P3K?', $article->faqs[0]['question']);
    }

    /**
     * Test publishing a new article sets published_at and public URL returns 200.
     */
    public function test_admin_can_publish_new_article_and_public_url_works(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.articles.store'), [
            'title' => 'Panduan P3K Kantor Surabaya',
            'slug' => 'panduan-p3k-kantor-surabaya',
            'excerpt' => 'Prosedur keselamatan kantor modern.',
            'content' => '<p>Artikel lengkap panduan P3K untuk perkantoran bertingkat di Surabaya.</p>',
            'status' => 'published',
            'target_city_slug' => 'kota-surabaya',
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $article = Article::where('slug', 'panduan-p3k-kantor-surabaya')->first();
        $this->assertNotNull($article);
        $this->assertEquals('published', $article->status);
        $this->assertNotNull($article->published_at);

        // Verify public URL works
        $publicResponse = $this->get(route('articles.show', $article->slug));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Panduan P3K Kantor Surabaya');
        $publicResponse->assertSee('Artikel lengkap panduan P3K');
    }

    /**
     * Test editing a saved article updates the exact same record without creating a duplicate.
     */
    public function test_editing_saved_article_updates_same_record_without_duplicate(): void
    {
        $this->actingAs($this->admin);

        $article = Article::create([
            'title' => 'Artikel Asli Surabaya',
            'slug' => 'artikel-asli-surabaya',
            'content' => '<p>Konten asli</p>',
            'status' => 'draft',
            'target_city_slug' => 'kota-surabaya',
            'image_brief' => ['concept' => 'Visual asli'],
            'faqs' => [['question' => 'Q asli', 'answer' => 'A asli']],
        ]);

        $initialCount = Article::count();
        $this->assertEquals(1, $initialCount);

        $response = $this->put(route('admin.articles.update', $article->id), [
            'title' => 'Artikel Asli Surabaya Diperbarui',
            'slug' => 'artikel-asli-surabaya',
            'content' => '<p>Konten telah diperbarui dengan data baru</p>',
            'status' => 'published',
            'target_city_slug' => 'kota-surabaya',
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertEquals(1, Article::count(), 'Article count must remain 1 (no duplicates)');

        $fresh = $article->fresh();
        $this->assertEquals($article->id, $fresh->id);
        $this->assertEquals('Artikel Asli Surabaya Diperbarui', $fresh->title);
        $this->assertEquals('published', $fresh->status);
        $this->assertNotNull($fresh->published_at);
        // Ensure image_brief and faqs are preserved when not re-sent
        $this->assertEquals('Visual asli', $fresh->image_brief['concept']);
        $this->assertEquals('Q asli', $fresh->faqs[0]['question']);
    }

    /**
     * Test validation failures redirect back and display actionable errors while preserving inputs.
     */
    public function test_validation_failure_redirects_back_with_errors_and_displays_them(): void
    {
        $this->actingAs($this->admin);

        // 1. Direct post redirects back with session errors
        $response = $this->from(route('admin.articles.create'))->post(route('admin.articles.store'), [
            'title' => '',
            'content' => '',
            'status' => 'draft',
            'excerpt' => 'Excerpt tersimpan',
        ]);

        $response->assertRedirect(route('admin.articles.create'));
        $response->assertSessionHasErrors(['title', 'content']);

        // 2. Following redirects renders the validation error banner in HTML
        $followedResponse = $this->from(route('admin.articles.create'))
            ->followingRedirects()
            ->post(route('admin.articles.store'), [
                'title' => '',
                'content' => '',
                'status' => 'draft',
                'excerpt' => 'Excerpt tersimpan',
            ]);

        $followedResponse->assertStatus(200);
        $followedResponse->assertSee('Terdapat beberapa kesalahan pengisian form');
    }

    /**
     * Test create and edit pages render the region filter, search inputs, and preserve preselected city.
     */
    public function test_regional_selector_and_filter_rendered_on_forms(): void
    {
        $this->actingAs($this->admin);

        // 1. Create page renders AI and target selectors with province filter and search
        $createResponse = $this->get(route('admin.articles.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('id="filter_ai_province"', false);
        $createResponse->assertSee('id="search_ai_city"', false);
        $createResponse->assertSee('id="filter_target_province"', false);
        $createResponse->assertSee('id="search_target_city"', false);
        $createResponse->assertSee('id="target_city_slug"', false);

        // 2. Edit page renders target selector and preselects existing city
        $article = Article::create([
            'title' => 'Artikel Surabaya Testing',
            'slug' => 'artikel-surabaya-testing',
            'content' => '<p>Konten</p>',
            'status' => 'draft',
            'target_city_slug' => 'surabaya',
        ]);

        $editResponse = $this->get(route('admin.articles.edit', $article->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('id="filter_target_province"', false);
        $editResponse->assertSee('id="search_target_city"', false);
        $editResponse->assertSee('value="surabaya"', false);
        $editResponse->assertSee('selected', false);
    }

    /**
     * Test combined region filtering and partial city search logic matches expected records.
     */
    public function test_combined_region_filtering_and_partial_city_search_logic(): void
    {
        $regionalService = app(RegionalService::class);
        $cities = $regionalService->getAllCities();

        $this->assertNotEmpty($cities);

        // Case 1: Search partial 'surab' in province 'Jawa Timur'
        $filtered = array_filter($cities, function ($city) {
            $matchProv = strcasecmp($city['province'], 'Jawa Timur') === 0;
            $matchSearch = stripos($city['name'], 'surab') !== false;

            return $matchProv && $matchSearch;
        });

        $this->assertCount(1, $filtered);
        $first = reset($filtered);
        $this->assertEquals('surabaya', $first['slug']);

        // Case 2: Case-insensitive search 'GRESIK' in 'Jawa Timur'
        $filteredGresik = array_filter($cities, function ($city) {
            $matchProv = strcasecmp($city['province'], 'Jawa Timur') === 0;
            $matchSearch = stripos($city['name'], 'GRESIK') !== false;

            return $matchProv && $matchSearch;
        });

        $this->assertCount(1, $filteredGresik);
        $gresik = reset($filteredGresik);
        $this->assertEquals('kab-gresik', $gresik['slug']);

        // Case 3: Filter mismatch (searching Bandung in Jawa Timur should yield 0)
        $mismatch = array_filter($cities, function ($city) {
            $matchProv = strcasecmp($city['province'], 'Jawa Timur') === 0;
            $matchSearch = stripos($city['name'], 'bandung') !== false;

            return $matchProv && $matchSearch;
        });
        $this->assertCount(0, $mismatch);
    }
}
