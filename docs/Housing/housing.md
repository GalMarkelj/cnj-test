# Housing Data Upload and Analysis Feature

## Overview

This feature allows users to upload a CSV file containing housing data, which is then validated and stored in the
database. It also provides statistical analysis of the stored data, including:

- Total houses sold
- Crime statistics by year
- Average price analysis overall and by year/area

The frontend provides an interface for file upload and displays relevant statistics.

---

## Backend - HousingController

### **Endpoints**

#### Fetch Housing Statistics

**Controller Method:** `index()`

- Retrieves all housing records from the database, if they are present.
- Calculates:
    - Total number of houses sold
    - Crimes by year
    - Overall average price
    - Average price by year and area
- Returns an Inertia response rendering the `index` page with statistics.

##### **Response Example:**

```json
{
    "stats": {
        "sum_of_sold_houses": 15000,
        "crimes_by_year": {
            "2011": 2000,
            "2012": 2500
        },
        "average_price": {
            "overall": 350000,
            "by_year_and_area": {
                "2011": {
                    "City of London": 500000,
                    "Westminster": 450000
                },
                "2012": {
                    "City of London": 550000,
                    "Westminster": 480000
                }
            }
        }
    }
}
```

#### Upload CSV File

**Controller Method:** `uploadDocument(Request $request)`

- Validates and processes a CSV file.
- Checks for duplicate entries.
- If valid, stores data in the database.
- If duplicates exist, the transaction is rolled back.

##### **Validation Rules:**

| Field           | Type                 | Rules                    |
|-----------------|----------------------|--------------------------|
| `date`          | `string`             | Required, `Y-m-d` format |
| `area`          | `string`             | Required, max: 255 chars |
| `average_price` | `unsignedBigInteger` | Required, min: 0         |
| `code`          | `string`             | Required                 |
| `houses_sold`   | `integer`            | Nullable, min: 0         |
| `no_of_crimes`  | `float`              | Nullable, min: 0         |
| `borough_flag`  | `integer`            | Required, `0 or 1`       |

##### **Error Handling:**

If a duplicate row is detected, the response contains an error message with duplicate records:

```json
{
    "message": "Storing the uploaded data failed due to record duplication.",
    "duplicates": [
        {
            "date": "2023-01-01",
            "area": "Westminster"
        },
        {
            "date": "2023-01-02",
            "area": "City of London"
        }
    ]
}
```

---

## Frontend - Housing Data Page (`index.tsx`)

### **Components Used**

- `Card`
- `Checkbox`
- `Button`
- `Input`
- `useForm`, `usePage` from Inertia.js

### **Features**

#### **1. File Upload Form**

- Accepts CSV files
- Validates selection before submission
- Submits data using Inertia’s form submission

#### **2. Display of Statistics**

- Shows:
    - Overall average price
    - Total houses sold
    - Crimes by year
    - Average price trends in `City of London`
- Handles cases where no data exists

#### **3. Error Handling & Duplicates**

- If duplicate records exist, they are displayed under an error message

---

## How to Use

1. **Upload a CSV File**

    - Select a `.csv` file with the correct format.
    - Confirm you want to store the records in the database with the checkbox.
    - Click **Submit**.
    - If data is valid, it is saved in the database.
    - If duplicates exist, or the data is incorrect, an error message appears, and because of the usage of transactions,
      non of the data is stored in to the database.

2. **View Statistics**

    - If data exists, statistics are shown on the page.
    - The average price trend for `City of London` is displayed year by year.

---

## File Format Example

```csv
date,area,average_price,code,houses_sold,no_of_crimes,borough_flag
2023-01-01,City of London,550000,E09000001,100,200,1
2023-01-01,Westminster,450000,E09000033,150,250,1
```

---

## Tests

These tests validate the functionality of uploading CSV files, processing housing data, and displaying the correct
statistical calculations on the frontend.

## File Locations

The test files are located in the following directories:

- **PEST Tests**: `tests/Feature/Housing/`
- **Cypress Tests**: `tests/cypress/integration/housing.cy.js`
- **Cypress Fixtures** (test CSV files): `tests/cypress/fixtures/`

---

## PEST Tests

### Purpose

The PEST tests cover backend validation, ensuring that:

- Only valid CSV files are accepted.
- Required fields exist in the CSV.
- Duplicate records are not stored.
- Data is correctly inserted into the database.
- Uploaded files are deleted after processing.

### Test Descriptions

#### **1. File Validation**

- `fails when a non-CSV file is uploaded`: Ensures that only CSV files are accepted.

#### **2. Data Validation**

- `fails when CSV file has missing required fields`: Ensures required fields are present.
- `fails when uploading duplicate data`: Prevents duplicate records in the database.

#### **3. Database Handling**

- `uploads a valid CSV document and stores records in the database`: Confirms successful data storage.
- `deletes the document after processing`: Ensures the uploaded file is removed after processing.

#### **4. Statistics Calculation**

- `returns null stats when there are no records`: Ensures empty records return `null` stats.
- `returns correct statistics and records (with all the stats present)`: Validates computed statistics.
- `returns correct statistics and records (with some of the stats missing)`: Ensures missing data is handled correctly.

---

## Cypress Tests

### Purpose

The Cypress tests focus on frontend behavior, verifying that:

- The correct error messages are displayed for invalid files.
- The UI correctly handles missing data.
- The upload process works as expected.
- The displayed statistics match the uploaded data.

### Test Descriptions

#### **1. Initial State**

- `shows message when no data is available`: Ensures a message appears when no data exists.

#### **2. File Upload Validation**

- `uploads a non-csv document`: Ensures non-CSV files are rejected.
- `uploads a csv document with duplicated records`: Detects and reports duplicate records.
- `uploads a csv document with missing date`: Validates that date is required.
- `uploads a csv document with missing area`: Ensures an area field is mandatory.

#### **3. Successful Upload**

- `uploads a CSV file successfully and checks the stats`: Verifies correct data display after upload.

#### **4. UI Behavior**

- `tries to submit without checkbox confirmation`: Ensures the confirmation checkbox is required.
- `tries to submit without a file`: Prevents submission without a file.
