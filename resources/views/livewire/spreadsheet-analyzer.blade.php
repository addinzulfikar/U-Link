<div>
    {{-- Error & Success Messages --}}
    @if($errorMessage)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errorMessage }}
            <button type="button" class="btn-close" wire:click="$set('errorMessage', null)" aria-label="Close"></button>
        </div>
    @endif

    @if($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $successMessage }}
            <button type="button" class="btn-close" wire:click="$set('successMessage', null)" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Left Column: Upload & File List --}}
        <div class="col-md-4">
            {{-- Upload Section --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📤 Upload Spreadsheet</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="uploadAndAnalyze">
                        <div class="mb-3">
                            <label for="spreadsheetFile" class="form-label">Pilih File</label>
                            <input
                                type="file"
                                class="form-control @error('spreadsheetFile') is-invalid @enderror"
                                id="spreadsheetFile"
                                wire:model="spreadsheetFile"
                                accept=".xlsx,.xls,.csv,.ods"
                            >
                            @error('spreadsheetFile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: Excel (.xlsx, .xls), CSV, ODS. Maks: 10MB</div>
                        </div>

                        <div wire:loading wire:target="spreadsheetFile" class="text-muted mb-3">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            Mengupload file...
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            wire:loading.attr="disabled"
                            wire:target="uploadAndAnalyze"
                            @if($isAnalyzing) disabled @endif
                        >
                            @if($isAnalyzing)
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Menganalisis...
                            @else
                                🔍 Upload & Analisis
                            @endif
                        </button>
                    </form>
                </div>
            </div>

            {{-- Uploaded Files List --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📁 File Tersimpan</h5>
                </div>
                <div class="card-body p-0">
                    @if($uploadedFiles->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($uploadedFiles as $file)
                                <div class="list-group-item {{ $selectedUpload && $selectedUpload->id === $file->id ? 'active' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <h6 class="mb-1 text-truncate" title="{{ $file->original_filename }}">
                                                {{ $file->original_filename }}
                                            </h6>
                                            <small class="{{ $selectedUpload && $selectedUpload->id === $file->id ? 'text-white-50' : 'text-muted' }}">
                                                {{ $file->getFileTypeLabel() }} • {{ $file->formatted_file_size }}
                                                <br>
                                                📅 {{ $file->created_at->format('d M Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button
                                                class="btn btn-sm {{ $selectedUpload && $selectedUpload->id === $file->id ? 'btn-light' : 'btn-outline-primary' }}"
                                                wire:click="viewAnalysis({{ $file->id }})"
                                                title="Lihat Analisis"
                                            >
                                                👁️
                                            </button>
                                            <button
                                                class="btn btn-sm {{ $selectedUpload && $selectedUpload->id === $file->id ? 'btn-light' : 'btn-outline-success' }}"
                                                wire:click="downloadFile({{ $file->id }})"
                                                title="Download"
                                            >
                                                ⬇️
                                            </button>
                                            <button
                                                class="btn btn-sm {{ $selectedUpload && $selectedUpload->id === $file->id ? 'btn-light' : 'btn-outline-danger' }}"
                                                wire:click="deleteFile({{ $file->id }})"
                                                wire:confirm="Apakah Anda yakin ingin menghapus file ini?"
                                                title="Hapus"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <div class="display-4 mb-2">📊</div>
                            <p class="mb-0">Belum ada file yang diupload.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Analysis Result --}}
        <div class="col-md-8">
            @if($analysisResult)
                {{-- Analysis Header --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📊 Hasil Analisis</h5>
                        <button class="btn btn-sm btn-light" wire:click="closeAnalysis">✕ Tutup</button>
                    </div>
                    <div class="card-body">
                        @if($selectedUpload)
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <h6 class="fw-bold">{{ $selectedUpload->original_filename }}</h6>
                                    <small class="text-muted">
                                        Dianalisis: {{ $selectedUpload->analyzed_at ? $selectedUpload->analyzed_at->format('d M Y H:i') : 'Belum' }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary" wire:click="reanalyze({{ $selectedUpload->id }})">
                                        🔄 Analisis Ulang
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 1. File Summary --}}
                @if(isset($analysisResult['ringkasan_file']))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📋 1. Ringkasan File</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">{{ $analysisResult['ringkasan_file']['deskripsi'] ?? '' }}</p>
                            @if(isset($analysisResult['ringkasan_file']['nama_sheet']))
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($analysisResult['ringkasan_file']['nama_sheet'] as $sheetName)
                                        <span class="badge bg-primary">{{ $sheetName }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 2. Sheet Details --}}
                @if(isset($analysisResult['sheet_details']))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📑 2. Detail Per Sheet</h5>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="sheetAccordion">
                                @foreach($analysisResult['sheet_details'] as $sheetName => $sheetData)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#sheet{{ $loop->index }}">
                                                <strong>{{ $sheetName }}</strong>
                                                <span class="badge bg-secondary ms-2">{{ $sheetData['jumlah_baris'] ?? 0 }} baris</span>
                                            </button>
                                        </h2>
                                        <div id="sheet{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#sheetAccordion">
                                            <div class="accordion-body">
                                                {{-- Business Function --}}
                                                @if(isset($sheetData['fungsi_bisnis']))
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold">🎯 Fungsi Bisnis:</h6>
                                                        @foreach($sheetData['fungsi_bisnis'] as $func)
                                                            <div class="alert alert-info py-2 mb-2">
                                                                <strong>{{ $func['icon'] ?? '📌' }} {{ $func['fungsi'] ?? '' }}</strong>
                                                                <p class="mb-0 small">{{ $func['deskripsi'] ?? '' }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- Database Table Suggestion --}}
                                                @if(isset($sheetData['tabel_database_terkait']))
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold">💾 Tabel Database Terkait:</h6>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($sheetData['tabel_database_terkait'] as $table)
                                                                <span class="badge bg-secondary">{{ $table }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Column Details --}}
                                                @if(isset($sheetData['detail_kolom']))
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold">📝 Penjelasan Kolom (untuk Admin Non-Teknis):</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Kolom</th>
                                                                        <th>Tipe Data</th>
                                                                        <th>Penjelasan</th>
                                                                        <th>% Kosong</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($sheetData['detail_kolom'] as $col)
                                                                        <tr>
                                                                            <td><strong>{{ $col['nama_kolom'] ?? '-' }}</strong></td>
                                                                            <td>
                                                                                @php
                                                                                    $badgeColors = [
                                                                                        'mata_uang' => 'success',
                                                                                        'tanggal' => 'info',
                                                                                        'angka' => 'warning',
                                                                                    ];
                                                                                    $badgeColor = $badgeColors[$col['tipe_data']] ?? 'secondary';
                                                                                @endphp
                                                                                <span class="badge bg-{{ $badgeColor }}">
                                                                                    {{ ucfirst(str_replace('_', ' ', $col['tipe_data'] ?? 'teks')) }}
                                                                                </span>
                                                                            </td>
                                                                            <td>{{ $col['penjelasan'] ?? '-' }}</td>
                                                                            <td>
                                                                                @if(($col['persentase_kosong'] ?? 0) > 50)
                                                                                    <span class="text-danger">{{ $col['persentase_kosong'] ?? 0 }}%</span>
                                                                                @elseif(($col['persentase_kosong'] ?? 0) > 20)
                                                                                    <span class="text-warning">{{ $col['persentase_kosong'] ?? 0 }}%</span>
                                                                                @else
                                                                                    <span class="text-success">{{ $col['persentase_kosong'] ?? 0 }}%</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 3. Sheet Relations --}}
                @if(isset($analysisResult['relasi_antar_sheet']))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">🔗 3. Relasi Antar Sheet</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($analysisResult['relasi_antar_sheet']['info']))
                                <p class="text-muted mb-0">{{ $analysisResult['relasi_antar_sheet']['info'] }}</p>
                            @else
                                @foreach($analysisResult['relasi_antar_sheet'] as $relation)
                                    <div class="alert alert-secondary mb-2">
                                        <strong>{{ $relation['sheet1'] ?? '' }}</strong> ↔️ <strong>{{ $relation['sheet2'] ?? '' }}</strong>
                                        <p class="mb-0 small">{{ $relation['deskripsi'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 4. Data Problems --}}
                @if(isset($analysisResult['masalah_data']))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">⚠️ 4. Potensi Masalah Data</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($analysisResult['masalah_data']['info']))
                                <div class="alert alert-success mb-0">
                                    {{ $analysisResult['masalah_data']['info'] }}
                                </div>
                            @else
                                @foreach($analysisResult['masalah_data'] as $sheetName => $problems)
                                    <h6 class="fw-bold mt-3">📄 {{ $sheetName }}</h6>
                                    @foreach($problems as $problem)
                                        <div class="alert alert-warning mb-2">
                                            <strong>{{ $problem['icon'] ?? '⚠️' }} {{ $problem['jenis'] ?? '' }}</strong>
                                            @if(isset($problem['detail']))
                                                <ul class="mb-1 small">
                                                    @foreach(array_slice($problem['detail'], 0, 3) as $detail)
                                                        <li>
                                                            @if(isset($detail['kolom']))
                                                                Kolom "{{ $detail['kolom'] }}"
                                                                @if(isset($detail['persentase_kosong']))
                                                                    : {{ $detail['persentase_kosong'] }}% kosong
                                                                @endif
                                                                @if(isset($detail['baris']))
                                                                    (Baris {{ $detail['baris'] }})
                                                                @endif
                                                                @if(isset($detail['nilai']))
                                                                    = {{ $detail['nilai'] }}
                                                                @endif
                                                            @elseif(isset($detail['baris_asli']))
                                                                Baris {{ $detail['baris_asli'] }} & {{ $detail['baris_duplikat'] }} identik
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            @if(isset($problem['dampak_bisnis']))
                                                <p class="mb-0 small text-danger"><strong>Dampak:</strong> {{ $problem['dampak_bisnis'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 5. Business Insights --}}
                @if(isset($analysisResult['insight_bisnis']))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">💡 5. Insight Bisnis Awal</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($analysisResult['insight_bisnis']['info']))
                                <p class="text-muted mb-0">{{ $analysisResult['insight_bisnis']['info'] }}</p>
                            @else
                                @foreach($analysisResult['insight_bisnis'] as $sheetName => $insights)
                                    <h6 class="fw-bold mt-3">📄 {{ $sheetName }}</h6>
                                    <div class="row">
                                        @foreach($insights as $insight)
                                            <div class="col-md-6 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex align-items-center">
                                                            <span class="h4 mb-0 me-2">{{ $insight['icon'] ?? '📊' }}</span>
                                                            <div>
                                                                <small class="text-muted">{{ $insight['judul'] ?? '' }}</small>
                                                                <div class="fw-bold">{{ $insight['nilai'] ?? '-' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 6. Recommendations --}}
                @if(isset($analysisResult['rekomendasi']))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">✅ 6. Rekomendasi Perbaikan</h5>
                        </div>
                        <div class="card-body">
                            @foreach($analysisResult['rekomendasi'] as $section => $recs)
                                <h6 class="fw-bold mt-3">
                                    @if($section === '_umum')
                                        🌐 Rekomendasi Umum
                                    @else
                                        📄 {{ $section }}
                                    @endif
                                </h6>
                                @foreach($recs as $rec)
                                    <div class="alert alert-light border mb-2">
                                        <div class="d-flex">
                                            <span class="h4 mb-0 me-3">{{ $rec['icon'] ?? '💡' }}</span>
                                            <div>
                                                <strong>{{ $rec['kategori'] ?? '' }}</strong>
                                                @if(isset($rec['masalah']))
                                                    <p class="mb-1 small text-danger">❌ {{ $rec['masalah'] }}</p>
                                                @endif
                                                <p class="mb-1 small">✨ {{ $rec['saran'] ?? '' }}</p>
                                                @if(isset($rec['manfaat']))
                                                    <p class="mb-0 small text-success">✅ {{ $rec['manfaat'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif

            @else
                {{-- Empty State --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="display-1 mb-4">📊</div>
                        <h4 class="fw-bold mb-3">Analisis Spreadsheet</h4>
                        <p class="text-muted mb-4">
                            Upload file spreadsheet (Excel, CSV, atau ODS) untuk mendapatkan analisis bisnis otomatis.
                            <br>Sistem akan menganalisis struktur data, mendeteksi masalah, dan memberikan insight bisnis.
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="row text-start">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="me-2">📋</span>
                                            <div>
                                                <strong>Analisis Struktur</strong>
                                                <p class="small text-muted mb-0">Identifikasi sheet, kolom, dan tipe data</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="me-2">🎯</span>
                                            <div>
                                                <strong>Fungsi Bisnis</strong>
                                                <p class="small text-muted mb-0">Deteksi tujuan setiap sheet</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="me-2">⚠️</span>
                                            <div>
                                                <strong>Deteksi Masalah</strong>
                                                <p class="small text-muted mb-0">Temukan data yang bermasalah</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="me-2">💡</span>
                                            <div>
                                                <strong>Insight Bisnis</strong>
                                                <p class="small text-muted mb-0">Ringkasan dan analisis awal</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
