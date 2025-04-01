<?php

namespace App\Http\Controllers;

use App\Models\Housing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        // Validate the file
        $request->validate([
            'housing_document' => 'required|mimes:csv|max:203480',
        ]);

        // Store the file temporarily and get its full path
        $filePath = $request->file('housing_document')->store('uploads/housing_documents');
        $fullPath = storage_path("app/private/$filePath");

        $duplicates = [];

        // If there is a duplicated row, bad row, non of the other records are stored
        DB::beginTransaction();

        try {
            // Open file and process line by line
            if (($handle = fopen($fullPath, 'r')) === false) {
                throw new \Exception('Unable to open the CSV file.');
            }

            fgetcsv($handle); // Skip header row

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Perform row validation
                $validator = Validator::make([
                    'date' => $row[0],
                    'area' => $row[1],
                    'average_price' => $row[2],
                    'code' => $row[3],
                    'houses_sold' => $row[4] ?? null,
                    'no_of_crimes' => $row[5] ?? null,
                    'borough_flag' => $row[6],
                ], [
                    'date' => 'required|date_format:Y-m-d',
                    'area' => 'required|string|max:255',
                    'average_price' => 'required|numeric|min:0',
                    'code' => 'required|string',
                    'houses_sold' => 'nullable|integer|min:0',
                    'no_of_crimes' => 'nullable|numeric|min:0',
                    'borough_flag' => 'required|integer|in:0,1',
                ]);

                // If validation fails, rollback and throw exception
                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                // Check if record already exists
                $exists = Housing::where('date', $row[0])
                    ->where('area', $row[1])
                    ->exists();

                if ($exists) {
                    $duplicates[] = ['date' => $row[0], 'area' => $row[1]];
                } else {
                    Housing::create([
                        'date' => $row[0],
                        'area' => $row[1],
                        'average_price' => $row[2],
                        'code' => $row[3],
                        'houses_sold' => !empty($row[4]) ? $row[4] : null,
                        'no_of_crimes' => !empty($row[5]) ? (float)$row[5] : null,
                        'borough_flag' => $row[6],
                    ]);
                }
            }

            fclose($handle); // Close file handle

            // If there are no duplicates, commit the transaction
            if (empty($duplicates)) {
                DB::commit();
                // Delete the file after processing
                Storage::delete($filePath);

                return back()->with('success', 'Data successfully stored to the database.');
            }

            // If there are duplicates, rollback the transaction
            DB::rollBack();

            // Delete the file after processing
            Storage::delete($filePath);

            throw new \Exception('Storing the uploaded data failed due to record duplication. Please review and re-upload the document.', 1);
        } catch (\Exception $e) {
            // Ensure the file is deleted even if an exception occurs
            if (file_exists($fullPath)) {
                Storage::delete($filePath);
            }

            // Handle exception and provide feedback
            DB::rollBack();
            return back()->withErrors([
                'message' => $e->getMessage(),
                'duplicates' => json_encode($duplicates),
            ]);
        }
    }
}
