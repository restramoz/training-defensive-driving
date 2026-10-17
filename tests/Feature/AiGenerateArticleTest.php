<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\OllamaService;
use App\Services\RegionalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiGenerateArticleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'Password123!',
            'is_admin' => true,
        ]);
    }

    protected function bindFakeRegional(array $city): void
    {
        $mock = $this->createMock(RegionalService::class);
        $mock->method('findCity')->willReturn($city);
        $mock->method('findDistrict')->willReturn(null);
        $this->app->instance(RegionalService::class, $mock);
    }

    protected function bindFakeOllama(array $response): void
    {
        $mock = $this->createMock(OllamaService::class);
        $mock->method('generateArticle')->willReturn($response);
        $this->app->instance(OllamaService::class, $mock);
    }

    /**
     * Test endpoint rejects word_count below minimum 6000.
     */
    public function test_rejects_word_count_below_minimum_6000(): void
    {
        $this->bindFakeRegional(['name' => 'Kota Surabaya', 'slug' => 'surabaya']);

        $response = $this->actingAs($this->admin)->postJson(route('admin.articles.ai-generate'), [
            'city_slug' => 'surabaya',
            'word_count' => 3000,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['word_count']);
    }

    /**
     * Test endpoint rejects word_count above maximum 9000.
     */
    public function test_rejects_word_count_above_maximum_9000(): void
    {
        $this->bindFakeRegional(['name' => 'Kota Surabaya', 'slug' => 'surabaya']);

        $response = $this->actingAs($this->admin)->postJson(route('admin.articles.ai-generate'), [
            'city_slug' => 'surabaya',
            'word_count' => 9500,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['word_count']);
    }

    /**
     * Test endpoint accepts word_count between 6000 and 9000.
     */
    public function test_accepts_valid_word_count_and_returns_generated_article(): void
    {
        $this->bindFakeRegional(['name' => 'Kota Surabaya', 'slug' => 'surabaya']);
        $this->bindFakeOllama([
            'success' => true,
            'title' => 'Panduan Defensive Driving Surabaya',
            'slug' => 'panduan-defensive-driving-surabaya',
            'excerpt' => 'Ringkasan panduan.',
            'content' => '<p>Konten artikel lengkap.</p>',
            'seo_title' => 'SEO Title',
            'meta_description' => 'Meta description.',
            'suggested_faqs' => [],
            'image_brief' => [],
            'validated_word_count' => 6200,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.articles.ai-generate'), [
            'city_slug' => 'surabaya',
            'word_count' => 6000,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'title' => 'Panduan Defensive Driving Surabaya',
            'validated_word_count' => 6200,
        ]);
    }

    /**
     * Test unauthenticated guest cannot call AI generation endpoint.
     */
    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson(route('admin.articles.ai-generate'), [
            'city_slug' => 'surabaya',
            'word_count' => 6000,
        ]);

        $response->assertStatus(401);
    }
}
