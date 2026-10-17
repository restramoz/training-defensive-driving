<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizerService;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    protected HtmlSanitizerService $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new HtmlSanitizerService;
    }

    /**
     * Test safe HTML tags and structure remain intact.
     */
    public function test_safe_tags_remain_intact(): void
    {
        $input = '<h2>Panduan P3K</h2><p>Pelatihan pertolongan pertama sangat <strong>penting</strong>.</p><ul><li>CPR</li><li>Balut Bidai</li></ul>';
        $output = $this->sanitizer->sanitize($input);

        $this->assertStringContainsString('<h2>Panduan P3K</h2>', $output);
        $this->assertStringContainsString('<strong>penting</strong>', $output);
        $this->assertStringContainsString('<li>CPR</li>', $output);
    }

    /**
     * Test malicious scripts, iframes, styles, and SVG are stripped.
     */
    public function test_malicious_tags_are_stripped(): void
    {
        $input = '<p>Aman</p><script>alert("XSS")</script><iframe src="https://evil.com"></iframe><svg><circle r="10"/></svg><style>body { display:none; }</style>';
        $output = $this->sanitizer->sanitize($input);

        $this->assertStringContainsString('<p>Aman</p>', $output);
        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('alert("XSS")', $output);
        $this->assertStringNotContainsString('<iframe', $output);
        $this->assertStringNotContainsString('<svg', $output);
        $this->assertStringNotContainsString('<style', $output);
    }

    /**
     * Test event handlers (onclick, onerror, onload) are removed.
     */
    public function test_event_handlers_are_removed(): void
    {
        $input = '<p onclick="stealCookies()" onmouseover="evil()">Teks</p><img src="/uploads/articles/test.jpg" onerror="alert(1)">';
        $output = $this->sanitizer->sanitize($input);

        $this->assertStringNotContainsString('onclick', $output);
        $this->assertStringNotContainsString('onmouseover', $output);
        $this->assertStringNotContainsString('onerror', $output);
        $this->assertStringContainsString('Teks', $output);
    }

    /**
     * Test dangerous URL schemes are stripped from href and src.
     */
    public function test_dangerous_schemes_are_rejected(): void
    {
        $input = '<a href="javascript:alert(1)">Link Bahaya</a><a href="https://example.com">Link Aman</a><img src="data:image/svg+xml;base64,PHN2Zz4=" alt="test">';
        $output = $this->sanitizer->sanitize($input);

        $this->assertStringNotContainsString('javascript:alert(1)', $output);
        $this->assertStringNotContainsString('data:image/svg+xml', $output);
        $this->assertStringContainsString('href="https://example.com"', $output);
    }

    /**
     * Test rel="noopener noreferrer" is enforced on target="_blank".
     */
    public function test_target_blank_enforces_noopener_noreferrer(): void
    {
        $input = '<a href="https://kemnaker.go.id" target="_blank">Kemnaker RI</a>';
        $output = $this->sanitizer->sanitize($input);

        $this->assertStringContainsString('target="_blank"', $output);
        $this->assertStringContainsString('rel="noopener noreferrer"', $output);
    }

    /**
     * Test relative URLs for internal uploads are preserved.
     */
    public function test_relative_upload_urls_are_preserved(): void
    {
        $input = '<img src="/uploads/articles/sample-cpr.webp" alt="Simulasi CPR" width="600" height="400">';
        $output = $this->sanitizer->sanitize($input);

        $this->assertStringContainsString('src="/uploads/articles/sample-cpr.webp"', $output);
        $this->assertStringContainsString('alt="Simulasi CPR"', $output);
    }
}
