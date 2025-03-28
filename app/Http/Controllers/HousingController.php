<?php

namespace App\Http\Controllers;

use App\Models\Housing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HousingController extends Controller
{
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

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
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

        fclose($handle);

        return back()->with('success', 'Data successfully stored to the database.');
    }
}
