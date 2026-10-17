<?php

namespace App\Services;

use Illuminate\Support\Str;

class RegionalService
{
    private static ?array $data = null;

    private static ?array $citySlugMap = null;

    private static ?array $districtSlugMap = null;

    /**
     * Cache path for compiled regional dataset to avoid re-parsing JSON on every request.
     */
    private static function getCachePath(): string
    {
        return storage_path('framework/cache/wilayah_cache.php');
    }

    /**
     * Load and index dataset from master_wilayah_bps.json or compiled cache.
     */
    private static function initialize(): void
    {
        if (self::$data !== null) {
            return;
        }

        $cacheFile = self::getCachePath();
        $sourceFile = base_path('master_wilayah_bps.json');

        if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($sourceFile)) {
            $cached = require $cacheFile;
            if (is_array($cached) && isset($cached['cities'], $cached['citySlugMap'], $cached['districtSlugMap'])) {
                self::$data = $cached['cities'];
                self::$citySlugMap = $cached['citySlugMap'];
                self::$districtSlugMap = $cached['districtSlugMap'];

                return;
            }
        }

        self::buildIndex($sourceFile, $cacheFile);
    }

    /**
     * Build lookup structures from master_wilayah_bps.json.
     */
    private static function buildIndex(string $sourceFile, string $cacheFile): void
    {
        if (! file_exists($sourceFile)) {
            self::$data = [];
            self::$citySlugMap = [];
            self::$districtSlugMap = [];

            return;
        }

        $jsonContent = file_get_contents($sourceFile);
        $records = json_decode($jsonContent, true);

        if (! is_array($records)) {
            self::$data = [];
            self::$citySlugMap = [];
            self::$districtSlugMap = [];

            return;
        }

        $hubCodes = [
            '3171000', '3172000', '3173000', '3174000', '3175000', // Jakarta
            '3578000', // Surabaya
            '3273000', // Bandung
            '3374000', // Semarang
            '1271000', // Medan
            '7371000', // Makassar
            '6472000', // Balikpapan
            '1671000', // Palembang
            '2171000', // Batam
            '5171000', // Denpasar
            '3471000', // Yogyakarta
            '3573000', // Malang
            '3275000', // Bekasi
            '3276000', // Depok
            '3271000', // Bogor
            '3674000', // Tangerang Selatan
            '3671000', // Tangerang
            '3201000', // Kab. Bogor
            '3216000', // Kab. Bekasi
            '3215000', // Kab. Karawang
            '3515000', // Kab. Sidoarjo
            '3525000', // Kab. Gresik
        ];

        $cities = [];
        $citySlugMap = [];
        $districtSlugMap = [];

        foreach ($records as $r) {
            $cityCode = trim((string) $r['Kode Kab/Kota']);
            $type = trim((string) $r['Jenis Wilayah']);
            $rawName = trim((string) $r['Kabupaten/Kota']);
            $cityName = ($type === 'Kota') ? 'Kota '.$rawName : 'Kabupaten '.$rawName;
            $rawSlug = Str::slug($rawName);
            $slug = ($type === 'Kota') ? $rawSlug : 'kab-'.$rawSlug;
            $provCode = trim((string) $r['Kode Provinsi']);
            $provName = trim((string) $r['Provinsi']);

            if (! isset($cities[$cityCode])) {
                $cities[$cityCode] = [
                    'code' => $cityCode,
                    'name' => $cityName,
                    'raw_name' => $rawName,
                    'type' => $type,
                    'province' => $provName,
                    'province_code' => $provCode,
                    'island' => self::resolveIsland($provCode),
                    'slug' => $slug,
                    'is_hub' => in_array($cityCode, $hubCodes),
                    'districts' => [],
                ];

                // Map various URL representations to this city
                $citySlugMap[$slug] = $cityCode;
                if ($type === 'Kota') {
                    $citySlugMap['kota-'.$rawSlug] = $cityCode;
                    // If no collision, map the raw slug
                    if (! isset($citySlugMap[$rawSlug])) {
                        $citySlugMap[$rawSlug] = $cityCode;
                    }
                } else {
                    $citySlugMap['kabupaten-'.$rawSlug] = $cityCode;
                    if (! isset($citySlugMap[$rawSlug])) {
                        $citySlugMap[$rawSlug] = $cityCode;
                    }
                }
            }

            $distName = trim((string) $r['Kecamatan']);
            $distCode = trim((string) $r['Kode Kecamatan']);
            $distSlug = Str::slug($distName);

            $districtItem = [
                'name' => $distName,
                'code' => $distCode,
                'slug' => $distSlug,
                'city_code' => $cityCode,
            ];

            $cities[$cityCode]['districts'][] = $districtItem;

            if (! isset($districtSlugMap[$distSlug])) {
                $districtSlugMap[$distSlug] = [];
            }
            $districtSlugMap[$distSlug][] = $districtItem;

            // City-scoped district key
            $cityDistKey = $slug.'/'.$distSlug;
            $districtSlugMap[$cityDistKey] = [$districtItem];
        }

        self::$data = $cities;
        self::$citySlugMap = $citySlugMap;
        self::$districtSlugMap = $districtSlugMap;

        // Write compiled cache
        $cacheDir = dirname($cacheFile);
        if (! is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $exportData = [
            'cities' => $cities,
            'citySlugMap' => $citySlugMap,
            'districtSlugMap' => $districtSlugMap,
        ];

        @file_put_contents($cacheFile, '<?php return '.var_export($exportData, true).';');
    }

    /**
     * Map province code to island group.
     */
    private static function resolveIsland(string $provCode): string
    {
        $prefix = substr($provCode, 0, 2);

        return match ($prefix) {
            '11', '12', '13', '14', '15', '16', '17', '18', '19', '21' => 'Sumatera',
            '31', '32', '33', '34', '35', '36' => 'Jawa',
            '51', '52', '53' => 'Bali & Nusa Tenggara',
            '61', '62', '63', '64', '65' => 'Kalimantan',
            '71', '72', '73', '74', '75', '76' => 'Sulawesi',
            '81', '82' => 'Maluku',
            '91', '92', '94', '95', '96', '97' => 'Papua',
            default => 'Indonesia',
        };
    }

    /**
     * Return all 514 cities.
     */
    public function getAllCities(): array
    {
        self::initialize();

        return array_values(self::$data);
    }

    /**
     * Return curated hub/featured cities.
     */
    public function getFeaturedCities(int $limit = 24): array
    {
        self::initialize();
        $featured = [];
        $regular = [];

        foreach (self::$data as $city) {
            if ($city['is_hub']) {
                $featured[] = $city;
            } else {
                $regular[] = $city;
            }
        }

        $merged = array_merge($featured, $regular);

        return array_slice($merged, 0, $limit);
    }

    /**
     * Find a city by its slug or name variant.
     */
    public function findCity(string $slug): ?array
    {
        self::initialize();
        $clean = Str::slug($slug);

        // 1. Direct lookup in slug map
        if (isset(self::$citySlugMap[$clean])) {
            $code = self::$citySlugMap[$clean];

            return self::$data[$code] ?? null;
        }

        // 2. Strip leading 'kota-' if present
        if (str_starts_with($clean, 'kota-')) {
            $sub = substr($clean, 5);
            if (isset(self::$citySlugMap[$sub])) {
                $code = self::$citySlugMap[$sub];

                return self::$data[$code] ?? null;
            }
        }

        // 3. Strip leading 'kabupaten-' if present
        if (str_starts_with($clean, 'kabupaten-')) {
            $sub = substr($clean, 10);
            if (isset(self::$citySlugMap['kab-'.$sub])) {
                $code = self::$citySlugMap['kab-'.$sub];

                return self::$data[$code] ?? null;
            }
            if (isset(self::$citySlugMap[$sub])) {
                $code = self::$citySlugMap[$sub];

                return self::$data[$code] ?? null;
            }
        }

        // 4. Strip leading 'kab-' if present
        if (str_starts_with($clean, 'kab-')) {
            $sub = substr($clean, 4);
            if (isset(self::$citySlugMap[$sub])) {
                $code = self::$citySlugMap[$sub];

                return self::$data[$code] ?? null;
            }
        }

        return null;
    }

    /**
     * Find a district by slug and optional parent city slug.
     */
    public function findDistrict(string $districtSlug, ?string $citySlug = null): ?array
    {
        self::initialize();
        $cleanDist = Str::slug($districtSlug);

        // If parent city is specified
        if ($citySlug !== null) {
            $city = $this->findCity($citySlug);
            if (! $city) {
                return null;
            }

            foreach ($city['districts'] as $dist) {
                if ($dist['slug'] === $cleanDist) {
                    return $this->enrichDistrict($dist, $city);
                }
            }

            return null;
        }

        // Direct lookup without city
        if (isset(self::$districtSlugMap[$cleanDist])) {
            $matches = self::$districtSlugMap[$cleanDist];
            if (! empty($matches)) {
                $first = $matches[0];
                $city = self::$data[$first['city_code']] ?? null;
                if ($city) {
                    return $this->enrichDistrict($first, $city);
                }
            }
        }

        return null;
    }

    /**
     * Enrich district data with parent city, province, and sibling districts.
     */
    private function enrichDistrict(array $district, array $city): array
    {
        $siblings = [];
        foreach ($city['districts'] as $d) {
            if ($d['code'] !== $district['code']) {
                $siblings[] = $d;
            }
        }

        return [
            'name' => $district['name'],
            'code' => $district['code'],
            'slug' => $district['slug'],
            'city_name' => $city['name'],
            'city_raw_name' => $city['raw_name'],
            'city_slug' => $city['slug'],
            'city_type' => $city['type'],
            'city_code' => $city['code'],
            'province' => $city['province'],
            'island' => $city['island'],
            'city' => $city,
            'sibling_districts' => $siblings,
        ];
    }

    /**
     * Get other cities in the same province.
     */
    public function getProvinceCities(string $province, ?string $excludeCode = null, int $limit = 12): array
    {
        self::initialize();
        $result = [];

        foreach (self::$data as $city) {
            if ($city['province'] === $province && $city['code'] !== $excludeCode) {
                $result[] = $city;
                if (count($result) >= $limit) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get other cities in the same island.
     */
    public function getIslandCities(string $island, ?string $excludeCode = null, int $limit = 12): array
    {
        self::initialize();
        $result = [];

        foreach (self::$data as $city) {
            if ($city['island'] === $island && $city['code'] !== $excludeCode) {
                $result[] = $city;
                if (count($result) >= $limit) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Generate Google Maps Embed URL for a city or district safely.
     */
    public function getGoogleMapsEmbedUrl(string $locationName, int $zoom = 13): string
    {
        $query = urlencode($locationName.', Indonesia');

        return "https://maps.google.com/maps?q={$query}&z={$zoom}&output=embed";
    }

    /**
     * Get list of all unique provinces from indexed dataset.
     *
     * @return array<int, array{code: string, name: string, island: string, total_cities: int}>
     */
    public function getProvinces(): array
    {
        self::initialize();
        $provinces = [];

        foreach (self::$data as $city) {
            $code = $city['province_code'];
            if (! isset($provinces[$code])) {
                $provinces[$code] = [
                    'code' => $code,
                    'name' => $city['province'],
                    'island' => $city['island'],
                    'total_cities' => 0,
                ];
            }
            $provinces[$code]['total_cities']++;
        }

        ksort($provinces);

        return array_values($provinces);
    }
}
