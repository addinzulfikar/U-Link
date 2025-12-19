<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpreadsheetUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'umkm_id',
        'user_id',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'analysis_result',
        'analyzed_at',
    ];

    protected $casts = [
        'umkm_id' => 'integer',
        'user_id' => 'integer',
        'file_size' => 'integer',
        'analysis_result' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2).' '.$units[$index];
    }

    public function getFileTypeLabel(): string
    {
        return match ($this->file_type) {
            'xlsx' => 'Excel (.xlsx)',
            'xls' => 'Excel (.xls)',
            'csv' => 'CSV',
            'ods' => 'OpenDocument Spreadsheet',
            default => strtoupper($this->file_type),
        };
    }
}
