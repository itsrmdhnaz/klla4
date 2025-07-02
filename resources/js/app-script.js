function doGet(e) {
    const sheet = SpreadsheetApp.openById('1q3XRRRIaaFotu67jqZPI2IzgXQa4J6pG6-zZGZC6Xco')
        .getSheetByName('Inputan RAW Data Lead');
    const range = e.parameter.range || 'A2:I';
    const startDate = e.parameter.startDate;
    const endDate = e.parameter.endDate;

    const values = sheet.getRange(range).getValues().filter(row => row[0]);
    const result = [];

    for (const row of values) {
        const rawTimestamp = row[0];

        // Format sebagai ISO: 'yyyy-MM-dd' (untuk Laravel)
        const formattedDate = Utilities.formatDate(new Date(rawTimestamp), 'Asia/Jakarta', 'yyyy-MM-dd');

        if (startDate && endDate) {
            if (formattedDate < startDate || formattedDate > endDate) continue;
        }

        // Overwrite tanggal kolom ke 0 agar konsisten dengan Laravel
        row[0] = formattedDate;

        result.push(row);
    }

    return ContentService
        .createTextOutput(JSON.stringify(result))
        .setMimeType(ContentService.MimeType.JSON);
}
