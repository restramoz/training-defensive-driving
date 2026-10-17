<?php

namespace Tests\Unit;

use App\Services\WhatsAppService;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    public function test_disabled_config_returns_false_and_null_url(): void
    {
        config([
            'training.whatsapp.enabled' => false,
            'training.whatsapp.number' => '081234567890',
        ]);

        $service = new WhatsAppService;

        $this->assertFalse($service->isEnabled());
        $this->assertNull($service->getUrl());
        $this->assertNull($service->getIndividualUrl());
        $this->assertNull($service->getCorporateUrl());
    }

    public function test_empty_number_returns_false_and_null_url(): void
    {
        config([
            'training.whatsapp.enabled' => true,
            'training.whatsapp.number' => '',
        ]);

        $service = new WhatsAppService;

        $this->assertFalse($service->isEnabled());
        $this->assertNull($service->getUrl());
    }

    public function test_normalizes_indonesian_local_numbers_with_leading_zero(): void
    {
        $service = new WhatsAppService;

        $this->assertEquals('6281234567890', $service->normalize('0812-3456-7890'));
        $this->assertEquals('6281234567890', $service->normalize('0812 3456 7890'));
        $this->assertEquals('6281234567890', $service->normalize('+62 812-3456-7890'));
    }

    public function test_generates_valid_wa_url_with_prefilled_message_when_enabled(): void
    {
        config([
            'training.whatsapp.enabled' => true,
            'training.whatsapp.number' => '081234567890',
            'training.whatsapp.default_message' => 'Halo, saya ingin konsultasi.',
        ]);

        $service = new WhatsAppService;

        $this->assertTrue($service->isEnabled());
        $url = $service->getUrl();

        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString(rawurlencode('Halo, saya ingin konsultasi.'), $url);
    }

    public function test_individual_and_corporate_urls_use_distinct_messages(): void
    {
        config([
            'training.whatsapp.enabled' => true,
            'training.whatsapp.number' => '081234567890',
            'training.whatsapp.individual_message' => 'Pesan individu',
            'training.whatsapp.corporate_message' => 'Pesan perusahaan',
        ]);

        $service = new WhatsAppService;

        $individualUrl = $service->getIndividualUrl();
        $corporateUrl = $service->getCorporateUrl();

        $this->assertStringContainsString(rawurlencode('Pesan individu'), $individualUrl);
        $this->assertStringContainsString(rawurlencode('Pesan perusahaan'), $corporateUrl);
    }
}
