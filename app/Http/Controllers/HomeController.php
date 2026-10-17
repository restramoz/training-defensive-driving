<?php

namespace App\Http\Controllers;

use App\Services\RegionalService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(RegionalService $regionalService): View
    {
        $allCities = $regionalService->getAllCities();

        $perPage = 24;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($allCities, $offset, $perPage);
        $paginatedCities = new LengthAwarePaginator(
            $items,
            count($allCities),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        /** @var array<int, array{name: string, raw_name: string, type: string, province: string, slug: string}> */
        $allCitiesForSearch = array_map(fn ($c) => [
            'name' => $c['name'],
            'raw_name' => $c['raw_name'],
            'type' => $c['type'],
            'province' => $c['province'],
            'slug' => $c['slug'],
        ], $allCities);

        return view('home', compact('paginatedCities', 'allCitiesForSearch'));
    }
}
