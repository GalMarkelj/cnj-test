<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Models\Housing;

test('fails when a non-CSV file is uploaded', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->post(route('housing.document.upload'), [
        'housing_document' => $file,
    ]);

    $response->assertSessionHasErrors(['housing_document']);
});

test('fails when CSV file has missing required fields', function () {
    $csvContent = "date,area,average_price,code,houses_sold,no_of_crimes,borough_flag\n" .
        "2023-01-01,London,,LDN,10,5,1\n"; // Missing average_price

    $file = UploadedFile::fake()->createWithContent('housing.csv', $csvContent);

    $response = $this->post(route('housing.document.upload'), [
        'housing_document' => $file,
    ]);

    $response->assertSessionHasErrors(['message']);
});

// date and area are tied together and unique
test('fails when uploading duplicate data', function () {
    $data = [
        'uuid' => (string)Str::ulid(),
        'date' => '2023-01-01',
        'area' => 'London',
        'average_price' => 500000,
        'code' => 'LDN',
        'houses_sold' => 10,
        'no_of_crimes' => 5,
        'borough_flag' => 1,
    ];
    Housing::create($data);

    $csvContent = "date,area,average_price,code,houses_sold,no_of_crimes,borough_flag\n" .
        "2023-01-01,London,500000,LDN,10,5,1\n"; // Duplicate record

    $file = UploadedFile::fake()->createWithContent('housing.csv', $csvContent);

    $response = $this->post(route('housing.document.upload'), [
        'housing_document' => $file,
    ]);

    $response->assertSessionHasErrors(['message']);
    // Assert that the data is missing due to the DB transaction
    $this->assertDatabaseMissing('housing', $data);
});

test('uploading a valid CSV document and store records in the database', function () {
    // Create a temporary CSV file
    $csvContent = "date,area,average_price,code,houses_sold,no_of_crimes,borough_flag\n" .
        "2025-01-01,London,500000,LDN,10,5,1\n";
    $file = UploadedFile::fake()->createWithContent('housing.csv', $csvContent);

    // Send request
    $response = $this->post(route('housing.document.upload'), [
        'housing_document' => $file,
    ]);

    // Assert response
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Data successfully stored to the database.');

    // Assert database has the new record
    $this->assertDatabaseHas('housing', [
        'date' => '2025-01-01',
        'area' => 'London',
        'average_price' => 500000,
        'code' => 'LDN',
        'houses_sold' => 10,
        'no_of_crimes' => 5,
        'borough_flag' => 1,
    ]);
});
