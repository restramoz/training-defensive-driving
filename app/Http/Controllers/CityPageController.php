<?php

namespace App\Http\Controllers;

use App\Services\RegionalService;
use Illuminate\View\View;

class CityPageController extends Controller
{
    public function show(string $citySlug, RegionalService $regionalService): View
    {
        $city = $regionalService->findCity($citySlug);

        if (! $city) {
            abort(404, 'Wilayah kota/kabupaten tidak ditemukan');
        }

        $otherCitiesInIsland = $regionalService->getIslandCities($city['island'], $city['code'], 12);
        $otherCitiesInProvince = $regionalService->getProvinceCities($city['province'], $city['code'], 8);

        $faqItems = [
            [
                'q' => "Apakah program Training Defensive Driving dapat diselenggarakan in-house untuk perusahaan di {$city['name']}?",
                'a' => "Ya. Program in-house training dapat didiskusikan untuk diselenggarakan langsung di lokasi perusahaan atau fasilitas operasional Anda di {$city['name']} dan sekitarnya, disesuaikan dengan kebutuhan armada dan jadwal operasional.",
            ],
            [
                'q' => "Berapa jumlah peserta ideal untuk sesi pelatihan Defensive Driving di {$city['name']}?",
                'a' => 'Untuk memastikan pemahaman teori, analisis studi kasus, dan interaksi yang efektif, rasio kelas yang disarankan umumnya berkisar antara 15 hingga 25 peserta per sesi pelatihan.',
            ],
            [
                'q' => "Apakah materi pelatihan dapat disesuaikan dengan profil kendaraan dan rute di {$city['name']}?",
                'a' => "Tentu. Materi pelatihan dapat diselaraskan dengan jenis kendaraan yang digunakan (kendaraan operasional ringan, angkutan niaga, atau kendaraan lapangan) serta karakteristik medan jalan di wilayah {$city['name']}.",
            ],
            [
                'q' => "Bagaimana cara berkonsultasi mengenai jadwal dan pelaksanaan di {$city['name']}?",
                'a' => 'Silakan gunakan tombol konsultasi pada halaman ini untuk mendiskusikan perkiraan jumlah pengemudi, target waktu, dan kebutuhan spesifik organisasi Anda bersama tim kami.',
            ],
        ];

        $mapsEmbedUrl = $regionalService->getGoogleMapsEmbedUrl($city['name']);

        return view('city-page', compact(
            'city',
            'otherCitiesInIsland',
            'otherCitiesInProvince',
            'faqItems',
            'mapsEmbedUrl'
        ));
    }
}
