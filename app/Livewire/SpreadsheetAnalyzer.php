<?php

namespace App\Livewire;

use App\Models\SpreadsheetUpload;
use App\Services\SpreadsheetAnalyzerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SpreadsheetAnalyzer extends Component
{
    use WithFileUploads;

    public $spreadsheetFile;
    
    public $spreadsheetFiles = []; // Support multiple files

    public $uploadedFiles = [];

    public $selectedUpload = null;

    public $analysisResult = null;
    
    public $financialOverview = null;

    public $isAnalyzing = false;
    
    public $isProcessingFinancials = false;

    public $errorMessage = null;

    public $successMessage = null;

    protected $rules = [
        'spreadsheetFile' => 'nullable|file|mimes:xlsx,xls,csv,ods|max:10240', // Max 10MB
        'spreadsheetFiles.*' => 'nullable|file|mimes:xlsx,xls,csv,ods|max:10240', // Max 10MB each
    ];

    protected $messages = [
        'spreadsheetFile.file' => 'File tidak valid.',
        'spreadsheetFile.mimes' => 'Format file harus Excel (.xlsx, .xls), CSV, atau OpenDocument (.ods).',
        'spreadsheetFile.max' => 'Ukuran file maksimal 10MB.',
        'spreadsheetFiles.*.file' => 'File tidak valid.',
        'spreadsheetFiles.*.mimes' => 'Format file harus Excel (.xlsx, .xls), CSV, atau OpenDocument (.ods).',
        'spreadsheetFiles.*.max' => 'Ukuran file maksimal 10MB per file.',
    ];

    public function mount()
    {
        $this->loadUploadedFiles();
        $this->loadFinancialOverview();
    }

    public function loadUploadedFiles()
    {
        $umkm = Auth::user()->umkm;

        if ($umkm) {
            $this->uploadedFiles = SpreadsheetUpload::where('umkm_id', $umkm->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->uploadedFiles = collect();
        }
    }
    
    public function loadFinancialOverview()
    {
        $umkm = Auth::user()->umkm;
        
        if ($umkm) {
            $service = app(\App\Services\FinancialOverviewService::class);
            try {
                $this->financialOverview = $service->generateOverview($umkm->id);
            } catch (\Exception $e) {
                // Silently fail if no data yet
                $this->financialOverview = null;
            }
        }
    }

    public function uploadAndAnalyze()
    {
        $this->validate();

        $this->errorMessage = null;
        $this->successMessage = null;
        $this->isAnalyzing = true;

        try {
            $umkm = Auth::user()->umkm;

            if (! $umkm) {
                throw new \Exception('Anda belum memiliki UMKM. Silakan daftarkan UMKM terlebih dahulu.');
            }

            // Store the file
            $originalName = $this->spreadsheetFile->getClientOriginalName();
            $extension = $this->spreadsheetFile->getClientOriginalExtension();
            $filename = time().'_'.uniqid().'.'.$extension;
            $path = $this->spreadsheetFile->storeAs('spreadsheets/'.$umkm->id, $filename, 'local');

            // Create upload record
            $upload = SpreadsheetUpload::create([
                'umkm_id' => $umkm->id,
                'user_id' => Auth::id(),
                'filename' => $filename,
                'original_filename' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $this->spreadsheetFile->getSize(),
            ]);

            // Analyze the file
            $analyzer = new SpreadsheetAnalyzerService;
            $result = $analyzer->analyze($path);

            // Save analysis result
            $upload->update([
                'analysis_result' => $result,
                'analyzed_at' => now(),
            ]);

            $this->analysisResult = $result;
            $this->selectedUpload = $upload;
            $this->successMessage = 'File berhasil diupload dan dianalisis!';
            $this->spreadsheetFile = null;
            $this->loadUploadedFiles();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: '.$e->getMessage();
        }

        $this->isAnalyzing = false;
    }

    public function viewAnalysis($uploadId)
    {
        $upload = SpreadsheetUpload::find($uploadId);

        if ($upload && $upload->umkm_id === Auth::user()->umkm?->id) {
            $this->selectedUpload = $upload;
            $this->analysisResult = $upload->analysis_result;
        }
    }

    public function reanalyze($uploadId)
    {
        $upload = SpreadsheetUpload::find($uploadId);

        if (! $upload || $upload->umkm_id !== Auth::user()->umkm?->id) {
            $this->errorMessage = 'File tidak ditemukan.';

            return;
        }

        $this->isAnalyzing = true;
        $this->errorMessage = null;

        try {
            $analyzer = new SpreadsheetAnalyzerService;
            $result = $analyzer->analyze($upload->file_path);

            $upload->update([
                'analysis_result' => $result,
                'analyzed_at' => now(),
            ]);

            $this->analysisResult = $result;
            $this->selectedUpload = $upload;
            $this->successMessage = 'File berhasil dianalisis ulang!';
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: '.$e->getMessage();
        }

        $this->isAnalyzing = false;
    }

    public function downloadFile($uploadId)
    {
        $upload = SpreadsheetUpload::find($uploadId);

        if (! $upload || $upload->umkm_id !== Auth::user()->umkm?->id) {
            $this->errorMessage = 'File tidak ditemukan.';

            return;
        }

        return Storage::download($upload->file_path, $upload->original_filename);
    }

    public function deleteFile($uploadId)
    {
        $upload = SpreadsheetUpload::find($uploadId);

        if (! $upload || $upload->umkm_id !== Auth::user()->umkm?->id) {
            $this->errorMessage = 'File tidak ditemukan.';

            return;
        }

        try {
            // Delete the file from storage
            Storage::delete($upload->file_path);

            // Delete the record
            $upload->delete();

            $this->successMessage = 'File berhasil dihapus!';

            if ($this->selectedUpload && $this->selectedUpload->id === $uploadId) {
                $this->selectedUpload = null;
                $this->analysisResult = null;
            }

            $this->loadUploadedFiles();
        } catch (\Exception $e) {
            $this->errorMessage = 'Gagal menghapus file: '.$e->getMessage();
        }
    }

    public function closeAnalysis()
    {
        $this->selectedUpload = null;
        $this->analysisResult = null;
    }
    
    public function uploadAndProcessFinancials()
    {
        $this->validate([
            'spreadsheetFiles.*' => 'required|file|mimes:xlsx,xls,csv,ods|max:10240',
        ]);

        $this->errorMessage = null;
        $this->successMessage = null;
        $this->isProcessingFinancials = true;

        try {
            $umkm = Auth::user()->umkm;

            if (! $umkm) {
                throw new \Exception('Anda belum memiliki UMKM. Silakan daftarkan UMKM terlebih dahulu.');
            }

            $filePaths = [];
            $uploadIds = [];

            // Store all files first
            foreach ($this->spreadsheetFiles as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = time().'_'.uniqid().'.'.$extension;
                $path = $file->storeAs('spreadsheets/'.$umkm->id, $filename, 'local');

                // Create upload record
                $upload = SpreadsheetUpload::create([
                    'umkm_id' => $umkm->id,
                    'user_id' => Auth::id(),
                    'filename' => $filename,
                    'original_filename' => $originalName,
                    'file_path' => $path,
                    'file_type' => $extension,
                    'file_size' => $file->getSize(),
                ]);

                $filePaths[] = Storage::path($path);
                $uploadIds[] = $upload->id;
            }

            // Process all files and merge financial data
            $financialService = app(\App\Services\FinancialOverviewService::class);
            $result = $financialService->processFinancialData($filePaths, $umkm->id, $uploadIds[0] ?? null);

            // Update upload records with processing results
            foreach ($uploadIds as $uploadId) {
                SpreadsheetUpload::find($uploadId)->update([
                    'analysis_result' => [
                        'type' => 'financial_data',
                        'processing_stats' => $result['stats'],
                        'errors' => $result['errors'],
                    ],
                    'analyzed_at' => now(),
                ]);
            }

            $errorCount = count($result['errors']);
            $successCount = $result['stats']['total_rows_imported'];
            
            if ($errorCount > 0) {
                $this->successMessage = "Berhasil memproses {$successCount} transaksi dari {$result['stats']['total_files']} file. Terdapat {$errorCount} baris dengan kesalahan yang tetap diimpor.";
            } else {
                $this->successMessage = "Berhasil memproses {$successCount} transaksi dari {$result['stats']['total_files']} file tanpa kesalahan!";
            }

            $this->spreadsheetFiles = [];
            $this->loadUploadedFiles();
            $this->loadFinancialOverview();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: '.$e->getMessage();
        }

        $this->isProcessingFinancials = false;
    }

    public function render()
    {
        return view('livewire.spreadsheet-analyzer');
    }
}
