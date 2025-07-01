# Contoh Cara Filtering Berdasarkan Tanggal

## Data Mentah dari Google Sheets:
```
Row 2: [2024-06-01, "", "", "", "Model A", "Cash", "Program X", "", "New"]
Row 3: [2024-06-02, "", "", "", "Model B", "Credit", "Program Y", "", "Contacted"] 
Row 4: [2024-06-03, "", "", "", "Model A", "Cash", "Program X", "", "Qualified"]
Row 5: [2024-06-15, "", "", "", "Model C", "Credit", "Program Z", "", "Converted"]
```

## Jika User Pilih Tanggal: 2024-06-01 sampai 2024-06-03

### Langkah Filtering:
1. **Ambil semua data**: `A2:I1000` (1 API call)
2. **Filter berdasarkan Column A (index 0)**:
   ```php
   $start = Carbon::parse('2024-06-01')->startOfDay();
   $end = Carbon::parse('2024-06-03')->endOfDay();
   
   // Filter data
   $filteredData = array_filter($data, function ($row) use ($start, $end) {
       $rowDate = Carbon::parse($row[0]); // Column A
       return $rowDate->between($start, $end);
   });
   ```

3. **Hasil setelah filter**:
   ```
   Row 2: [2024-06-01, "", "", "", "Model A", "Cash", "Program X", "", "New"]
   Row 3: [2024-06-02, "", "", "", "Model B", "Credit", "Program Y", "", "Contacted"] 
   Row 4: [2024-06-03, "", "", "", "Model A", "Cash", "Program X", "", "Qualified"]
   ```

4. **Analisis Payment Method (Column F - index 5)**:
   - Cash: 2 data (66.7%)
   - Credit: 1 data (33.3%)

5. **Analisis Program (Column G - index 6)**:
   - Program X: 2 data
   - Program Y: 1 data

6. **Dan seterusnya...**

## Keuntungan Metode Ini:
✅ **Efisien**: Hanya 1 API call ke Google Sheets  
✅ **Fleksibel**: Bisa filter range tanggal apapun  
✅ **Cepat**: Filtering dilakukan di server  
✅ **Akurat**: Semua chart konsisten dengan filter tanggal yang sama  

## Jika Tidak Ada Filter Tanggal:
- Semua data ditampilkan (dari awal sampai akhir)
- User bisa lihat overview keseluruhan data
