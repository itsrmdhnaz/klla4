<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Master Data Cabang') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Data Cabang</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <button type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                data-modal-target="branch-modal" data-modal-toggle="branch-modal"
                                onclick="showCreateModal()">
                                <i class="fas fa-plus mr-2"></i>Tambah Cabang
                            </button>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="w-full overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="branchTable" class="table table-striped table-bordered w-full">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama Cabang</th>
                                        <th class="text-center">Jumlah Pegawai</th>
                                        <th class="text-center">Tanggal Dibuat</th>
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
    <div id="branch-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="modalTitle" class="text-xl font-medium text-black">
                        Tambah Cabang
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="branch-modal">
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
                    <form id="branchForm" class="space-y-4">
                        <div>
                            <label for="branch_name" class="block mb-2 text-sm font-medium text-black">Nama Cabang *</label>
                            <input type="text" id="branch_name" name="branch_name"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Masukkan nama cabang..."
                                required>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" onclick="saveBranch()"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan
                    </button>
                    <button type="button" data-modal-hide="branch-modal"
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
                const $targetEl = document.getElementById('branch-modal');
                const options = {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        $('#branchForm')[0].reset();
                        AdminUtils.clearValidationErrors('#branchForm');
                        setTimeout(() => {
                            const backdrops = document.querySelectorAll('[modal-backdrop]');
                            backdrops.forEach(backdrop => backdrop.remove());
                        }, 100);
                    },
                    onShow: () => {
                        setTimeout(() => {
                            $('#branch_name').focus();
                        }, 100);
                    }
                };
                
                modal = new Modal($targetEl, options);

                // Initialize DataTable
                dataTable = $('#branchTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('admin.master-data.cabang.data') }}',
                        type: 'GET',
                        error: function(xhr, error, thrown) {
                            AdminUtils.handleAjaxError(xhr, error, thrown);
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'branch_name', name: 'branch_name' },
                        { data: 'employee_count', name: 'employee_count', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'created_at', name: 'created_at', className: 'text-center' },
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
                $('#modalTitle').text('Tambah Cabang');
                $('#branchForm')[0].reset();
                AdminUtils.clearValidationErrors('#branchForm');
                
                const existingBackdrops = document.querySelectorAll('[modal-backdrop]');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                
                modal.show();
            }

            function editBranch(id) {
                isEditMode = true;
                editingId = id;
                $('#modalTitle').text('Edit Cabang');

                $.ajax({
                    url: '{{ route('admin.master-data.cabang.show', ['id' => '__ID__']) }}'.replace('__ID__', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const branch = response.data;
                            $('#branch_name').val(branch.branch_name);

                            AdminUtils.clearValidationErrors('#branchForm');
                            
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

            function saveBranch() {
                const formData = $('#branchForm').serialize();
                
                const url = isEditMode ? 
                    '{{ route('admin.master-data.cabang.update', ['id' => '__ID__']) }}'.replace('__ID__', editingId) : 
                    '{{ route('admin.master-data.cabang.store') }}';

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
                                AdminUtils.showValidationErrors(response.errors, '#branchForm');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        const errors = AdminUtils.handleAjaxError(xhr, status, error);
                        if (errors) {
                            AdminUtils.showValidationErrors(errors, '#branchForm');
                        }
                    }
                });
            }

            function deleteBranch(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data cabang akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.master-data.cabang.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id),
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
            $(document).on('click', '[data-modal-hide="branch-modal"]', function() {
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
