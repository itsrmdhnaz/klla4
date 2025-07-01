# Setup Google Sheets Analytics

## Langkah-langkah Setup:

### 1. Konfigurasi Google Sheets API

1. Buat project di Google Cloud Console
2. Enable Google Sheets API
3. Buat Service Account dan download JSON credentials
4. Simpan file credentials JSON di folder `storage/app/private/` dengan nama `klla5.json`

### 2. Konfigurasi Environment

Tambahkan ke file `.env`:
```
GOOGLE_SHEETS_SPREADSHEET_ID=your_google_sheets_id_here
GOOGLE_SHEETS_CREDENTIALS_FILE=klla5.json
```

### 3. Share Google Sheets

Share Google Sheets Anda dengan email service account yang ada di file credentials JSON dengan permission "Viewer".

### 4. Format Data di Google Sheets

Pastikan sheet bernama "Inputan RAW Data Lead" dengan format:
- Column A: Timestamp/Tanggal
- Column E: Model
- Column F: Payment Method (Cash/Credit)
- Column G: Program
- Column I: Status

### 5. Install Dependencies

```bash
composer install
npm install
```

### 6. Jalankan Aplikasi

```bash
php artisan serve
```

## Fitur:

1. **Payment Method Chart (Pie Chart 1)**: Menampilkan persentase Cash vs Credit dari column F
2. **Program Chart (Pie Chart 2)**: Menampilkan distribusi program dari column G  
3. **Model Chart (Pie Chart 3)**: Menampilkan distribusi model dari column E
4. **Status Over Time (Line Chart)**: Menampilkan status dari column I berdasarkan waktu

## API Endpoints:

- `/api/analytics/payment-method?start_date=2024-01-01&end_date=2024-12-31`
- `/api/analytics/program?start_date=2024-01-01&end_date=2024-12-31`
- `/api/analytics/model?start_date=2024-01-01&end_date=2024-12-31`
- `/api/analytics/status?start_date=2024-01-01&end_date=2024-12-31`

Semua endpoint mendukung filter tanggal opsional.
