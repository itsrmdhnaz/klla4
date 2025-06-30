<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Master Data Spreadsheet') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Data Spreadsheet</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <button type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                data-modal-target="spreadsheet-modal" data-modal-toggle="spreadsheet-modal"
                                onclick="showCreateModal()">
                                <i class="fas fa-plus mr-2"></i>Tambah Spreadsheet
                            </button>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="w-full overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="spreadsheetTable" class="table table-striped table-bordered w-full">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama Spreadsheet</th>
                                        <th>Spreadsheet ID</th>
                                        <th>URL</th>
                                        <th>Email Service Account</th>
                                        <th class="text-center">Status Credentials</th>
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
    <div id="spreadsheet-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="modalTitle" class="text-xl font-medium text-black">
                        Tambah Spreadsheet
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="spreadsheet-modal">
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
                    <form id="spreadsheetForm" class="space-y-4" enctype="multipart/form-data">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-black">Nama Spreadsheet *</label>
                            <input type="text" id="name" name="name"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="spreadsheet_id" class="block mb-2 text-sm font-medium text-black">Spreadsheet ID *</label>
                            <input type="text" id="spreadsheet_id" name="spreadsheet_id"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                            <p class="text-sm text-gray-500 mt-1">ID dari URL Google Sheets</p>
                        </div>

                        <div>
                            <label for="spreadsheet_url" class="block mb-2 text-sm font-medium text-black">URL Spreadsheet</label>
                            <input type="url" id="spreadsheet_url" name="spreadsheet_url"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="service_account_email" class="block mb-2 text-sm font-medium text-black">Email Service Account</label>
                            <input type="email" id="service_account_email" name="service_account_email"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="credentials_file" class="block mb-2 text-sm font-medium text-black">
                                File Credentials <span id="credentialsRequired">*</span>
                            </label>
                            <input type="file" id="credentials_file" name="credentials_file" accept=".json"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                            <p class="text-sm text-gray-500 mt-1">Upload file JSON service account dari Google Cloud Console</p>
                        </div>

                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-black">Deskripsi</label>
                            <textarea id="description" name="description" rows="3"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" onclick="saveSpreadsheet()"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan
                    </button>
                    <button type="button" data-modal-hide="spreadsheet-modal"
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
                const $targetEl = document.getElementById('spreadsheet-modal');
                const options = {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        $('#spreadsheetForm')[0].reset();
                        AdminUtils.clearValidationErrors('#spreadsheetForm');
                        setTimeout(() => {
                            const backdrops = document.querySelectorAll('[modal-backdrop]');
                            backdrops.forEach(backdrop => backdrop.remove());
                        }, 100);
                    }
                };
                
                modal = new Modal($targetEl, options);

                // Initialize DataTable
                dataTable = $('#spreadsheetTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('admin.master-data.spreadsheet.data') }}',
                        type: 'GET',
                        error: function(xhr, error, thrown) {
                            AdminUtils.handleAjaxError(xhr, error, thrown);
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'name', name: 'name' },
                        { data: 'spreadsheet_id', name: 'spreadsheet_id' },
                        { data: 'spreadsheet_url', name: 'spreadsheet_url', orderable: false },
                        { data: 'service_account_email', name: 'service_account_email' },
                        { data: 'credentials_status', name: 'credentials_status', orderable: false, className: 'text-center' },
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
            });

            function showCreateModal() {
                isEditMode = false;
                editingId = null;
                $('#modalTitle').text('Tambah Spreadsheet');
                $('#spreadsheetForm')[0].reset();
                AdminUtils.clearValidationErrors('#spreadsheetForm');
                $('#credentialsRequired').text('*');
                $('#credentials_file').prop('required', true);
                
                const existingBackdrops = document.querySelectorAll('[modal-backdrop]');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                
                modal.show();
            }

            function editSpreadsheet(id) {
                isEditMode = true;
                editingId = id;
                $('#modalTitle').text('Edit Spreadsheet');
                $('#credentialsRequired').text('');
                $('#credentials_file').prop('required', false);

                $.ajax({
                    url: '{{ route('admin.master-data.spreadsheet.show', ['id' => '__ID__']) }}'.replace('__ID__', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const spreadsheet = response.data;
                            $('#name').val(spreadsheet.name);
                            $('#spreadsheet_id').val(spreadsheet.spreadsheet_id);
                            $('#spreadsheet_url').val(spreadsheet.spreadsheet_url);
                            $('#service_account_email').val(spreadsheet.service_account_email);
                            $('#description').val(spreadsheet.description);

                            AdminUtils.clearValidationErrors('#spreadsheetForm');
                            
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

            function saveSpreadsheet() {
                const formData = new FormData($('#spreadsheetForm')[0]);
                
                const url = isEditMode ? 
                    '{{ route('admin.master-data.spreadsheet.update', ['id' => '__ID__']) }}'.replace('__ID__', editingId) : 
                    '{{ route('admin.master-data.spreadsheet.store') }}';

                if (isEditMode) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            modal.hide();
                            dataTable.ajax.reload(null, false);
                        } else {
                            if (response.errors) {
                                AdminUtils.showValidationErrors(response.errors, '#spreadsheetForm');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        const errors = AdminUtils.handleAjaxError(xhr, status, error);
                        if (errors) {
                            AdminUtils.showValidationErrors(errors, '#spreadsheetForm');
                        }
                    }
                });
            }

            function deleteSpreadsheet(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data spreadsheet akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.master-data.spreadsheet.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id),
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
            $(document).on('click', '[data-modal-hide="spreadsheet-modal"]', function() {
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
