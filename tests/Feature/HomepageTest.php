<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomepageTest extends TestCase
{
    /**
     * Test the homepage loads successfully and contains all core sections.
     */
    public function test_homepage_returns_success_and_contains_content(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Berkendara Lebih Aman Dimulai dari Keputusan yang Tepat');
        $response->assertSee('Saya Pengemudi Individu');
        $response->assertSee('Saya Mewakili Perusahaan');
        $response->assertSee('Prinsip Utama Defensive Driving');
        $response->assertSee('PROGRAM UNTUK PENGEMUDI INDIVIDU');
        $response->assertSee('PROGRAM PERUSAHAAN &amp; FLEET OPERATOR', false);
        $response->assertSee('Alur Konsultasi &amp; Pelaksanaan Pelatihan', false);
        $response->assertSee('Jelajahi Informasi Training Defensive Driving Berdasarkan Wilayah');
        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Mulai Diskusikan Kebutuhan Pelatihan Berkendara Anda.');
        $response->assertSee(route('city.landing', ['citySlug' => ':slug']), false);
        $response->assertDontSee('Pelatihan P3K');
        $response->assertDontSee('/pelatihan-p3k/');
    }

    /**
     * Test homepage regional pagination works for page 2.
     */
    public function test_homepage_pagination_works(): void
    {
        $response = $this->get('/?page=2');

        $response->assertStatus(200);
        $response->assertSee('page=1');
    }

    /**
     * Test homepage embeds searchable city dataset for fast instant search.
     */
    public function test_homepage_regional_search_payload_present(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('const ALL_CITIES =', false);
        $response->assertSee('city-search-input');
    }
}
