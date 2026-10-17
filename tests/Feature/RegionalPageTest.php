<?php

namespace Tests\Feature;

use App\Services\RegionalService;
use Tests\TestCase;

class RegionalPageTest extends TestCase
{
    /**
     * Test valid city landing page returns 200 and renders dynamic data.
     */
    public function test_valid_city_page_returns_200_and_renders_regional_data(): void
    {
        $response = $this->get('/kota-malang');

        $response->assertStatus(200);
        $response->assertSee('Training Defensive Driving di Kota Malang');
        $response->assertSee('PROVINSI JAWA TIMUR');
        $response->assertSee('Kecamatan di Wilayah Kota Malang');
        $response->assertSee('Klojen');
        $response->assertSee('Lowokwaru');
        $response->assertSee('FAQ: Training Defensive Driving di Kota Malang');
        $response->assertSee('Konsultasi Training Defensive Driving di Kota Malang');
        $response->assertDontSee('Pelatihan P3K');
    }

    /**
     * Test Aceh province city (Banda Aceh) page works correctly.
     */
    public function test_aceh_city_page_works(): void
    {
        $response = $this->get('/kota-banda-aceh');

        $response->assertStatus(200);
        $response->assertSee('Training Defensive Driving di Kota Banda Aceh');
        $response->assertSee('PROVINSI ACEH');
        $response->assertSee('Kecamatan di Wilayah Kota Banda Aceh');
        $response->assertDontSee('Pelatihan P3K');
    }

    /**
     * Test Aceh Singkil regency page works with correct province hierarchy.
     */
    public function test_aceh_singkil_city_page_works_with_correct_province(): void
    {
        $response = $this->get('/kota-kab-aceh-singkil');

        $response->assertStatus(200);
        $response->assertSee('Training Defensive Driving di Kabupaten Aceh Singkil');
        $response->assertSee('PROVINSI ACEH');
        $response->assertSee('Kecamatan di Wilayah Kabupaten Aceh Singkil');
        $response->assertDontSee('SUMATERA UTARA');
        $response->assertDontSee('Pelatihan P3K');
    }

    /**
     * Test search for Aceh and Aceh Singkil via RegionalService.
     */
    public function test_regional_service_finds_aceh_and_aceh_singkil(): void
    {
        $service = new RegionalService;

        // Test Banda Aceh (city)
        $bandaAceh = $service->findCity('banda-aceh');
        $this->assertNotNull($bandaAceh);
        $this->assertEquals('Kota Banda Aceh', $bandaAceh['name']);
        $this->assertEquals('Aceh', $bandaAceh['province']);
        $this->assertEquals('1100000', $bandaAceh['province_code']);

        // Test Aceh Singkil (regency)
        $acehSingkil = $service->findCity('kab-aceh-singkil');
        $this->assertNotNull($acehSingkil);
        $this->assertEquals('Kabupaten Aceh Singkil', $acehSingkil['name']);
        $this->assertEquals('Aceh', $acehSingkil['province']);
        $this->assertEquals('1100000', $acehSingkil['province_code']);
    }

    /**
     * Test city page alias route /pelatihan/kota-{slug} works.
     */
    public function test_city_page_alias_route_works(): void
    {
        $response = $this->get('/pelatihan/kota-malang');

        $response->assertStatus(200);
        $response->assertSee('Training Defensive Driving di Kota Malang');
    }

    /**
     * Test invalid city returns 404.
     */
    public function test_invalid_city_returns_404(): void
    {
        $response = $this->get('/kota-kota-khayalan-xyz');
        $response->assertStatus(404);

        $responseAlias = $this->get('/pelatihan/kota-nonexistent-city-xyz');
        $responseAlias->assertStatus(404);
    }

    /**
     * Test valid district page returns 200 and renders parent hierarchy.
     */
    public function test_valid_district_page_returns_200_and_renders_hierarchy(): void
    {
        $response = $this->get('/kecamatan-klojen');

        $response->assertStatus(200);
        $response->assertSee('Training Defensive Driving di Kecamatan Klojen, Kota Malang');
        $response->assertSee('KECAMATAN KLOJEN');
        $response->assertSee('Kota Malang');
        $response->assertSee('Jawa Timur');
        $response->assertSee('Kecamatan Lainnya di Kota Malang');
        $response->assertSee('FAQ: Training Defensive Driving di Kecamatan Klojen');
        $response->assertDontSee('Pelatihan P3K');
    }

    /**
     * Test district page alias and nested routes work.
     */
    public function test_district_page_alias_and_nested_routes_work(): void
    {
        $responseAlias = $this->get('/pelatihan/kecamatan-klojen');
        $responseAlias->assertStatus(200);
        $responseAlias->assertSee('Training Defensive Driving di Kecamatan Klojen');

        $responseNested = $this->get('/kota-malang/kecamatan-klojen');
        $responseNested->assertStatus(200);
        $responseNested->assertSee('Training Defensive Driving di Kecamatan Klojen, Kota Malang');
    }

    /**
     * Test invalid district returns 404.
     */
    public function test_invalid_district_returns_404(): void
    {
        $response = $this->get('/kecamatan-kecamatan-tidak-ada-xyz');
        $response->assertStatus(404);

        $responseNested = $this->get('/kota-malang/kecamatan-tidak-ada-xyz');
        $responseNested->assertStatus(404);
    }

    /**
     * Test Aceh Singkil district lookup and URL resolution.
     */
    public function test_aceh_singkil_district_lookup_and_routes(): void
    {
        $service = new RegionalService;

        // Test district lookup without city scope (returns first match - Aceh Singkil)
        $singkil = $service->findDistrict('singkil');
        $this->assertNotNull($singkil);
        $this->assertEquals('Singkil', $singkil['name']);
        $this->assertEquals('Kabupaten Aceh Singkil', $singkil['city_name']);
        $this->assertEquals('Aceh', $singkil['province']);

        // Test district lookup WITH city scope (explicit Aceh Singkil)
        $singkilScoped = $service->findDistrict('singkil', 'kab-aceh-singkil');
        $this->assertNotNull($singkilScoped);
        $this->assertEquals('Singkil', $singkilScoped['name']);
        $this->assertEquals('Kabupaten Aceh Singkil', $singkilScoped['city_name']);
        $this->assertEquals('Aceh', $singkilScoped['province']);

        // Test district page route works
        $response = $this->get('/kecamatan-singkil');
        $response->assertStatus(200);
        $response->assertSee('Training Defensive Driving di Kecamatan Singkil, Kabupaten Aceh Singkil');
        $response->assertSee('ACEH');
        $response->assertDontSee('SUMATERA UTARA');
        $response->assertDontSee('Pelatihan P3K');

        // Test nested route works
        $responseNested = $this->get('/kota-kab-aceh-singkil/kecamatan-singkil');
        $responseNested->assertStatus(200);
        $responseNested->assertSee('Training Defensive Driving di Kecamatan Singkil, Kabupaten Aceh Singkil');
    }

    /**
     * Test duplicate district names resolve correctly with city scope.
     */
    public function test_duplicate_district_names_resolve_with_city_scope(): void
    {
        $service = new RegionalService;

        // "Singkil" exists in both Aceh Singkil (Aceh) and Manado (Sulawesi Utara)
        $singkilAceh = $service->findDistrict('singkil', 'kab-aceh-singkil');
        $singkilManado = $service->findDistrict('singkil', 'kota-manado');

        $this->assertNotNull($singkilAceh);
        $this->assertEquals('Aceh', $singkilAceh['province']);
        $this->assertEquals('Kabupaten Aceh Singkil', $singkilAceh['city_name']);

        $this->assertNotNull($singkilManado);
        $this->assertEquals('Sulawesi Utara', $singkilManado['province']);
        $this->assertEquals('Kota Manado', $singkilManado['city_name']);
    }
}
