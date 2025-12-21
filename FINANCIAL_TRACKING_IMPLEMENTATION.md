# Financial Tracking Implementation

## Overview
This implementation adds comprehensive financial tracking capabilities to U-LINK, allowing UMKM admin to upload and manage income/expense data from spreadsheets.

## Key Features Implemented

### 1. Enhanced Excel Template
The UMKM template now includes **two sheets**:

#### Sheet 1: Template Produk (Original)
- Columns: Nama Produk, Tipe, Kategori, Deskripsi, Harga, Stok, Status
- Used for managing product/service inventory

#### Sheet 2: Pemasukan & Pengeluaran (NEW)
- Columns:
  - **No**: Sequential number
  - **Tanggal**: Transaction date
  - **Jenis Transaksi**: Type (Pemasukan/Pengeluaran)
  - **Keterangan**: Description/notes
  - **Nominal (Rp)**: Amount in Rupiah

### 2. Multiple File Upload Support
- Admin can now upload **multiple spreadsheet files** at once
- System automatically **merges data** from all files into a single dataset
- Supports Excel (.xlsx, .xls), CSV, and ODS formats

### 3. Error Handling
- System **continues processing** even when errors are found
- Errors are **logged** but don't stop the import process
- Each transaction record stores validation errors in JSON format
- Admin receives a summary of:
  - Total rows processed
  - Total rows successfully imported
  - Number of errors encountered

### 4. Financial Overview Dashboard
The system displays a comprehensive overview with **5 key metrics**:

1. **Total Nilai Aset** (Total Asset Value)
   - Calculated as: Stock Value + Net Balance
   
2. **Total Nilai Barang Stok** (Total Stock Value)
   - Sum of (Product Price × Stock Quantity) for all products
   
3. **Total Pemasukan** (Total Income)
   - Sum of all income transactions
   
4. **Total Pengeluaran** (Total Expenses)
   - Sum of all expense transactions
   
5. **Saldo Bersih** (Net Balance)
   - Calculated as: Total Income - Total Expenses

### 5. Visualizations
- **Monthly Trends Table**: Shows income, expense, and balance for the last 6 months
- **Color-coded Cards**: Different colors for positive/negative balances
- **Transaction Statistics**: Count of income/expense transactions

## Database Schema

### New Table: `financial_transactions`
```sql
- id (bigint, primary key)
- umkm_id (foreign key to umkms)
- spreadsheet_upload_id (foreign key to spreadsheet_uploads, nullable)
- transaction_date (date)
- transaction_type (enum: 'Pemasukan', 'Pengeluaran')
- description (text, nullable)
- amount (decimal 15,2)
- source_file (string, nullable) - tracks which file data came from
- row_number (integer, nullable) - tracks row in spreadsheet
- validation_errors (json, nullable) - stores any validation errors
- timestamps
```

## Usage Instructions

### For UMKM Admin:

1. **Download Template**
   - Click "Download Template" in the admin dashboard
   - You'll receive an Excel file with two sheets

2. **Fill in Financial Data**
   - Navigate to "Pemasukan & Pengeluaran" sheet
   - Fill in your income and expense transactions
   - Use date format: DD/MM/YYYY or YYYY-MM-DD
   - Transaction type must be: "Pemasukan" or "Pengeluaran"

3. **Upload Files**
   - Go to "Spreadsheet Analyzer" page
   - Use "Upload Data Keuangan" section
   - Select one or multiple files
   - Click "Upload & Proses Data Keuangan"

4. **View Dashboard**
   - Financial overview automatically updates
   - View all 5 key metrics at a glance
   - Check monthly trends table

### Supported Date Formats:
- Excel date numbers (automatic)
- YYYY-MM-DD (2024-12-21)
- DD/MM/YYYY (21/12/2024)
- DD-MM-YYYY (21-12-2024)

### Transaction Type Values:
**For Income:**
- Pemasukan
- Income
- Masuk
- Pendapatan
- Revenue

**For Expenses:**
- Pengeluaran
- Expense
- Keluar
- Biaya
- Cost

## Technical Implementation

### Services Created:
1. **FinancialOverviewService** (`app/Services/FinancialOverviewService.php`)
   - Processes uploaded financial data
   - Handles multiple file merging
   - Generates overview statistics
   - Calculates monthly trends

### Models Created:
1. **FinancialTransaction** (`app/Models/FinancialTransaction.php`)
   - Stores individual transaction records
   - Tracks validation errors
   - Links to UMKM and upload records

### Livewire Components Updated:
1. **SpreadsheetAnalyzer** (`app/Livewire/SpreadsheetAnalyzer.php`)
   - Added `uploadAndProcessFinancials()` method
   - Added `loadFinancialOverview()` method
   - Support for multiple file uploads

### Views Updated:
1. **spreadsheet-analyzer.blade.php**
   - Added financial overview dashboard section
   - Added multiple file upload form
   - Added monthly trends visualization

## Error Handling Strategy

The system implements **graceful error handling**:

1. **File-level errors**: Logged but other files continue processing
2. **Row-level errors**: Invalid rows are imported with error flags
3. **Validation errors** stored in each transaction record:
   - Invalid date format
   - Invalid transaction type
   - Invalid amount
   - Missing required fields

4. **User feedback**:
   - Success message shows count of imported transactions
   - Warning shown if any errors were found
   - Detailed error information available per transaction

## Migration Steps

To deploy this feature:

```bash
# Run the migration
php artisan migrate

# The new table will be created:
# - financial_transactions
```

## Future Enhancements (Not implemented)

Possible additions in the future:
- Charts/graphs for visual trend analysis
- Export financial reports to PDF
- Categorization of expenses
- Budget planning and alerts
- Comparison with previous periods
- Transaction filtering and search
