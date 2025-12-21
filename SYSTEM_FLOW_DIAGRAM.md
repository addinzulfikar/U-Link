# System Flow Diagram - Financial Tracking Feature

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           USER FLOW                                      │
└─────────────────────────────────────────────────────────────────────────┘

1. ADMIN LOGIN
   │
   ├──> Access Spreadsheet Analyzer Page
   │
   └──> Download Template Button
        │
        ├──> UmkmTemplateExport::generate()
        │    │
        │    ├──> Sheet 1: Product Template (Original)
        │    └──> Sheet 2: Pemasukan & Pengeluaran (NEW)
        │         └──> Columns: No | Tanggal | Jenis | Keterangan | Nominal
        │
        └──> Download: Template_Produk_[UMKM]_[Date].xlsx

2. FILL IN EXCEL
   │
   ├──> Sheet 1: Products (optional)
   └──> Sheet 2: Financial Transactions
        └──> Add income/expense data

3. UPLOAD TO SYSTEM
   │
   ├──> Option A: Single File (Analysis)
   │    └──> SpreadsheetAnalyzer::uploadAndAnalyze()
   │         └──> SpreadsheetAnalyzerService::analyze()
   │              └──> Shows structure analysis
   │
   └──> Option B: Multiple Files (Financial Processing) ⭐ NEW
        └──> SpreadsheetAnalyzer::uploadAndProcessFinancials()
             │
             ├──> Store all files
             ├──> FinancialOverviewService::processFinancialData()
             │    │
             │    ├──> Loop through each file
             │    │    │
             │    │    ├──> Find "Pemasukan & Pengeluaran" sheet
             │    │    ├──> Detect header row automatically
             │    │    ├──> Map columns (date, type, amount, description)
             │    │    │
             │    │    └──> Parse each row
             │    │         │
             │    │         ├──> Parse date (DD/MM/YYYY, YYYY-MM-DD, Excel)
             │    │         ├──> Parse type (Pemasukan/Pengeluaran)
             │    │         ├──> Parse amount (remove Rp, dots, commas)
             │    │         │
             │    │         └──> If ERROR → Log but CONTINUE ✅
             │    │
             │    └──> Merge all data into single dataset
             │
             └──> Save to database: financial_transactions table

4. VIEW DASHBOARD ⭐ NEW
   │
   └──> FinancialOverviewService::generateOverview()
        │
        ├──> Calculate 5 Key Metrics:
        │    │
        │    ├──> 💎 Total Asset Value
        │    │    └──> = Stock Value + Net Balance
        │    │
        │    ├──> 📦 Total Stock Value
        │    │    └──> = Σ(Product.price × Product.stock)
        │    │
        │    ├──> 📈 Total Income
        │    │    └──> = Σ(transactions where type = 'Pemasukan')
        │    │
        │    ├──> 📉 Total Expenses
        │    │    └──> = Σ(transactions where type = 'Pengeluaran')
        │    │
        │    └──> 💰 Net Balance
        │         └──> = Total Income - Total Expenses
        │
        ├──> Generate Monthly Trends (6 months)
        │    └──> Group by month, sum income/expense
        │
        └──> Display on Dashboard
             │
             ├──> 5 Colored Cards (metrics)
             ├──> Monthly Trends Table
             └──> Error Warning (if any)
```

---

## Data Flow Diagram

```
┌──────────────┐
│ Excel Files  │ (Multiple files can be uploaded)
│ File1.xlsx   │
│ File2.xlsx   │
│ File3.xlsx   │
└──────┬───────┘
       │
       ▼
┌──────────────────────────────────────────────┐
│  FinancialOverviewService                    │
│  ┌────────────────────────────────────┐     │
│  │ processFinancialData()             │     │
│  │  • Find correct sheet              │     │
│  │  • Detect headers                  │     │
│  │  • Map columns                     │     │
│  │  • Parse rows                      │     │
│  │  • Handle errors gracefully        │     │
│  │  • Merge all data                  │     │
│  └────────────────────────────────────┘     │
└──────────────────┬───────────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │ financial_transactions│
        │ ┌──────────────────┐ │
        │ │ id               │ │
        │ │ umkm_id          │ │
        │ │ transaction_date │ │
        │ │ transaction_type │ │ ← 'Pemasukan' or 'Pengeluaran'
        │ │ description      │ │
        │ │ amount           │ │
        │ │ source_file      │ │ ← Track which file
        │ │ row_number       │ │ ← Track which row
        │ │ validation_errors│ │ ← JSON of errors (if any)
        │ └──────────────────┘ │
        └──────────┬────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │ generateOverview()   │
        │  • Calculate metrics │
        │  • Monthly trends    │
        └──────────┬────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │   Dashboard View     │
        │  ┌────────────────┐  │
        │  │ 💎 Total Aset  │  │
        │  │ 📦 Stok Value  │  │
        │  │ 📈 Pemasukan   │  │
        │  │ 📉 Pengeluaran │  │
        │  │ 💰 Saldo       │  │
        │  ├────────────────┤  │
        │  │ Monthly Trends │  │
        │  │ (Table)        │  │
        │  └────────────────┘  │
        └──────────────────────┘
```

---

## Error Handling Flow

```
Row Data: [Date, Type, Amount, Description]
│
├──> Parse Date
│    ├──> ✅ Valid → Store date
│    └──> ❌ Invalid → Add error "Format tanggal tidak valid"
│
├──> Parse Type
│    ├──> ✅ Valid (Pemasukan/Pengeluaran) → Store type
│    └──> ❌ Invalid → Add error "Jenis transaksi tidak valid"
│
├──> Parse Amount
│    ├──> ✅ Valid number → Store amount
│    └──> ❌ Invalid → Add error "Nominal tidak valid"
│
└──> Save Transaction
     │
     ├──> Has Errors? → Store in validation_errors field (JSON)
     │                   But STILL SAVE THE ROW ✅
     │
     └──> No Errors? → validation_errors = null
                       Save normally ✅

Result:
• All rows processed ✅
• Errors logged per transaction ✅
• Process doesn't stop ✅
• User gets summary:
  "Berhasil memproses 45 transaksi dari 3 file.
   Terdapat 5 baris dengan kesalahan yang tetap diimpor."
```

---

## Database Schema

```sql
CREATE TABLE financial_transactions (
    id BIGSERIAL PRIMARY KEY,
    umkm_id BIGINT NOT NULL,
    spreadsheet_upload_id BIGINT NULL,
    transaction_date DATE NOT NULL,
    transaction_type VARCHAR(20) NOT NULL,  -- 'Pemasukan' or 'Pengeluaran'
    description TEXT NULL,
    amount DECIMAL(15,2) NOT NULL,
    source_file VARCHAR(255) NULL,          -- Track origin
    row_number INTEGER NULL,                -- Track row in spreadsheet
    validation_errors JSON NULL,            -- Store errors if any
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (umkm_id) REFERENCES umkms(id) ON DELETE CASCADE,
    FOREIGN KEY (spreadsheet_upload_id) REFERENCES spreadsheet_uploads(id) ON DELETE SET NULL,
    
    INDEX idx_umkm_id (umkm_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_transaction_type (transaction_type)
);
```

---

## Test Coverage

```
tests/Unit/UmkmTemplateExportTest.php
├─ test_template_has_two_sheets ✅
├─ test_first_sheet_is_product_template ✅
├─ test_second_sheet_is_income_expense_template ✅
└─ test_second_sheet_has_correct_column_structure ✅

tests/Unit/FinancialOverviewServiceTest.php
├─ test_can_find_header_row ✅
├─ test_can_map_columns ✅
├─ test_can_parse_transaction_type ✅
├─ test_can_parse_date_from_string ✅
└─ test_can_parse_amount ✅

Total: 9 tests, 59 assertions, all passing ✅
```

---

## Key Features Summary

✅ Second sheet in template with correct structure
✅ Multiple file upload support
✅ Automatic data merging
✅ Graceful error handling (continues on error)
✅ 5 metrics dashboard
✅ Monthly trends visualization
✅ Flexible date/amount parsing
✅ Source tracking (file + row)
✅ Error logging per transaction
✅ Comprehensive test coverage
✅ Complete documentation (tech + user guide)
