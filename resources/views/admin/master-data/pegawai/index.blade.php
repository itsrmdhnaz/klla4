<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Master Data Pegawai') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header dengan flex yang lebih balanced -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Data Pegawai</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <button type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                data-modal-target="employee-modal" data-modal-toggle="employee-modal"
                                onclick="showCreateModal()">
                                <i class="fas fa-plus mr-2"></i>Tambah Pegawai
                            </button>
                        </div>
                    </div>

                    <!-- Table Container dengan proper width constraint -->
                    <div class="w-full overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="employeeTable" class="table table-striped table-bordered w-full">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">NIP</th>
                                        <th>Nama Pegawai</th>
                                        <th>Email</th>
                                        <th>Cabang</th>
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

    <!-- Flowbite Modal - Medium Size -->
    <div id="employee-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="modalTitle" class="text-xl font-medium text-black">
                        Tambah Pegawai
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="employee-modal">
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
                    <form id="employeeForm" class="space-y-4">
                        <div>
                            <label for="nip" class="block mb-2 text-sm font-medium text-black">NIP *</label>
                            <input type="text" id="nip" name="nip"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="nama_pegawai" class="block mb-2 text-sm font-medium text-black">Nama Pegawai
                                *</label>
                            <input type="text" id="nama_pegawai" name="nama_pegawai"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-black">Email *</label>
                            <input type="email" id="email" name="email"
                                class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="branches" class="block mb-2 text-sm font-medium text-black">Cabang *</label>
                            <select id="branches" name="branches[]" multiple required>
                                <!-- Options loaded via AJAX -->
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                            <p class="text-sm text-gray-500 mt-1">Pilih satu atau lebih cabang</p>
                        </div>
                        <div class="text-sm text-red-600" id="branchErrorMsg" style="display:none"></div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" onclick="saveEmployee()"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan
                    </button>
                    <button data-modal-hide="employee-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 ">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            let dataTable;
            let isEditMode = false;
            let editingNip = null;
            let modal;

            $(document).ready(function() {
                // Initialize Flowbite modal dengan proper options
                const $targetEl = document.getElementById('employee-modal');
                const options = {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        // Clear form dan backdrop saat modal ditutup
                        $('#employeeForm')[0].reset();
                        AdminUtils.clearValidationErrors('#employeeForm');
                        
                        // Remove any remaining backdrop manually
                        setTimeout(() => {
                            const backdrops = document.querySelectorAll('[modal-backdrop]');
                            backdrops.forEach(backdrop => backdrop.remove());
                        }, 100);
                    },
                    onShow: () => {
                        // Focus on first input when modal opens
                        setTimeout(() => {
                            $('#nip').focus();
                        }, 100);
                    }
                };
                
                modal = new Modal($targetEl, options);

                // Initialize DataTable with proper responsive settings
                dataTable = $('#employeeTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('admin.master-data.pegawai.data') }}',
                        type: 'GET',
                        error: function(xhr, error, thrown) {
                            AdminUtils.handleAjaxError(xhr, error, thrown);
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'nip',
                            name: 'nip',
                            className: 'text-center'
                        },
                        {
                            data: 'nama_pegawai',
                            name: 'nama_pegawai'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'branches',
                            name: 'branches',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'is_active',
                            orderable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    language: window.dataTableLanguage,
                    dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>' +
                        '<"row"<"col-12"tr>>' +
                        '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
                    drawCallback: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    },
                    initComplete: function() {
                        this.api().columns.adjust();
                    }
                });

                // Initialize Select2
                initializeSelect2();
                loadBranches();
            });

            function initializeSelect2() {
                // Simple Select2 initialization without custom styling
                AdminUtils.initSelect2('#branches', {
                    placeholder: 'Pilih cabang...',
                    dropdownParent: $('#employee-modal')
                });
            }

            function loadBranches() {
                $.ajax({
                    url: '{{ route('admin.master-data.pegawai.branches') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#branches').empty();
                            $.each(response.data, function(index, branch) {
                                $('#branches').append(new Option(branch.branch_name, branch.id_branch));
                            });
                            $('#branches').trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function showCreateModal() {
                isEditMode = false;
                editingNip = null;
                $('#modalTitle').text('Tambah Pegawai');
                $('#employeeForm')[0].reset();
                AdminUtils.clearValidationErrors('#employeeForm');
                $('#nip').prop('disabled', false);

                $('#branches').val(null).trigger('change');

                // Clear any existing backdrops before showing
                const existingBackdrops = document.querySelectorAll('[modal-backdrop]');
                existingBackdrops.forEach(backdrop => backdrop.remove());

                modal.show();
            }

            function editEmployee(nip) {
                isEditMode = true;
                editingNip = nip;
                $('#modalTitle').text('Edit Pegawai');

                $.ajax({
                    url: '{{ route('admin.master-data.pegawai.show', ['nip' => '__NIP__']) }}'.replace('__NIP__', nip),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const employee = response.data;
                            $('#nip').val(employee.nip).prop('disabled', true);
                            $('#nama_pegawai').val(employee.nama_pegawai);
                            $('#email').val(employee.email);

                            const selectedBranches = employee.branches.map(branch => branch.id_branch);
                            $('#branches').val(selectedBranches).trigger('change');

                            AdminUtils.clearValidationErrors('#employeeForm');
                            
                            // Clear any existing backdrops before showing
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

            function deleteEmployee(nip) {
                Swal.fire({
                    title: 'Hapus Pegawai?',
                    text: 'Apakah Anda yakin ingin menghapus pegawai ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.master-data.pegawai.destroy', ['nip' => '__NIP__']) }}'.replace('__NIP__', nip),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    dataTable.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                AdminUtils.handleAjaxError(xhr, status, error);
                            }
                        });
                    }
                });
            }

            function saveEmployee() {
                // Get form data with proper handling for select multiple
                const formData = $('#employeeForm').serializeArray();
                const data = {};

                // Convert form data to object, handling multiple values for same name
                $.each(formData, function(i, field) {
                    if (data[field.name]) {
                        if (Array.isArray(data[field.name])) {
                            data[field.name].push(field.value);
                        } else {
                            data[field.name] = [data[field.name], field.value];
                        }
                    } else {
                        data[field.name] = field.value;
                    }
                });

                // Special handling for Select2 multiple values
                const branchesValues = $('#branches').val();
                if (branchesValues && branchesValues.length > 0) {
                    data['branches'] = branchesValues;
                }

                // Prevent edit/delete for now
                if (isEditMode) {
                    Swal.fire('Fitur Belum Tersedia', 'Edit pegawai belum diaktifkan.', 'info');
                    return;
                }

                // Prevent branch creation from here
                if (!branchesValues || branchesValues.length === 0) {
                    $('#branchErrorMsg').text('Cabang wajib dipilih. Tidak bisa membuat cabang baru dari sini.').show();
                    return;
                } else {
                    $('#branchErrorMsg').hide();
                }

                const url = '{{ route('admin.master-data.pegawai.store') }}';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            modal.hide();
                            dataTable.ajax.reload(null, false);
                        } else {
                            if (response.errors) {
                                AdminUtils.showValidationErrors(response.errors, '#employeeForm');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        const errors = AdminUtils.handleAjaxError(xhr, status, error);
                        if (errors) {
                            AdminUtils.showValidationErrors(errors, '#employeeForm');
                        }
                    }
                });
            }

            // Handle manual modal close events
            $(document).on('click', '[data-modal-hide="employee-modal"]', function() {
                modal.hide();
            });

            // Handle escape key
            $(document).keydown(function(e) {
                if (e.key === "Escape") {
                    modal.hide();
                }
            });

            // Handle backdrop click
            $(document).on('click', '[modal-backdrop]', function(e) {
                if (e.target === this) {
                    modal.hide();
                }
            });
        </script>
    @endpush
</x-admin-app-layout>
