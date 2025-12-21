# Panduan Penggunaan Fitur Tracking Keuangan

## Untuk Admin Toko (UMKM)

### 1. Download Template Excel

1. Login sebagai Admin Toko
2. Buka menu "Spreadsheet Analyzer" 
3. Klik tombol "Download Template" di dashboard
4. Template akan terdownload dengan nama: `Template_Produk_[Nama_UMKM]_[Tanggal].xlsx`

### 2. Isi Data di Excel

Template memiliki **2 sheet**:

#### Sheet 1: Template Produk
Sheet ini untuk mengelola data produk/jasa (seperti biasa).

#### Sheet 2: Pemasukan & Pengeluaran (BARU!)
Sheet ini untuk mencatat transaksi keuangan dengan struktur:

| No | Tanggal | Jenis Transaksi | Keterangan | Nominal (Rp) |
|----|---------|----------------|------------|--------------|
| 1 | 15/01/2024 | Pemasukan | Penjualan Produk A | 500000 |
| 2 | 16/01/2024 | Pengeluaran | Beli Bahan Baku | 200000 |
| 3 | 17/01/2024 | Pemasukan | Penjualan Produk B | 750000 |

**Petunjuk Pengisian:**

- **No**: Nomor urut (1, 2, 3, ...)
- **Tanggal**: Format bebas (contoh: 15/01/2024, 2024-01-15, 15-01-2024)
- **Jenis Transaksi**: Isi dengan:
  - "Pemasukan" atau "Income" untuk uang masuk
  - "Pengeluaran" atau "Expense" untuk uang keluar
- **Keterangan**: Deskripsi transaksi (contoh: "Penjualan Produk A", "Bayar Listrik")
- **Nominal**: Jumlah uang (bisa pakai format: 500000 atau Rp 500.000)

### 3. Upload File ke Sistem

Ada 2 cara upload:

#### A. Upload 1 File (Untuk Analisis)
- Gunakan form "Upload Spreadsheet" (biru)
- Pilih 1 file
- Klik "Upload & Analisis"
- Sistem akan menganalisis struktur data

#### B. Upload Banyak File (Untuk Data Keuangan)
- Gunakan form "Upload Data Keuangan" (hijau)
- Pilih 1 atau lebih file sekaligus
- Klik "Upload & Proses Data Keuangan"
- **Sistem akan menggabungkan data dari semua file!**

### 4. Lihat Dashboard Keuangan

Setelah upload berhasil, Anda akan melihat **5 Ringkasan Utama**:

1. **💎 Total Nilai Aset**
   - Nilai total kekayaan UMKM
   - = Nilai Barang Stok + Saldo Bersih

2. **📦 Total Nilai Barang Stok**
   - Nilai semua produk yang ada di stok
   - = Σ(Harga Produk × Jumlah Stok)

3. **💰 Saldo Bersih**
   - Selisih antara pemasukan dan pengeluaran
   - = Total Pemasukan - Total Pengeluaran
   - Warna hijau = positif, merah = negatif

4. **📈 Total Pemasukan**
   - Jumlah semua uang yang masuk
   - Dari semua transaksi bertipe "Pemasukan"

5. **📉 Total Pengeluaran**
   - Jumlah semua uang yang keluar
   - Dari semua transaksi bertipe "Pengeluaran"

### 5. Lihat Tren Bulanan

Di bawah ringkasan, ada tabel yang menampilkan:
- Pemasukan per bulan (6 bulan terakhir)
- Pengeluaran per bulan
- Saldo per bulan

Ini membantu Anda melihat perkembangan keuangan dari waktu ke waktu.

## Fitur Error Handling

### Sistem Tidak Akan Berhenti Meskipun Ada Error!

Jika ada data yang salah format, sistem akan:
1. ✅ Tetap mengimpor data tersebut
2. ⚠️ Menandai bahwa ada kesalahan
3. 📝 Menyimpan catatan error
4. 💡 Memberikan notifikasi ke Anda

**Contoh kesalahan yang bisa ditoleransi:**
- Tanggal dengan format tidak standar
- Jenis transaksi salah ketik
- Nominal tidak valid
- Data kosong di beberapa baris

Setelah upload, Anda akan melihat ringkasan:
```
✅ Berhasil memproses 45 transaksi dari 3 file. 
⚠️ Terdapat 5 baris dengan kesalahan yang tetap diimpor.
```

## Menggabungkan Data dari Banyak File

### Skenario Penggunaan:

**Situasi:** Anda punya data keuangan di 3 file berbeda:
- `Januari-2024.xlsx`
- `Februari-2024.xlsx`
- `Maret-2024.xlsx`

**Solusi:**
1. Pilih ketiga file sekaligus saat upload
2. Klik "Upload & Proses Data Keuangan"
3. Sistem akan:
   - Membaca semua file
   - Menggabungkan data transaksi
   - Menampilkan overview gabungan
   - Menyimpan ke database

**Keuntungan:**
- Tidak perlu copy-paste manual
- Data tetap terorganisir per file
- Bisa tracking dari file mana data berasal
- Overview otomatis dari semua data

## Tips & Trik

### 1. Format Tanggal Fleksibel
Sistem mendukung berbagai format:
- ✅ 15/01/2024
- ✅ 2024-01-15
- ✅ 15-01-2024
- ✅ 01/15/2024

### 2. Format Nominal Fleksibel
Sistem bisa membaca:
- ✅ 500000
- ✅ 500.000
- ✅ Rp 500.000
- ✅ Rp. 500.000,-

### 3. Jenis Transaksi Fleksibel
Untuk **Pemasukan**, bisa pakai:
- Pemasukan
- Income
- Masuk
- Pendapatan

Untuk **Pengeluaran**, bisa pakai:
- Pengeluaran
- Expense
- Keluar
- Biaya

### 4. Sheet Tidak Harus Bernama Exact
Sistem akan mencari sheet yang mengandung kata kunci:
- "Pemasukan"
- "Pengeluaran"
- "Income"
- "Expense"
- "Transaksi"
- "Keuangan"

Jika tidak ada, akan menggunakan **sheet kedua** secara otomatis.

## Troubleshooting

### Q: Data saya tidak muncul di dashboard?
A: Pastikan:
- Sheet bernama "Pemasukan & Pengeluaran" atau menggunakan sheet kedua
- Header ada di baris 1 atau 4
- Ada kolom: Tanggal, Jenis Transaksi, Nominal

### Q: Nominal tidak terbaca dengan benar?
A: 
- Hindari mencampur angka dengan teks (contoh: ❌ "500 ribu")
- Gunakan format angka di Excel
- Atau tulis langsung: 500000

### Q: Tanggal error terus?
A: 
- Gunakan format Excel Date
- Atau format DD/MM/YYYY atau YYYY-MM-DD
- Pastikan tidak ada teks tambahan

### Q: File terlalu besar?
A: 
- Maksimal 10MB per file
- Split data ke beberapa file jika perlu
- Upload sekaligus menggunakan multiple file upload

## Kontak Support

Jika ada masalah atau pertanyaan:
1. Cek log error di sistem
2. Screenshot error yang muncul
3. Hubungi admin platform

---

**Selamat menggunakan fitur tracking keuangan! 📊💰**
