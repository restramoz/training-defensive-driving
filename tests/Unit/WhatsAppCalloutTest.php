<?php

namespace Tests\Unit;

use App\Services\OllamaService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WhatsAppCalloutTest extends TestCase
{
    protected function getService(): OllamaService
    {
        return new OllamaService();
    }

    /**
     * Test inserts exactly five callouts using configured contacts.
     */
    public function test_inserts_five_callouts_with_configured_contacts(): void
    {
        Config::set('services.whatsapp.callout_contacts', [
            '08118500177',
            '081399810272',
            '087894580806',
            '087894580649',
        ]);

        $service = $this->getService();
        $result = [
            'content' => '<p>Paragraf pertama.</p><p>Paragraf kedua.</p><p>Paragraf ketiga.</p><p>Paragraf keempat.</p><p>Paragraf kelima.</p><p>Paragraf keenam.</p>',
        ];

        $updated = $service->addWhatsAppCallout($result, 'Surabaya');
        $content = $updated['content'];

        // Assert exactly five callout cards are inserted
        $this->assertEquals(5, substr_count($content, 'article-wa-card'));
        $this->assertEquals(5, substr_count($content, 'data-wa-callout="1"'));

        // Assert all four configured phone numbers are normalized and present in wa.me links
        $expectedNumbers = ['628118500177', '6281399810272', '6287894580806', '6287894580649'];
        foreach ($expectedNumbers as $number) {
            $this->assertStringContainsString("wa.me/{$number}", $content);
        }
    }

    /**
     * Test CTA insertion does not corrupt article tables or semantic HTML.
     */
    public function test_cta_insertion_preserves_table_structure(): void
    {
        Config::set('services.whatsapp.callout_contacts', [
            '08118500177',
            '081399810272',
            '087894580806',
            '087894580649',
        ]);

        $service = $this->getService();
        $tableHtml = '<table class="w-full"><thead><tr><th>Modul</th><th>Durasi</th></tr></thead><tbody><tr><td>Defensive Driving</td><td>8 Jam</td></tr></tbody></table>';
        $result = [
            'content' => "<p>Pengantar pelatihan.</p>{$tableHtml}<p>Paragraf lanjutan setelah tabel.</p><p>Paragraf penutup.</p>",
        ];

        $updated = $service->addWhatsAppCallout($result, 'Jakarta');
        $content = $updated['content'];

        $this->assertStringContainsString($tableHtml, $content);
        $this->assertEquals(5, substr_count($content, 'article-wa-card'));
    }

    /**
     * Test missing or empty contacts skip insertion and never fabricate phone numbers.
     */
    public function test_missing_contacts_does_not_inject_callouts(): void
    {
        Config::set('services.whatsapp.callout_contacts', []);
        Config::set('training.whatsapp.callout_contacts', []);

        $service = $this->getService();
        $initialContent = '<p>Paragraf satu.</p><p>Paragraf dua.</p>';
        $result = ['content' => $initialContent];

        $updated = $service->addWhatsAppCallout($result, 'Bandung');

        $this->assertEquals($initialContent, $updated['content']);
        $this->assertEquals(0, substr_count($updated['content'], 'article-wa-card'));
        $this->assertStringNotContainsString('wa.me', $updated['content']);
    }

    /**
     * Test callouts are not duplicated if addWhatsAppCallout is called repeatedly.
     */
    public function test_prevents_duplicate_callout_insertion(): void
    {
        Config::set('services.whatsapp.callout_contacts', [
            '08118500177',
            '081399810272',
            '087894580806',
            '087894580649',
        ]);

        $service = $this->getService();
        $result = ['content' => '<p>Paragraf satu.</p><p>Paragraf dua.</p>'];

        $firstPass = $service->addWhatsAppCallout($result, 'Semarang');
        $secondPass = $service->addWhatsAppCallout($firstPass, 'Semarang');

        $this->assertEquals(5, substr_count($secondPass['content'], 'article-wa-card'));
    }
}
