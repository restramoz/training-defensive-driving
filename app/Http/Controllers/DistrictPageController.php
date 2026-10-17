<?php

namespace App\Http\Controllers;

use App\Services\RegionalService;
use Illuminate\View\View;

class DistrictPageController extends Controller
{
    public function show(string $districtSlug, RegionalService $regionalService): View
    {
        $district = $regionalService->findDistrict($districtSlug);

        if (! $district) {
            abort(404, 'Kecamatan tidak ditemukan');
        }

        return $this->renderView($district);
    }

    public function showNested(string $citySlug, string $districtSlug, RegionalService $regionalService): View
    {
        $district = $regionalService->findDistrict($districtSlug, $citySlug);

        if (! $district) {
            abort(404, 'Kecamatan tidak ditemukan di wilayah yang ditentukan');
        }

        return $this->renderView($district);
    }

    private function renderView(array $district): View
    {
        $cityName = $district['city_name'];
        $distName = $district['name'];

        $faqItems = [
            [
                'q' => "Apakah pelatihan Defensive Driving dapat diselenggarakan untuk pengemudi perusahaan di Kecamatan {$distName}?",
                'a' => "Ya. Pelatihan in-house dapat dikoordinasikan langsung ke lokasi fasilitas, kantor, atau area operasional perusahaan Anda di wilayah Kecamatan {$distName}, {$cityName} sesuai kesepakatan teknis pelaksanaan.",
            ],
            [
                'q' => "Apa saja sarana yang dibutuhkan untuk sesi kelas pelatihan di Kecamatan {$distName}?",
                'a' => 'Untuk sesi pemaparan materi teori dan analisis studi kasus, perusahaan cukup menyediakan ruang pertemuan yang kondusif, proyektor, serta media presentasi pendukung.',
            ],
            [
                'q' => "Bagaimana cara mendiskusikan kebutuhan pelatihan untuk armada di Kecamatan {$distName}?",
                'a' => 'Silakan gunakan tautan konsultasi pada halaman ini untuk menyampaikan estimasi jumlah pengemudi dan karakteristik kendaraan yang digunakan di area Kecamatan {$distName}.',
            ],
        ];

        $mapsEmbedUrl = (new RegionalService)->getGoogleMapsEmbedUrl("Kecamatan {$distName}, {$cityName}");

        return view('district-page', compact('district', 'faqItems', 'mapsEmbedUrl'));
    }
}
