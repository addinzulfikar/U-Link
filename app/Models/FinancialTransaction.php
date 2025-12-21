<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    use HasFactory;

    const TYPE_INCOME = 'Pemasukan';
    const TYPE_EXPENSE = 'Pengeluaran';

    protected $fillable = [
        'umkm_id',
        'spreadsheet_upload_id',
        'transaction_date',
        'transaction_type',
        'description',
        'amount',
        'source_file',
        'row_number',
        'validation_errors',
    ];

    protected $casts = [
        'umkm_id' => 'integer',
        'spreadsheet_upload_id' => 'integer',
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'row_number' => 'integer',
        'validation_errors' => 'array',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function spreadsheetUpload(): BelongsTo
    {
        return $this->belongsTo(SpreadsheetUpload::class);
    }

    public function isIncome(): bool
    {
        return $this->transaction_type === self::TYPE_INCOME;
    }

    public function isExpense(): bool
    {
        return $this->transaction_type === self::TYPE_EXPENSE;
    }

    public function hasErrors(): bool
    {
        return !empty($this->validation_errors);
    }
}
