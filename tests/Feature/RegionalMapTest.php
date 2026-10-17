<?php

namespace Tests\Feature;

use App\Services\RegionalService;
use Tests\TestCase;

class RegionalMapTest extends TestCase
{
    /**
     * Test RegionalService generates correct Google Maps Embed URL.
     */
    public function test_regional_service_generates_google_maps_embed_url(): void
    {
        $service = new RegionalService;
        $url = $service->getGoogleMapsEmbedUrl('Kota Surabaya', 13);

        $this->assertStringContainsString('https://maps.google.com/maps?', $url);
        $this->assertStringContainsString('output=embed', $url);
        $this->assertStringContainsString('z=13', $url);
        $this->assertStringContainsString(urlencode('Kota Surabaya, Indonesia'), $url);
    }

    /**
     * Test City landing page includes Google Maps Embed iframe.
     */
    public function test_city_page_renders_google_maps_embed(): void
    {
        $response = $this->get('/kota-malang');

        $response->assertStatus(200);
        $response->assertSee('PETA CAKUPAN WILAYAH');
        $response->assertSee('https://maps.google.com/maps?q='.urlencode('Kota Malang, Indonesia'));
        $response->assertSee('<iframe', false);
    }

    /**
     * Test District landing page includes Google Maps Embed iframe.
     */
    public function test_district_page_renders_google_maps_embed(): void
    {
        $response = $this->get('/kecamatan-klojen');

        $response->assertStatus(200);
        $response->assertSee('PETA WILAYAH KECAMATAN');
        $response->assertSee('https://maps.google.com/maps?q='.urlencode('Kecamatan Klojen, Kota Malang, Indonesia'));
        $response->assertSee('<iframe', false);
    }
}
