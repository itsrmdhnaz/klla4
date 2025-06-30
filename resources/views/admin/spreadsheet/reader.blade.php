<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            Spreadsheet Reader
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form id="spreadsheetReaderForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Spreadsheet</label>
                            <select id="reader_spreadsheet" class="w-full border-gray-300 rounded-lg select2">
                                <option value="">Select Spreadsheet</option>
                                @foreach($spreadsheets as $spreadsheet)
                                    <option value="{{ $spreadsheet->id }}">{{ $spreadsheet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Branch</label>
                            <select id="reader_branch" class="w-full border-gray-300 rounded-lg select2">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id_branch }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sheet</label>
                            <select id="reader_sheet" class="w-full border-gray-300 rounded-lg select2" disabled>
                                <option value="">Select Sheet</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Column</label>
                        <select id="reader_column" class="w-full border-gray-300 rounded-lg select2" disabled>
                            <option value="">Select Column</option>
                        </select>
                    </div>
                    <div>
                        <button type="button" id="btnReadData" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" disabled>
                            Read Data
                        </button>
                    </div>
                </form>
                <div id="reader_result" class="mt-6"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Select2 CDN (or use your asset pipeline) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            // Inisialisasi Select2 untuk semua select
            $('.select2').select2({
                width: '100%',
                theme: 'default'
            });

            // Re-init select2 jika select di-enable/disable/diganti option
            function reinitSelect2(selector) {
                $(selector).select2('destroy').select2({ width: '100%', theme: 'default' });
            }

            $('#reader_spreadsheet, #reader_branch').on('change', function() {
                const spreadsheetId = $('#reader_spreadsheet').val();
                const branchId = $('#reader_branch').val();
                $('#reader_sheet').empty().append('<option value="">Select Sheet</option>').prop('disabled', true);
                $('#reader_column').empty().append('<option value="">Select Column</option>').prop('disabled', true);
                $('#btnReadData').prop('disabled', true);
                reinitSelect2('#reader_sheet');
                reinitSelect2('#reader_column');
                if (spreadsheetId && branchId) {
                    $.get("{{ url('admin/spreadsheet/reader') }}/" + spreadsheetId + "/sheets?branch_id=" + branchId, function(res) {
                        if (res.success && res.data.length > 0) {
                            res.data.forEach(sheet => {
                                $('#reader_sheet').append(new Option(sheet.sheet_display_name || sheet.sheet_name, sheet.id));
                            });
                            $('#reader_sheet').prop('disabled', false);
                            reinitSelect2('#reader_sheet');
                        }
                    });
                }
            });

            $('#reader_sheet').on('change', function() {
                const sheetId = $(this).val();
                $('#reader_column').empty().append('<option value="">Select Column</option>').prop('disabled', true);
                $('#btnReadData').prop('disabled', true);
                reinitSelect2('#reader_column');
                if (sheetId) {
                    $.get("{{ url('admin/spreadsheet/reader/sheet') }}/" + sheetId + "/columns", function(res) {
                        if (res.success && res.data.length > 0) {
                            res.data.forEach(col => {
                                $('#reader_column').append(new Option(col.name + ' (' + col.range + ')', col.key));
                            });
                            $('#reader_column').prop('disabled', false);
                            reinitSelect2('#reader_column');
                        }
                    });
                }
            });

            $('#reader_column').on('change', function() {
                $('#btnReadData').prop('disabled', !$(this).val());
            });

            $('#btnReadData').on('click', function() {
                const spreadsheetId = $('#reader_spreadsheet').val();
                const branchId = $('#reader_branch').val();
                const sheetId = $('#reader_sheet').val();
                const columnKey = $('#reader_column').val();
                if (!spreadsheetId || !branchId || !sheetId || !columnKey) return;

                $.post("{{ route('admin.spreadsheet.reader.read') }}", {
                    spreadsheet_id: spreadsheetId,
                    branch_id: branchId,
                    sheet_id: sheetId,
                    column_key: columnKey,
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    if (res.success) {
                        $('#reader_result').html('<pre>' + JSON.stringify(res.data, null, 2) + '</pre>');
                    } else {
                        $('#reader_result').html('<div class="text-red-600">Failed to read data</div>');
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-app-layout>
