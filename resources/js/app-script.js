function doGet(e) {
    // Handle parameter action untuk membedakan fungsi
    if (e.parameter.action === 'getSalesData') {
        return getSalesData(e);
    }

    const sheet = SpreadsheetApp.openById('1q3XRRRIaaFotu67jqZPI2IzgXQa4J6pG6-zZGZC6Xco')
        .getSheetByName('Inputan RAW Data Lead');
    const range = e.parameter.range || 'A2:I';
    const startDate = e.parameter.startDate;
    const endDate = e.parameter.endDate;
    const sales = e.parameter.sales; // Tambah parameter sales

    const values = sheet.getRange(range).getValues().filter(row => row[0]);
    const result = [];

    for (const row of values) {
        const rawTimestamp = row[0];

        // Format sebagai ISO: 'yyyy-MM-dd' (untuk Laravel)
        const formattedDate = Utilities.formatDate(new Date(rawTimestamp), 'Asia/Jakarta', 'yyyy-MM-dd');

        // Filter berdasarkan rentang tanggal
        if (startDate && endDate) {
            if (formattedDate < startDate || formattedDate > endDate) continue;
        }

        // Filter berdasarkan sales jika ada (column P = index 15)
        if (sales && sales.trim() !== '') {
            // Extend range jika perlu untuk mengambil column P
            if (row.length <= 15) {
                // Ambil row lengkap sampai column P
                const extendedRow = sheet.getRange(values.indexOf(row) + 2, 1, 1, 16).getValues()[0];
                if (!extendedRow[15] || extendedRow[15].toString().trim() !== sales.trim()) {
                    continue;
                }
            } else {
                if (!row[15] || row[15].toString().trim() !== sales.trim()) {
                    continue;
                }
            }
        }

        // Overwrite tanggal kolom ke 0 agar konsisten dengan Laravel
        row[0] = formattedDate;

        result.push(row);
    }

    return ContentService
        .createTextOutput(JSON.stringify(result))
        .setMimeType(ContentService.MimeType.JSON);
}

function getSalesData(e) {
    const sheet = SpreadsheetApp.openById('1q3XRRRIaaFotu67jqZPI2IzgXQa4J6pG6-zZGZC6Xco')
        .getSheetByName('Inputan RAW Data Lead');
    const startDate = e.parameter.startDate;
    const endDate = e.parameter.endDate;

    // Ambil data dari column A (tanggal) dan P (sales)
    const values = sheet.getRange('A2:P').getValues().filter(row => row[0] && row[15]); // row[15] = column P
    const salesSet = new Set();

    for (const row of values) {
        const rawTimestamp = row[0];
        const salesName = row[15]; // Column P

        // Format tanggal seperti di fungsi doGet
        const formattedDate = Utilities.formatDate(new Date(rawTimestamp), 'Asia/Jakarta', 'yyyy-MM-dd');

        // Filter berdasarkan rentang tanggal
        if (startDate && endDate) {
            if (formattedDate < startDate || formattedDate > endDate) continue;
        }

        // Tambahkan sales name ke Set untuk menghilangkan duplikat
        if (salesName && salesName.toString().trim() !== '') {
            salesSet.add(salesName.toString().trim());
        }
    }

    // Convert Set ke Array dan sort
    const salesList = Array.from(salesSet).sort();

    return ContentService
        .createTextOutput(JSON.stringify(salesList))
        .setMimeType(ContentService.MimeType.JSON);
}
