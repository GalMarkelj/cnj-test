<?php

namespace App\Http\Controllers;

use App\Models\Housing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class HousingController extends Controller
{
    public function index()
    {
        $records = Housing::all();
        $recordsCount = $records->count();

        $sumOfAvgPrices = 0;
        $sumOfSoldHouses = 0;
        $crimesByYear = [];

        $sumOfAvgPriceByYearAndArea = [];
        $averagePriceByYearAndArea = [];

        if ($recordsCount > 0) {
            foreach ($records as $row) {
                $year = date('Y', strtotime($row['date']));
                $area = $row['area'];

                // Overall average price
                $sumOfAvgPrices += $row['average_price'];

                // Sum houses sold (ignoring null values)
                if (!is_null($row['houses_sold'])) {
                    $sumOfSoldHouses += $row['houses_sold'];
                }

                // Sum no_of_crimes by year (ignoring null values)
                if (!is_null($row['no_of_crimes'])) {
                    if (!isset($crimesByYear[$year])) {
                        $crimesByYear[$year] = 0;
                    }
                    $crimesByYear[$year] += $row['no_of_crimes'];
                }

                // Sum average price by year and area
                if (!isset($sumOfAvgPriceByYearAndArea[$year][$area])) {
                    $sumOfAvgPriceByYearAndArea[$year][$area] = ['sum' => 0, 'count' => 0];
                }
               $sumOfAvgPriceByYearAndArea[$year][$area]['sum'] += $row['average_price'];
               $sumOfAvgPriceByYearAndArea[$year][$area]['count']++;
            }

            // Calculate average price by year and area
            foreach ($sumOfAvgPriceByYearAndArea as $year => $areas) {
                foreach ($areas as $area => $data) {
                    $averagePriceByYearAndArea[$year][$area] = $data['sum'] / $data['count'];
                }
            }
        }

        return Inertia::render('index', [
            'housings' => $records,
            'stats' => $recordsCount == 0 ? null : [
                'sum_of_sold_houses' => $sumOfSoldHouses,
                'crimes_by_year' => $crimesByYear,
                'average_price' => [
                    'overall' => $recordsCount > 0 ? $sumOfAvgPrices / $recordsCount : 0,
                    'by_year_and_area' => $averagePriceByYearAndArea
                ],
            ]
        ]);
    }
    //
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'housing_document' => 'required|mimes:csv|max:203480'
        ]);

        $filePath = $request->file('housing_document')->store('uploads/housing_documents');
        $fullPath = storage_path("app/private/$filePath");

        // Open file and process line by line
        $handle = fopen($fullPath, 'r');
        fgetcsv($handle); // Skip header row

        $duplicates = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $existingRecord = Housing::where('date', $row[0])
                ->where('area', $row[1])
                ->first();

            if ($existingRecord) {
                $duplicates[] = ['date' => $row[0], 'area' => $row[1]];
                $duplicates[] = ['date' => $existingRecord->date, 'area' => $existingRecord->area];
                $existingRecord->delete();
            } else {
                Housing::create([
                    'uuid' => (string) Str::ulid(),
                    'date' => $row[0],
                    'area' => $row[1],
                    'average_price' => $row[2],
                    'code' => $row[3],
                    'houses_sold' => !empty($row[5]) ? $row[5] : null,
                    'no_of_crimes' => !empty($row[5]) ? (float) $row[5] : null,
                    'borough_flag' => $row[6],
                ]);
            }
        }

        fclose($handle);

        // If there are duplicates, return a warning message, but still proceed with the insertions
        if (count($duplicates) > 0) {
            throw ValidationException::withMessages(['message' => 'There are duplicate records.', 'duplicates' => json_encode($duplicates)]);
        }

        return back()->with('success', 'Data successfully stored to the database.');
    }
}
