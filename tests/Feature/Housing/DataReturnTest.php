<?php

use App\Models\Housing;
use Illuminate\Support\Str;

it('returns null stats when there are no records', function () {
    $response = $this->get(route('index'));
    $response->assertInertia(fn($page) => $page->where('stats', null));
});

it('returns correct statistics and records (with all the stats present)', function () {
    Housing::create([
        'uuid' => (string)Str::ulid(),
        'date' => '2023-01-01',
        'area' => 'London',
        'average_price' => 500000,
        'code' => 'LDN',
        'houses_sold' => 10,
        'no_of_crimes' => 5,
        'borough_flag' => 1,
    ]);

    $response = $this->get(route('index'));

    $response->assertInertia(fn($page) => $page->has('stats')
        ->where('stats.sum_of_sold_houses', 10)
        ->where('stats.crimes_by_year.2023', 5)
        ->where('stats.average_price.overall', 500000)
        ->where('stats.average_price.by_year_and_area.2023.London', 500000)
    );
});

it('returns correct statistics and records (with some of the stats missing)', function () {
    Housing::create([
        'uuid' => (string)Str::ulid(),
        'date' => '2023-01-01',
        'area' => 'London',
        'average_price' => 500000,
        'code' => 'LDN',
        'houses_sold' => null,
        'no_of_crimes' => null,
        'borough_flag' => 1,
    ]);

    $response = $this->get(route('index'));

    $response->assertInertia(fn($page) => $page->has('stats')
        ->where('stats.sum_of_sold_houses', 0) // The default value it's 0
        ->where('stats.average_price.overall', 500000)
        ->where('stats.average_price.by_year_and_area.2023.London', 500000)
        // Ensure `stats.crimes_by_year` does not have `2023`
        ->where('stats.crimes_by_year', fn($crimes) => !isset($crimes['2023']))
    );
});
