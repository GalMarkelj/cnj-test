# Tests

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
