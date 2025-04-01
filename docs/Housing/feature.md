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

1. **Upload a CSV File** (I have added a valid and a original non valid fiels to the root of the project, `valid.csv`
   and `original-non-valid.csv`)

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
