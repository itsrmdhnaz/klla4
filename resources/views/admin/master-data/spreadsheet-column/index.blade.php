<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Master Data Column Configuration') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Konfigurasi Kolom per Sheet</h3>
                            <p class="text-sm text-gray-600 mt-1">Kelola kolom yang bisa diakses di setiap sheet dengan range dan tipe datanya</p>
                        </div>
                        <div class="flex gap-2">
                            <!-- Filter Sheet -->
                            <select id="sheetFilter" class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                                <option value="">Semua Sheet</option>
                            </select>
                            
                            <button type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                data-modal-target="column-modal" data-modal-toggle="column-modal"
                                onclick="showCreateModal()">
                                <i class="fas fa-plus mr-2"></i>Tambah Kolom
                            </button>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="w-full overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="columnTable" class="table table-striped table-bordered w-full">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Spreadsheet</th>
                                        <th>Sheet</th>
                                        <th>Nama Kolom</th>
                                        <th>Column Key</th>
                                        <th>Range</th>
                                        <th class="text-center">Tipe Data</th>
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
    <div id="column-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="modalTitle" class="text-xl font-medium text-black">
                        Tambah Column Configuration
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="column-modal">
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
                    <form id="columnForm" class="space-y-4">
                        <div>
                            <label for="spreadsheet_sheet_id" class="block mb-2 text-sm font-medium text-black">Sheet *</label>
                            <select id="spreadsheet_sheet_id" name="spreadsheet_sheet_id" required>
                                <option value="">Pilih Sheet</option>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="column_key" class="block mb-2 text-sm font-medium text-black">Column Key *</label>
                                <input type="text" id="column_key" name="column_key"
                                    class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    placeholder="contoh: mpv_low, target_weekly"
                                    required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                <p class="text-sm text-gray-500 mt-1">Unique identifier untuk kolom (gunakan underscore)</p>
                            </div>

                            <div>
                                <label for="column_name" class="block mb-2 text-sm font-medium text-black">Nama Kolom *</label>
                                <input type="text" id="column_name" name="column_name"
                                    class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    placeholder="contoh: MPV Low, Target Weekly"
                                    required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="column_range" class="block mb-2 text-sm font-medium text-black">Range *</label>
                                <input type="text" id="column_range" name="column_range"
                                    class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    placeholder="contoh: D10, T10:T21"
                                    required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                <p class="text-sm text-gray-500 mt-1">Range di Google Sheets (contoh: D10 atau T10:T21)</p>
                            </div>

                            <div>
                                <label for="data_type" class="block mb-2 text-sm font-medium text-black">Tipe Data *</label>
                                <select id="data_type" name="data_type" required
                                    class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">Pilih Tipe Data</option>
                                    <option value="single">Single Cell (1 nilai)</option>
                                    <option value="range">Range (beberapa nilai berurutan)</option>
                                    <option value="array">Array (data dalam bentuk tabel)</option>
                                </select>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-black">Deskripsi</label>
                            <textarea id="description" name="description" rows="3"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Deskripsi kolom ini (opsional)"></textarea>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <!-- Advanced Settings (Collapsed by default) -->
                        <div class="border-t pt-4">
                            <button type="button" id="toggleAdvanced" class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-chevron-right mr-1" id="advancedIcon"></i>
                                Pengaturan Lanjutan
                            </button>
                            
                            <div id="advancedSettings" class="hidden mt-3 space-y-3">
                                <div>
                                    <label for="validation_rules" class="block mb-2 text-sm font-medium text-black">Validation Rules (JSON)</label>
                                    <textarea id="validation_rules" name="validation_rules" rows="3"
                                        class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 font-mono text-xs"
                                        placeholder='{"min": 0, "max": 100, "required": true}'></textarea>
                                    <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    <p class="text-sm text-gray-500 mt-1">Aturan validasi dalam format JSON (opsional)</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" onclick="saveColumn()"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan
                    </button>
                    <button type="button" data-modal-hide="column-modal"
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
                const $targetEl = document.getElementById('column-modal');
                const options = {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        $('#columnForm')[0].reset();
                        AdminUtils.clearValidationErrors('#columnForm');
                        $('#advancedSettings').addClass('hidden');
                        $('#advancedIcon').removeClass('fa-chevron-down').addClass('fa-chevron-right');
                        setTimeout(() => {
                            const backdrops = document.querySelectorAll('[modal-backdrop]');
                            backdrops.forEach(backdrop => backdrop.remove());
                        }, 100);
                    },
                    onShow: () => {
                        setTimeout(() => {
                            $('#spreadsheet_sheet_id').focus();
                        }, 100);
                    }
                };
                
                modal = new Modal($targetEl, options);

                // Initialize Select2
                initializeSelect2();
                loadData();

                // Initialize DataTable
                dataTable = $('#columnTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('admin.master-data.spreadsheet-column.data') }}',
                        type: 'GET',
                        data: function(d) {
                            d.sheet_id = $('#sheetFilter').val();
                        },
                        error: function(xhr, error, thrown) {
                            AdminUtils.handleAjaxError(xhr, error, thrown);
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'spreadsheet_name', name: 'sheet.spreadsheet.name' },
                        { data: 'sheet_name', name: 'sheet.sheet_name' },
                        { data: 'column_name', name: 'column_name' },
                        { data: 'column_key', name: 'column_key', className: 'font-mono text-sm' },
                        { data: 'column_range_display', name: 'column_range', orderable: false },
                        { data: 'data_type_badge', name: 'data_type', orderable: false, className: 'text-center' },
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
                $('#sheetFilter').on('change', function() {
                    dataTable.ajax.reload();
                });

                // Advanced settings toggle
                $('#toggleAdvanced').on('click', function() {
                    const $settings = $('#advancedSettings');
                    const $icon = $('#advancedIcon');
                    
                    if ($settings.hasClass('hidden')) {
                        $settings.removeClass('hidden');
                        $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                    } else {
                        $settings.addClass('hidden');
                        $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                    }
                });

                // Auto-generate column key from column name
                $('#column_name').on('input', function() {
                    const columnName = $(this).val();
                    const columnKey = columnName.toLowerCase()
                        .replace(/[^a-z0-9\s]/g, '')
                        .replace(/\s+/g, '_')
                        .trim();
                    
                    if (!isEditMode || !$('#column_key').val()) {
                        $('#column_key').val(columnKey);
                    }
                });
            });

            function initializeSelect2() {
                AdminUtils.initSelect2('#spreadsheet_sheet_id', {
                    placeholder: 'Pilih sheet...',
                    dropdownParent: $('#column-modal')
                });
            }

            function loadData() {
                // Load sheets
                $.ajax({
                    url: '{{ route('admin.master-data.spreadsheet-column.sheets') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Sheets response:', response);
                        if (response.success) {
                            // Populate modal select
                            $('#spreadsheet_sheet_id').empty().append('<option value="">Pilih Sheet</option>');
                            
                            // Populate filter select
                            $('#sheetFilter').empty().append('<option value="">Semua Sheet</option>');
                            
                            if (response.data && response.data.length > 0) {
                                $.each(response.data, function(index, sheet) {
                                    $('#spreadsheet_sheet_id').append(new Option(sheet.name, sheet.id));
                                    $('#sheetFilter').append(new Option(sheet.name, sheet.id));
                                });
                            } else {
                                console.warn('No sheets found');
                                Swal.fire('Info', 'Belum ada data sheet. Silakan tambahkan sheet terlebih dahulu.', 'info');
                            }
                            
                            $('#spreadsheet_sheet_id').trigger('change');
                            $('#sheetFilter').trigger('change');
                        } else {
                            console.error('Failed to load sheets:', response.message);
                            Swal.fire('Error', response.message || 'Gagal memuat data sheet', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error loading sheets:', xhr.responseJSON);
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function showCreateModal() {
                isEditMode = false;
                editingId = null;
                $('#modalTitle').text('Tambah Column Configuration');
                $('#columnForm')[0].reset();
                AdminUtils.clearValidationErrors('#columnForm');
                
                // Reset Select2 values
                $('#spreadsheet_sheet_id').val(null).trigger('change');
                
                const existingBackdrops = document.querySelectorAll('[modal-backdrop]');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                
                modal.show();
            }

            function editColumn(id) {
                isEditMode = true;
                editingId = id;
                $('#modalTitle').text('Edit Column Configuration');

                $.ajax({
                    url: '{{ route('admin.master-data.spreadsheet-column.show', ['id' => '__ID__']) }}'.replace('__ID__', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const column = response.data;
                            $('#spreadsheet_sheet_id').val(column.spreadsheet_sheet_id).trigger('change');
                            $('#column_key').val(column.column_key);
                            $('#column_name').val(column.column_name);
                            $('#column_range').val(column.column_range);
                            $('#data_type').val(column.data_type);
                            $('#description').val(column.description);
                            
                            if (column.validation_rules) {
                                $('#validation_rules').val(JSON.stringify(column.validation_rules, null, 2));
                                $('#toggleAdvanced').click(); // Show advanced settings
                            }

                            AdminUtils.clearValidationErrors('#columnForm');
                            
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

            function saveColumn() {
                const formData = $('#columnForm').serialize();
                
                // Handle validation_rules JSON
                const validationRules = $('#validation_rules').val().trim();
                let additionalData = '';
                
                if (validationRules) {
                    try {
                        const parsed = JSON.parse(validationRules);
                        additionalData = '&validation_rules=' + encodeURIComponent(JSON.stringify(parsed));
                    } catch (e) {
                        Swal.fire('Error', 'Format JSON validation rules tidak valid', 'error');
                        return;
                    }
                }
                
                const url = isEditMode ? 
                    '{{ route('admin.master-data.spreadsheet-column.update', ['id' => '__ID__']) }}'.replace('__ID__', editingId) : 
                    '{{ route('admin.master-data.spreadsheet-column.store') }}';

                $.ajax({
                    url: url,
                    type: isEditMode ? 'PUT' : 'POST',
                    data: formData + additionalData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            modal.hide();
                            dataTable.ajax.reload(null, false);
                        } else {
                            if (response.errors) {
                                AdminUtils.showValidationErrors(response.errors, '#columnForm');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        const errors = AdminUtils.handleAjaxError(xhr, status, error);
                        if (errors) {
                            AdminUtils.showValidationErrors(errors, '#columnForm');
                        }
                    }
                });
            }

            function deleteColumn(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Column configuration akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.master-data.spreadsheet-column.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id),
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
            $(document).on('click', '[data-modal-hide="column-modal"]', function() {
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
