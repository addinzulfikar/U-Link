<div class="max-w-7xl mx-auto">
    {{-- Error & Success Messages - Xero-style: Minimal alerts --}}
    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
            <span class="text-sm">{{ $errorMessage }}</span>
            <button type="button" wire:click="$set('errorMessage', null)" class="text-red-600 hover:text-red-800">✕</button>
        </div>
    @endif

    @if($successMessage)
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
            <span class="text-sm">{{ $successMessage }}</span>
            <button type="button" wire:click="$set('successMessage', null)" class="text-green-600 hover:text-green-800">✕</button>
        </div>
    @endif

    {{-- Financial Snapshot - Xero-style: Hero section with BIG numbers --}}
    @if($financialOverview)
        <div class="mb-8">
            {{-- Main Financial KPIs - Numbers FIRST, labels small --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                {{-- Total Nilai Aset --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Total Nilai Aset</div>
                    <div class="text-4xl font-semibold text-gray-900">Rp {{ number_format($financialOverview['overview']['total_asset_value'], 0, ',', '.') }}</div>
                </div>
                
                {{-- Total Nilai Barang Stok --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Total Nilai Barang Stok</div>
                    <div class="text-4xl font-semibold text-gray-900">Rp {{ number_format($financialOverview['overview']['total_stock_value'], 0, ',', '.') }}</div>
                </div>
                
                {{-- Saldo Bersih --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Saldo Bersih</div>
                    <div class="text-4xl font-semibold text-gray-900">
                        Rp {{ number_format($financialOverview['overview']['net_balance'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-500 mt-2">{{ $financialOverview['overview']['net_balance'] >= 0 ? 'Positif' : 'Perlu perhatian' }}</div>
                </div>
            </div>

            {{-- Secondary KPIs - Income & Expenses --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Total Pemasukan --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Total Pemasukan</div>
                    <div class="text-2xl font-semibold text-gray-900">Rp {{ number_format($financialOverview['overview']['total_income'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $financialOverview['statistics']['income_count'] }} transaksi</div>
                </div>
                
                {{-- Total Pengeluaran --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Total Pengeluaran</div>
                    <div class="text-2xl font-semibold text-gray-900">Rp {{ number_format($financialOverview['overview']['total_expense'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $financialOverview['statistics']['expense_count'] }} transaksi</div>
                </div>
            </div>
            
            {{-- Monthly Trends - Xero-style: Chart would be here, table is secondary --}}
            @if(!empty($financialOverview['monthly_trends']))
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h6 class="text-base font-semibold text-gray-900 mb-4">Tren 6 Bulan Terakhir</h6>

                    <div class="mb-4" wire:ignore>
                        <div class="h-64">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pemasukan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pengeluaran</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($financialOverview['monthly_trends'] as $trend)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ date('M Y', strtotime($trend['month'])) }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900">Rp {{ number_format($trend['income'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900">Rp {{ number_format($trend['expense'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                                            Rp {{ number_format($trend['balance'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            {{-- Insight Section - Xero-style: Text-based insights --}}
            @if($financialOverview['statistics']['transactions_with_errors'] > 0)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-900 px-4 py-3 rounded-lg mt-4">
                    <p class="text-sm"><strong>Perhatian:</strong> Terdapat {{ $financialOverview['statistics']['transactions_with_errors'] }} transaksi dengan kesalahan validasi.</p>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Left Column: Upload & File List --}}
        <div class="md:col-span-1">
            {{-- Multiple Files Upload for Financial Data --}}
            <div class="bg-white border border-gray-200 rounded-lg mb-6">
                <div class="border-b border-gray-200 px-6 py-4 bg-green-50">
                    <h5 class="text-base font-semibold text-gray-900">Upload Data Keuangan</h5>
                </div>
                <div class="p-6">
                    <form wire:submit.prevent="uploadAndProcessFinancials">
                        <div class="mb-4">
                            <label for="spreadsheetFiles" class="block text-sm font-medium text-gray-700 mb-2">Pilih File (Bisa Banyak)</label>
                            <input
                                type="file"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-600 file:text-white hover:file:bg-green-700"
                                id="spreadsheetFiles"
                                wire:model="spreadsheetFiles"
                                accept=".xlsx,.xls,.csv,.ods"
                                multiple
                            >
                            @error('spreadsheetFiles.*')
                                <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-xs text-gray-500 mt-1">
                                Upload 1 atau lebih file. Pastikan file memiliki sheet "Pemasukan & Pengeluaran".
                            </div>
                        </div>

                        <div wire:loading wire:target="spreadsheetFiles" class="text-gray-500 text-sm mb-3">
                            Mengupload file...
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors text-sm disabled:opacity-50"
                            wire:loading.attr="disabled"
                            wire:target="uploadAndProcessFinancials"
                            @if($isProcessingFinancials) disabled @endif
                        >
                            @if($isProcessingFinancials)
                                Memproses...
                            @else
                                Upload & Proses Data Keuangan
                            @endif
                        </button>
                    </form>
                </div>
            </div>

            {{-- Uploaded Files List --}}
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h5 class="text-base font-semibold text-gray-900">File Tersimpan</h5>
                </div>
                <div>
                    @if($uploadedFiles->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($uploadedFiles as $file)
                                <div class="px-6 py-4 {{ $selectedUpload && $selectedUpload->id === $file->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="flex-1 min-w-0">
                                            <h6 class="text-sm font-medium text-gray-900 truncate mb-1" title="{{ $file->original_filename }}">
                                                {{ $file->original_filename }}
                                            </h6>
                                            <p class="text-xs text-gray-500">
                                                {{ $file->getFileTypeLabel() }} • {{ $file->formatted_file_size }}
                                                <br>
                                                {{ $file->created_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                class="text-primary hover:text-primary-dark text-xs"
                                                wire:click="viewAnalysis({{ $file->id }})"
                                                title="Lihat Analisis"
                                            >
                                                Lihat
                                            </button>
                                            <button
                                                class="text-gray-600 hover:text-gray-800 text-xs"
                                                wire:click="downloadFile({{ $file->id }})"
                                                title="Download"
                                            >
                                                Download
                                            </button>
                                            <button
                                                class="text-red-600 hover:text-red-800 text-xs"
                                                wire:click="deleteFile({{ $file->id }})"
                                                wire:confirm="Apakah Anda yakin ingin menghapus file ini?"
                                                title="Hapus"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="text-4xl mb-3">📊</div>
                            <p class="text-sm text-gray-500">Belum ada file yang diupload.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Analysis Result --}}
        <div class="md:col-span-2">
            @if($analysisResult && $selectedUpload)
                <div class="bg-white border border-gray-200 rounded-lg mb-6">
                    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center bg-green-50">
                        <h5 class="text-base font-semibold text-gray-900">Detail Upload</h5>
                        <button class="text-sm font-medium text-gray-600 hover:text-gray-900" wire:click="closeAnalysis">✕ Tutup</button>
                    </div>
                    <div class="p-6">
                        <h6 class="font-semibold text-gray-900">{{ $selectedUpload->original_filename }}</h6>
                        <p class="text-xs text-gray-500 mb-4">
                            Diproses: {{ $selectedUpload->analyzed_at ? $selectedUpload->analyzed_at->format('d M Y H:i') : 'Belum' }}
                        </p>

                        @if(($analysisResult['type'] ?? null) === 'financial_data')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Total File</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ $analysisResult['processing_stats']['total_files'] ?? '-' }}</div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Total Transaksi Diimpor</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ $analysisResult['processing_stats']['total_rows_imported'] ?? '-' }}</div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="text-xs text-gray-500 mb-1">Jumlah Error</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ is_array($analysisResult['errors'] ?? null) ? count($analysisResult['errors']) : 0 }}</div>
                                </div>
                            </div>

                            @if(!empty($analysisResult['errors']) && is_array($analysisResult['errors']))
                                <div class="mt-4">
                                    <h6 class="text-sm font-semibold text-gray-900 mb-2">Ringkasan Error (contoh)</h6>
                                    <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                                        @foreach(array_slice($analysisResult['errors'], 0, 5) as $err)
                                            <li>{{ is_string($err) ? $err : json_encode($err) }}</li>
                                        @endforeach
                                    </ul>
                                    <p class="text-xs text-gray-500 mt-2">Jika ada error, data tetap diproses sejauh memungkinkan.</p>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-600">Tidak ada ringkasan yang dapat ditampilkan untuk file ini.</p>
                        @endif
                    </div>
                </div>

            @else
                {{-- Empty State - Xero-style: Calm, informative --}}
                <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                    <div class="text-6xl mb-4">📊</div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-3">Upload Data Keuangan</h4>
                    <p class="text-sm text-gray-600 mb-0 max-w-2xl mx-auto">
                        Gunakan panel <strong>Upload Data Keuangan</strong> di sebelah kiri untuk mengimpor transaksi.
                        Setelah diproses, ringkasan keuangan akan tampil di bagian atas.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
