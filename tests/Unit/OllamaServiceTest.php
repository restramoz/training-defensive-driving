<?php

namespace Tests\Unit;

use App\Services\OllamaService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OllamaServiceTest extends TestCase
{
    /**
     * Test OllamaService returns clear error when config is missing without sending HTTP request.
     */
    public function test_missing_config_returns_clean_error(): void
    {
        config(['services.ollama.base_url' => '']);
        config(['services.ollama.model' => '']);

        $service = new OllamaService;
        $result = $service->generateArticle(['city_name' => 'Surabaya']);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('OLLAMA_BASE_URL', $result['error']);
    }

    /**
     * Test pickTopicForRegion selects a core Training Defensive Driving service topic.
     */
    public function test_pick_topic_for_region_chooses_defensive_driving_core_topic(): void
    {
        $service = new OllamaService;
        $selected = $service->pickTopicForRegion('Kota Bandung');

        $this->assertNotEmpty($selected['topic']);
        $this->assertNotEmpty($selected['focus']);
        $this->assertStringContainsString('Kota Bandung', $selected['topic']);
        $this->assertTrue(
            str_contains($selected['topic'], 'Defensive Driving') || str_contains($selected['topic'], 'Berkendara')
        );
    }

    /**
     * Test resolveWordCount correctly returns the integer word count.
     */
    public function test_resolve_word_count(): void
    {
        $service = new OllamaService;

        $this->assertEquals(500, $service->resolveWordCount(500));
        $this->assertEquals(1000, $service->resolveWordCount(1000));
        $this->assertEquals(1500, $service->resolveWordCount(1500));
        $this->assertEquals(300, $service->resolveWordCount(300));
        $this->assertEquals(3000, $service->resolveWordCount(3000));
    }

    /**
     * Test parsing clean JSON response and isolating image_brief.
     */
    public function test_parse_response_with_valid_json(): void
    {
        $service = new OllamaService;
        $json = json_encode([
            'title' => 'Panduan Defensive Driving di Semarang',
            'slug' => 'panduan-defensive-driving-semarang',
            'excerpt' => 'Teknik antisipasi bahaya dan manajemen jarak aman berkendara.',
            'content' => '<h2>Prinsip Defensive Driving</h2><p>Penerapan aturan 3 detik di jalan tol.</p>',
            'seo_title' => 'Panduan Defensive Driving Semarang | Training Defensive Driving',
            'meta_description' => 'Pelatihan Defensive Driving in-house di Semarang.',
            'suggested_faqs' => [
                ['q' => 'Berapa jarak aman berkendara?', 'a' => 'Gunakan aturan minimal jarak 3 detik dalam kondisi cuaca normal.'],
            ],
            'image_brief' => [
                'concept' => 'Foto dokumenter sesi briefing keselamatan berkendara di Semarang.',
                'english_prompt' => 'Realistic photo of driver safety briefing in Semarang transport facility, natural lighting, no text, 8k',
                'aspect_ratio' => '16:9',
                'recommended_alt' => 'Training Defensive Driving di Semarang',
            ],
        ]);

        $result = $service->parseResponse($json, 'Fallback Topic', 'Semarang', 1000);

        $this->assertTrue($result['success']);
        $this->assertEquals('Panduan Defensive Driving di Semarang', $result['title']);
        $this->assertEquals('panduan-defensive-driving-semarang', $result['slug']);
        $this->assertArrayHasKey('image_brief', $result);
        $this->assertEquals('16:9', $result['image_brief']['aspect_ratio']);
        $this->assertStringContainsString('Semarang', $result['image_brief']['english_prompt']);
        $this->assertCount(1, $result['suggested_faqs']);
    }

    /**
     * Test parsing markdown code fenced JSON.
     */
    public function test_parse_response_with_fenced_markdown(): void
    {
        $service = new OllamaService;
        $fenced = "```json\n".json_encode([
            'title' => 'Manajemen Kelelahan Pengemudi di Medan',
            'content' => '<p>Pentingnya istirahat berkala untuk pengemudi jarak jauh.</p>',
            'image_brief' => [
                'concept' => 'Pengemudi profesional fokus di jalan tol',
                'english_prompt' => 'Professional fleet driver focused on the road, realistic, 8k, no text',
            ],
        ])."\n```";

        $result = $service->parseResponse($fenced, 'Fallback Topic', 'Medan', 1000);

        $this->assertTrue($result['success']);
        $this->assertEquals('Manajemen Kelelahan Pengemudi di Medan', $result['title']);
        $this->assertEquals('16:9', $result['image_brief']['aspect_ratio']);
    }

    /**
     * Test word counting ignores HTML tags and decodes entities properly.
     */
    public function test_count_words_ignores_html_tags_and_decodes_entities(): void
    {
        $service = new OllamaService;
        $html = '<h2>Defensive Driving &amp; Keselamatan</h2><p>Pengemudi profesional harus menguasai jarak aman 3 detik.</p>';

        $this->assertEquals(11, $service->countWords($html));
    }

    /**
     * Test a sufficiently long initial article does not trigger continuation.
     */
    public function test_long_article_does_not_trigger_continuation(): void
    {
        config([
            'services.ollama.base_url' => 'https://ollama.local',
            'services.ollama.model' => 'test-model',
            'services.whatsapp.callout_contacts' => ['08118500177'],
        ]);

        $words = implode(' ', array_fill(0, 6200, 'defensive'));
        $json = json_encode([
            'title' => 'Artikel Cukup',
            'slug' => 'artikel-cukup',
            'content' => "<p>{$words}</p>",
        ]);

        Http::fake([
            '*' => Http::response(['message' => ['content' => $json]], 200),
        ]);

        $service = new OllamaService;
        $result = $service->generateArticle([
            'city_name' => 'Surabaya',
            'word_count' => 6000,
            'topic' => 'Defensive Driving Fleet',
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('validated_word_count', $result);
        $this->assertEquals(6200, $result['validated_word_count']);
        Http::assertSentCount(1);
    }

    /**
     * Test short initial article triggers continuation and brings article to requested count.
     */
    public function test_short_article_triggers_continuation_and_brings_to_target(): void
    {
        config([
            'services.ollama.base_url' => 'https://ollama.local',
            'services.ollama.model' => 'test-model',
            'services.whatsapp.callout_contacts' => ['08118500177'],
        ]);

        $firstPart = implode(' ', array_fill(0, 4000, 'keselamatan'));
        $secondPart = implode(' ', array_fill(0, 2500, 'pengemudi'));

        $json = json_encode([
            'title' => 'Artikel Lanjutan',
            'slug' => 'artikel-lanjutan',
            'content' => "<p>{$firstPart}</p>",
        ]);

        Http::fake([
            '*' => Http::sequence()
                ->push(['message' => ['content' => $json]], 200)
                ->push(['message' => ['content' => "<p>{$secondPart}</p>"]], 200),
        ]);

        $service = new OllamaService;
        $result = $service->generateArticle([
            'city_name' => 'Bandung',
            'word_count' => 6000,
            'topic' => 'Antisipasi Bahaya Lalu Lintas',
        ]);

        $this->assertTrue($result['success']);
        Http::assertSentCount(2);
        $this->assertGreaterThanOrEqual(6000, $result['validated_word_count']);
        $this->assertStringContainsString('keselamatan', $result['content']);
        $this->assertStringContainsString('pengemudi', $result['content']);
    }

    /**
     * Test repeated continuation content is rejected safely.
     */
    public function test_repeated_continuation_content_is_rejected_safely(): void
    {
        config([
            'services.ollama.base_url' => 'https://ollama.local',
            'services.ollama.model' => 'test-model',
        ]);

        $existing = '<p>Pelatihan keselamatan berkendara sangat penting untuk pengemudi armada.</p>';

        Http::fake([
            '*' => Http::response(['message' => ['content' => '<p>Pelatihan keselamatan berkendara sangat penting untuk pengemudi armada.</p>']], 200),
        ]);

        $service = new OllamaService;
        $res = $service->requestContinuation($existing, 1000, 'Surabaya', 'Defensive Driving');

        $this->assertFalse($res['success']);
        $this->assertEquals('Continuation repeated existing content', $res['error']);
    }

    /**
     * Test empty continuation content is rejected safely.
     */
    public function test_empty_continuation_content_is_rejected_safely(): void
    {
        config([
            'services.ollama.base_url' => 'https://ollama.local',
            'services.ollama.model' => 'test-model',
        ]);

        Http::fake([
            '*' => Http::response(['message' => ['content' => '   ']], 200),
        ]);

        $service = new OllamaService;
        $res = $service->requestContinuation('<p>Ada konten awal</p>', 1000, 'Surabaya', 'Defensive Driving');

        $this->assertFalse($res['success']);
        $this->assertEquals('Empty continuation response', $res['error']);
    }

    /**
     * Test article below requested count after maximum retry limit fails explicitly.
     */
    public function test_article_below_requested_count_after_max_attempts_fails_explicitly(): void
    {
        config([
            'services.ollama.base_url' => 'https://ollama.local',
            'services.ollama.model' => 'test-model',
        ]);

        $initial = implode(' ', array_fill(0, 1000, 'awal'));
        $json = json_encode([
            'title' => 'Artikel Pendek',
            'slug' => 'artikel-pendek',
            'content' => "<p>{$initial}</p>",
        ]);

        $sequence = Http::sequence();
        $sequence->push(['message' => ['content' => $json]], 200);
        for ($i = 0; $i < 8; $i++) {
            $words = implode(' ', array_map(fn ($k) => "tambah{$i}_{$k}", range(1, 10)));
            $sequence->push(['message' => ['content' => "<p>{$words}</p>"]], 200);
        }

        Http::fake(['*' => $sequence]);

        $service = new OllamaService;
        $result = $service->generateArticle([
            'city_name' => 'Semarang',
            'word_count' => 6000,
            'topic' => 'Manajemen Kelelahan',
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('shorter than the requested word count', $result['error']);
        $this->assertStringContainsString('requested: 6000', $result['error']);
    }

    /**
     * Test malformed or empty article content is not accepted as successful.
     */
    public function test_malformed_or_empty_article_content_rejected(): void
    {
        $service = new OllamaService;

        // Empty string
        $res1 = $service->parseResponse('', 'Topik', 'Jakarta', 6000);
        $this->assertFalse($res1['success']);

        // JSON with empty content
        $emptyJson = json_encode([
            'title' => 'Judul Ada',
            'content' => '   ',
        ]);
        $res2 = $service->parseResponse($emptyJson, 'Topik', 'Jakarta', 6000);
        $this->assertFalse($res2['success']);

        // Whitespace only
        $res3 = $service->parseResponse("   \n\t  ", 'Topik', 'Jakarta', 6000);
        $this->assertFalse($res3['success']);
    }

    /**
     * Test CTA insertion does not change the article's validated word count.
     */
    public function test_cta_insertion_does_not_change_validated_word_count(): void
    {
        config(['services.whatsapp.callout_contacts' => ['08118500177', '081399810272']]);

        $service = new OllamaService;
        $articleHtml = '<p>Paragraf pertama edukasi keselamatan armada operasional.</p><p>Paragraf kedua pemeliharaan rem berkala.</p>';
        $countBefore = $service->countWords($articleHtml);

        $withCta = $service->addWhatsAppCallout(['content' => $articleHtml], 'Medan');
        $countAfter = $service->countWords($withCta['content']);

        $this->assertEquals($countBefore, $countAfter);
        $this->assertEquals(5, substr_count($withCta['content'], 'article-wa-card'));
    }

    /**
     * Test article exceeding maximum allowed words fails explicitly.
     */
    public function test_article_exceeding_maximum_words_fails(): void
    {
        config([
            'services.ollama.base_url' => 'https://ollama.local',
            'services.ollama.model' => 'test-model',
        ]);

        $hugeText = implode(' ', array_fill(0, 9200, 'defensive'));
        $json = json_encode([
            'title' => 'Artikel Sangat Panjang',
            'slug' => 'artikel-sangat-panjang',
            'content' => "<p>{$hugeText}</p>",
        ]);

        Http::fake([
            '*' => Http::response(['message' => ['content' => $json]], 200),
        ]);

        $service = new OllamaService;
        $result = $service->generateArticle([
            'city_name' => 'Surabaya',
            'word_count' => 6000,
            'topic' => 'Defensive Driving Fleet',
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('exceeds the maximum allowed', $result['error']);
    }
}
