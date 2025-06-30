<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Master Data Sheet Configuration') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Konfigurasi Sheet per Spreadsheet</h3>
                            <p class="text-sm text-gray-600 mt-1">Kelola sheet/tab yang ada dalam setiap spreadsheet dan mapping ke cabang</p>
                        </div>
                        <div class="flex gap-2">
                            <!-- Filter Spreadsheet -->
                            <select id="spreadsheetFilter" class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                                <option value="">Semua Spreadsheet</option>
                            </select>
                            
                            <button type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                data-modal-target="sheet-modal" data-modal-toggle="sheet-modal"
                                onclick="showCreateModal()">
                                <i class="fas fa-plus mr-2"></i>Tambah Sheet
                            </button>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="w-full overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="sheetTable" class="table table-striped table-bordered w-full">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Spreadsheet</th>
                                        <th>Nama Sheet</th>
                                        <th>Display Name</th>
                                        <th>Cabang</th>
                                        <th class="text-center">Jumlah Kolom</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded here by DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="sheet-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="modalTitle" class="text-xl font-medium text-black">
                        Tambah Sheet Configuration
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="sheet-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <form id="sheetForm" class="space-y-4">
                        <div>
                            <label for="spreadsheet_id" class="block mb-2 text-sm font-medium text-black">Spreadsheet *</label>
                            <select id="spreadsheet_id" name="spreadsheet_id" required>
                                <option value="">Pilih Spreadsheet</option>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="sheet_name" class="block mb-2 text-sm font-medium text-black">Nama Sheet *</label>
                            <select id="sheet_name" name="sheet_name" required>
                                <option value="">Pilih atau ketik nama sheet</option>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                            <p class="text-sm text-gray-500 mt-1">Pilih dari cabang yang ada atau ketik nama sheet baru</p>
                        </div>

                        <div>
                            <label for="sheet_display_name" class="block mb-2 text-sm font-medium text-black">Display Name</label>
                            <input type="text" id="sheet_display_name" name="sheet_display_name"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Nama yang akan ditampilkan (opsional)">
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="branch_id" class="block mb-2 text-sm font-medium text-black">Cabang</label>
                            <select id="branch_id" name="branch_id">
                                <option value="">Pilih Cabang (Opsional)</option>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                            <p class="text-sm text-gray-500 mt-1">Mapping sheet ke cabang tertentu</p>
                        </div>

                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-black">Deskripsi</label>
                            <textarea id="description" name="description" rows="3"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Deskripsi sheet ini (opsional)"></textarea>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" onclick="saveSheet()"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan
                    </button>
                    <button type="button" data-modal-hide="sheet-modal"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-black focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let dataTable;
            let isEditMode = false;
            let editingId = null;
            let modal;

            $(document).ready(function() {
                // Initialize modal
                const $targetEl = document.getElementById('sheet-modal');
                const options = {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        $('#sheetForm')[0].reset();
                        AdminUtils.clearValidationErrors('#sheetForm');
                        setTimeout(() => {
                            const backdrops = document.querySelectorAll('[modal-backdrop]');
                            backdrops.forEach(backdrop => backdrop.remove());
                        }, 100);
                    },
                    onShow: () => {
                        setTimeout(() => {
                            $('#spreadsheet_id').focus();
                        }, 100);
                    }
                };
                
                modal = new Modal($targetEl, options);

                // Initialize Select2
                initializeSelect2();
                loadData();

                // Initialize DataTable
                dataTable = $('#sheetTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('admin.master-data.spreadsheet-sheet.data') }}',
                        type: 'GET',
                        data: function(d) {
                            d.spreadsheet_id = $('#spreadsheetFilter').val();
                        },
                        error: function(xhr, error, thrown) {
                            AdminUtils.handleAjaxError(xhr, error, thrown);
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'spreadsheet_name', name: 'spreadsheet.name' },
                        { data: 'sheet_name', name: 'sheet_name' },
                        { data: 'sheet_display_name', name: 'sheet_display_name' },
                        { data: 'branch_name', name: 'branch.branch_name', orderable: false },
                        { data: 'columns_count', name: 'columns_count', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'status', name: 'is_active', orderable: false, className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    language: window.dataTableLanguage,
                    dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>' +
                         '<"row"<"col-12"tr>>' +
                         '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
                    drawCallback: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });

                // Filter change event
                $('#spreadsheetFilter').on('change', function() {
                    dataTable.ajax.reload();
                });
            });

            function initializeSelect2() {
                AdminUtils.initSelect2('#spreadsheet_id', {
                    placeholder: 'Pilih spreadsheet...',
                    dropdownParent: $('#sheet-modal')
                });
                
                AdminUtils.initSelect2('#branch_id', {
                    placeholder: 'Pilih cabang...',
                    dropdownParent: $('#sheet-modal')
                });

                // Initialize sheet_name with tags and branch data
                AdminUtils.initSelect2('#sheet_name', {
                    placeholder: 'Pilih atau ketik nama sheet...',
                    dropdownParent: $('#sheet-modal'),
                    tags: true,
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        
                        if (term === '') {
                            return null;
                        }
                        
                        return {
                            id: term,
                            text: term + ' (Sheet Baru)',
                            newTag: true
                        };
                    },
                    templateResult: function(data) {
                        if (data.newTag) {
                            return $('<span class="text-blue-600"><i class="fas fa-plus mr-1"></i>' + data.text + '</span>');
                        }
                        return data.text;
                    }
                });
            }

            function loadData() {
                // Load spreadsheets
                $.ajax({
                    url: '{{ route('admin.master-data.spreadsheet-sheet.spreadsheets') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Spreadsheets response:', response); // Debug log
                        if (response.success) {
                            // Populate modal select
                            $('#spreadsheet_id').empty().append('<option value="">Pilih Spreadsheet</option>');
                            
                            // Populate filter select
                            $('#spreadsheetFilter').empty().append('<option value="">Semua Spreadsheet</option>');
                            
                            if (response.data && response.data.length > 0) {
                                $.each(response.data, function(index, spreadsheet) {
                                    $('#spreadsheet_id').append(new Option(spreadsheet.name, spreadsheet.id));
                                    $('#spreadsheetFilter').append(new Option(spreadsheet.name, spreadsheet.id));
                                });
                            } else {
                                console.warn('No spreadsheets found');
                                Swal.fire('Info', 'Belum ada data spreadsheet. Silakan tambahkan spreadsheet terlebih dahulu.', 'info');
                            }
                            
                            $('#spreadsheet_id').trigger('change');
                            $('#spreadsheetFilter').trigger('change');
                        } else {
                            console.error('Failed to load spreadsheets:', response.message);
                            Swal.fire('Error', response.message || 'Gagal memuat data spreadsheet', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error loading spreadsheets:', xhr.responseJSON);
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });

                // Load branches for sheet name options and branch selector
                $.ajax({
                    url: '{{ route('admin.master-data.spreadsheet-sheet.branches') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Branches response:', response); // Debug log
                        if (response.success) {
                            // Populate branch selector
                            $('#branch_id').empty().append('<option value="">Pilih Cabang (Opsional)</option>');
                            
                            // Populate sheet name selector with branch names
                            $('#sheet_name').empty().append('<option value="">Pilih atau ketik nama sheet</option>');
                            
                            if (response.data && response.data.length > 0) {
                                $.each(response.data, function(index, branch) {
                                    // Add to branch selector
                                    $('#branch_id').append(new Option(branch.branch_name, branch.id_branch));
                                    
                                    // Add to sheet name selector as predefined options
                                    $('#sheet_name').append(new Option(branch.branch_name, branch.branch_name));
                                });
                            } else {
                                console.warn('No branches found');
                            }
                            
                            $('#branch_id').trigger('change');
                            $('#sheet_name').trigger('change');
                        } else {
                            console.error('Failed to load branches:', response.message);
                            Swal.fire('Error', response.message || 'Gagal memuat data cabang', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error loading branches:', xhr.responseJSON);
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function showCreateModal() {
                isEditMode = false;
                editingId = null;
                $('#modalTitle').text('Tambah Sheet Configuration');
                $('#sheetForm')[0].reset();
                AdminUtils.clearValidationErrors('#sheetForm');
                
                // Reset Select2 values
                $('#spreadsheet_id').val(null).trigger('change');
                $('#sheet_name').val(null).trigger('change');
                $('#branch_id').val(null).trigger('change');
                
                const existingBackdrops = document.querySelectorAll('[modal-backdrop]');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                
                modal.show();
            }

            function editSheet(id) {
                isEditMode = true;
                editingId = id;
                $('#modalTitle').text('Edit Sheet Configuration');

                $.ajax({
                    url: '{{ route('admin.master-data.spreadsheet-sheet.show', ['id' => '__ID__']) }}'.replace('__ID__', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const sheet = response.data;
                            $('#spreadsheet_id').val(sheet.spreadsheet_id).trigger('change');
                            
                            // Set sheet name - check if it exists in options first
                            var sheetNameExists = $('#sheet_name option[value="' + sheet.sheet_name + '"]').length > 0;
                            if (!sheetNameExists) {
                                // Add as new option if it doesn't exist
                                var newOption = new Option(sheet.sheet_name, sheet.sheet_name, true, true);
                                $('#sheet_name').append(newOption);
                            } else {
                                $('#sheet_name').val(sheet.sheet_name);
                            }
                            $('#sheet_name').trigger('change');
                            
                            $('#sheet_display_name').val(sheet.sheet_display_name);
                            $('#branch_id').val(sheet.branch_id).trigger('change');
                            $('#description').val(sheet.description);

                            AdminUtils.clearValidationErrors('#sheetForm');
                            
                            const existingBackdrops = document.querySelectorAll('[modal-backdrop]');
                            existingBackdrops.forEach(backdrop => backdrop.remove());
                            
                            modal.show();
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            // Auto-fill branch when sheet name matches a branch name
            $(document).on('change', '#sheet_name', function() {
                var selectedSheetName = $(this).val();
                if (selectedSheetName) {
                    // Check if sheet name matches any branch name
                    var matchingBranchOption = $('#branch_id option').filter(function() {
                        return $(this).text() === selectedSheetName;
                    });
                    
                    if (matchingBranchOption.length > 0) {
                        $('#branch_id').val(matchingBranchOption.val()).trigger('change');
                        
                        // Auto-fill display name if empty
                        if (!$('#sheet_display_name').val()) {
                            $('#sheet_display_name').val('Cabang ' + selectedSheetName);
                        }
                    }
                }
            });

            function saveSheet() {
                const formData = $('#sheetForm').serialize();
                
                const url = isEditMode ? 
                    '{{ route('admin.master-data.spreadsheet-sheet.update', ['id' => '__ID__']) }}'.replace('__ID__', editingId) : 
                    '{{ route('admin.master-data.spreadsheet-sheet.store') }}';

                $.ajax({
                    url: url,
                    type: isEditMode ? 'PUT' : 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            modal.hide();
                            dataTable.ajax.reload(null, false);
                        } else {
                            if (response.errors) {
                                AdminUtils.showValidationErrors(response.errors, '#sheetForm');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        const errors = AdminUtils.handleAjaxError(xhr, status, error);
                        if (errors) {
                            AdminUtils.showValidationErrors(errors, '#sheetForm');
                        }
                    }
                });
            }

            function deleteSheet(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Sheet configuration akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.master-data.spreadsheet-sheet.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id),
                            type: 'DELETE',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Terhapus!', response.message, 'success');
                                    dataTable.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                AdminUtils.handleAjaxError(xhr, status, error);
                            }
                        });
                    }
                });
            }

            // Handle modal close events
            $(document).on('click', '[data-modal-hide="sheet-modal"]', function() {
                modal.hide();
            });

            $(document).keydown(function(e) {
                if (e.key === "Escape") {
                    modal.hide();
                }
            });
        </script>
    @endpush
</x-admin-app-layout>
