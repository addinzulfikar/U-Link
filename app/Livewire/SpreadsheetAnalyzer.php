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

    public $uploadedFiles = [];

    public $selectedUpload = null;

    public $analysisResult = null;

    public $isAnalyzing = false;

    public $errorMessage = null;

    public $successMessage = null;

    protected $rules = [
        'spreadsheetFile' => 'required|file|mimes:xlsx,xls,csv,ods|max:10240', // Max 10MB
    ];

    protected $messages = [
        'spreadsheetFile.required' => 'Silakan pilih file spreadsheet.',
        'spreadsheetFile.file' => 'File tidak valid.',
        'spreadsheetFile.mimes' => 'Format file harus Excel (.xlsx, .xls), CSV, atau OpenDocument (.ods).',
        'spreadsheetFile.max' => 'Ukuran file maksimal 10MB.',
    ];

    public function mount()
    {
        $this->loadUploadedFiles();
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

    public function render()
    {
        return view('livewire.spreadsheet-analyzer');
    }
}
