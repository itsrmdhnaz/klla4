<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Permission Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Manajemen Permission Spreadsheet</h3>
                            <p class="text-sm text-gray-600 mt-1">Kelola akses user ke spreadsheet, sheet, dan kolom</p>
                        </div>
                        <div class="flex gap-2">
                            <!-- Filter User -->
                            <select id="userFilter" class="bg-gray-50 border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                                <option value="">Semua User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            
                            <button type="button"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                onclick="showBulkAssignModal()">
                                <i class="fas fa-users-cog mr-2"></i>Bulk Assign
                            </button>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="w-full overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="permissionTable" class="table table-striped table-bordered w-full">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>User Info</th>
                                        <th>Employee Info</th>
                                        <th class="text-center">Spreadsheets</th>
                                        <th class="text-center">Sheets</th>
                                        <th class="text-center">Columns</th>
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

    <!-- Permission Management Modal -->
    <div id="permission-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="permissionModalTitle" class="text-xl font-medium text-black">
                        Manage User Permissions
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="permission-modal">
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
                    <!-- User Info -->
                    <div id="userInfoSection" class="bg-gray-50 p-4 rounded-lg hidden">
                        <h4 class="font-semibold text-gray-800 mb-2">User Information</h4>
                        <div id="userInfoContent"></div>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button type="button" 
                                class="permission-tab active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600"
                                data-tab="spreadsheet">
                                Spreadsheet Permissions
                            </button>
                            <button type="button" 
                                class="permission-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                data-tab="sheet">
                                Sheet Permissions
                            </button>
                            <button type="button" 
                                class="permission-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                data-tab="column">
                                Column Permissions
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Contents -->
                    <div id="spreadsheet-tab-content" class="permission-tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Assign Form -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Assign Spreadsheet Permission</h5>
                                <form id="spreadsheetPermissionForm" class="space-y-3">
                                    <input type="hidden" id="spreadsheet_user_id" name="user_id">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Spreadsheet</label>
                                        <select id="spreadsheet_select" name="spreadsheet_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Spreadsheet</option>
                                            @foreach($spreadsheets as $spreadsheet)
                                                <option value="{{ $spreadsheet->id }}">{{ $spreadsheet->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Permission Level</label>
                                        <select name="permission_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="read">Read Only</option>
                                            <option value="write">Read & Write</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                        Assign Permission
                                    </button>
                                </form>
                            </div>

                            <!-- Current Permissions -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Current Spreadsheet Permissions</h5>
                                <div id="currentSpreadsheetPermissions" class="space-y-2 max-h-64 overflow-y-auto">
                                    <!-- Permissions will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="sheet-tab-content" class="permission-tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Assign Form -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Assign Sheet Permission</h5>
                                <form id="sheetPermissionForm" class="space-y-3">
                                    <input type="hidden" id="sheet_user_id" name="user_id">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Sheet</label>
                                        <select id="sheet_select" name="sheet_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Sheet</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Permission Level</label>
                                        <select name="permission_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="read">Read Only</option>
                                            <option value="write">Read & Write</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                        Assign Permission
                                    </button>
                                </form>
                            </div>

                            <!-- Current Permissions -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Current Sheet Permissions</h5>
                                <div id="currentSheetPermissions" class="space-y-2 max-h-64 overflow-y-auto">
                                    <!-- Permissions will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="column-tab-content" class="permission-tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Assign Form -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Assign Column Permission</h5>
                                <form id="columnPermissionForm" class="space-y-3">
                                    <input type="hidden" id="column_user_id" name="user_id">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Column</label>
                                        <select id="column_select" name="column_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Column</option>
                                        </select>
                                    </div>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="can_read" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">Can Read</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="can_write" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">Can Write</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                                        Assign Permission
                                    </button>
                                </form>
                            </div>

                            <!-- Current Permissions -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Current Column Permissions</h5>
                                <div id="currentColumnPermissions" class="space-y-2 max-h-64 overflow-y-auto">
                                    <!-- Permissions will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Assign Modal -->
    <div id="bulk-assign-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-medium text-black">
                        Bulk Permission Assignment
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="bulk-assign-modal">
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
                    <form id="bulkAssignForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Users</label>
                            <select id="bulk_users" name="users[]" multiple class="mt-1 block w-full">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Spreadsheet</label>
                            <select id="bulk_spreadsheet" name="spreadsheet_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Spreadsheet</option>
                                @foreach($spreadsheets as $spreadsheet)
                                    <option value="{{ $spreadsheet->id }}">{{ $spreadsheet->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Permission Level</label>
                            <select name="permission_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="read">Read Only</option>
                                <option value="write">Read & Write</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Assign to Selected Users
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let dataTable;
            let permissionModal;
            let bulkAssignModal;
            let currentUserId = null;
            let spreadsheetHierarchy = [];

            $(document).ready(function() {
                // Initialize modals
                initializeModals();
                initializeSelect2();
                initializeDataTable();
                loadSpreadsheetHierarchy();
                bindEvents();
            });

            function initializeModals() {
                const permissionModalEl = document.getElementById('permission-modal');
                permissionModal = new Modal(permissionModalEl, {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true
                });

                const bulkModalEl = document.getElementById('bulk-assign-modal');
                bulkAssignModal = new Modal(bulkModalEl, {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true
                });
            }

            function initializeSelect2() {
                AdminUtils.initSelect2('#bulk_users', {
                    placeholder: 'Pilih users...',
                    dropdownParent: $('#bulk-assign-modal')
                });
            }

            function initializeDataTable() {
                dataTable = $('#permissionTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('admin.spreadsheet.permissions.data') }}',
                        type: 'GET',
                        data: function(d) {
                            d.user_id = $('#userFilter').val();
                        },
                        error: function(xhr, error, thrown) {
                            AdminUtils.handleAjaxError(xhr, error, thrown);
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'name', name: 'name' },
                        { data: 'employee_info', name: 'employee_info', orderable: false, searchable: false },
                        { data: 'spreadsheet_count', name: 'spreadsheet_count', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'sheet_count', name: 'sheet_count', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'column_count', name: 'column_count', orderable: false, searchable: false, className: 'text-center' },
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
                $('#userFilter').on('change', function() {
                    dataTable.ajax.reload();
                });
            }

            function loadSpreadsheetHierarchy() {
                $.ajax({
                    url: '{{ route('admin.spreadsheet.permissions.hierarchy') }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            spreadsheetHierarchy = response.data;
                            populateSheetSelect();
                            populateColumnSelect();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading hierarchy:', error);
                    }
                });
            }

            function populateSheetSelect() {
                const sheetSelect = $('#sheet_select');
                sheetSelect.empty().append('<option value="">Pilih Sheet</option>');
                
                spreadsheetHierarchy.forEach(spreadsheet => {
                    spreadsheet.sheets.forEach(sheet => {
                        const displayName = `${spreadsheet.name} - ${sheet.name}`;
                        sheetSelect.append(new Option(displayName, sheet.id));
                    });
                });
            }

            function populateColumnSelect() {
                const columnSelect = $('#column_select');
                columnSelect.empty().append('<option value="">Pilih Column</option>');
                
                spreadsheetHierarchy.forEach(spreadsheet => {
                    spreadsheet.sheets.forEach(sheet => {
                        sheet.columns.forEach(column => {
                            const displayName = `${spreadsheet.name} - ${sheet.name} - ${column.name}`;
                            columnSelect.append(new Option(displayName, column.id));
                        });
                    });
                });
            }

            function bindEvents() {
                // Tab switching
                $('.permission-tab').on('click', function() {
                    const tabName = $(this).data('tab');
                    switchTab(tabName);
                });

                // Form submissions
                $('#spreadsheetPermissionForm').on('submit', function(e) {
                    e.preventDefault();
                    assignSpreadsheetPermission();
                });

                $('#sheetPermissionForm').on('submit', function(e) {
                    e.preventDefault();
                    assignSheetPermission();
                });

                $('#columnPermissionForm').on('submit', function(e) {
                    e.preventDefault();
                    assignColumnPermission();
                });

                $('#bulkAssignForm').on('submit', function(e) {
                    e.preventDefault();
                    performBulkAssign();
                });
            }

            function switchTab(tabName) {
                // Update tab buttons
                $('.permission-tab').removeClass('active border-blue-500 text-blue-600')
                    .addClass('border-transparent text-gray-500');
                $(`[data-tab="${tabName}"]`).removeClass('border-transparent text-gray-500')
                    .addClass('active border-blue-500 text-blue-600');

                // Show/hide tab contents
                $('.permission-tab-content').addClass('hidden');
                $(`#${tabName}-tab-content`).removeClass('hidden');
            }

            function manageUserPermissions(userId) {
                currentUserId = userId;
                
                // Set user IDs in forms
                $('#spreadsheet_user_id, #sheet_user_id, #column_user_id').val(userId);
                
                // Load user permissions
                loadUserPermissions(userId);
                
                permissionModal.show();
            }

            function loadUserPermissions(userId) {
                $.ajax({
                    url: '{{ route('admin.spreadsheet.permissions.user', ['userId' => '__USER_ID__']) }}'.replace('__USER_ID__', userId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            displayUserInfo(response.user);
                            displayCurrentPermissions(response.permissions);
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function displayUserInfo(user) {
                const userInfoContent = `
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                        </div>
                        <div>
                            <h5 class="font-semibold text-gray-900">${user.name}</h5>
                            <p class="text-sm text-gray-600">${user.email}</p>
                        </div>
                    </div>
                `;
                $('#userInfoContent').html(userInfoContent);
                $('#userInfoSection').removeClass('hidden');
            }

            function displayCurrentPermissions(permissions) {
                // Display spreadsheet permissions
                let spreadsheetHtml = '';
                permissions.spreadsheets.forEach(perm => {
                    const badgeClass = perm.permission_level === 'admin' ? 'bg-red-100 text-red-800' : 
                                     perm.permission_level === 'write' ? 'bg-yellow-100 text-yellow-800' : 
                                     'bg-green-100 text-green-800';
                    spreadsheetHtml += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium">${perm.spreadsheet_name}</span>
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full ${badgeClass}">
                                    ${perm.permission_level.toUpperCase()}
                                </span>
                            </div>
                            <button onclick="revokePermission('spreadsheet', '${perm.id}')" 
                                class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                });
                $('#currentSpreadsheetPermissions').html(spreadsheetHtml || '<p class="text-gray-500 text-sm">No permissions assigned</p>');

                // Display sheet permissions
                let sheetHtml = '';
                permissions.sheets.forEach(perm => {
                    const badgeClass = perm.permission_level === 'write' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                    sheetHtml += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium">${perm.sheet_name}</span>
                                <br><small class="text-gray-500">${perm.spreadsheet_name}</small>
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full ${badgeClass}">
                                    ${perm.permission_level.toUpperCase()}
                                </span>
                            </div>
                            <button onclick="revokePermission('sheet', '${perm.id}')" 
                                class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                });
                $('#currentSheetPermissions').html(sheetHtml || '<p class="text-gray-500 text-sm">No permissions assigned</p>');

                // Display column permissions
                let columnHtml = '';
                permissions.columns.forEach(perm => {
                    const readBadge = perm.can_read ? '<span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">READ</span>' : '';
                    const writeBadge = perm.can_write ? '<span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">WRITE</span>' : '';
                    columnHtml += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium">${perm.column_name}</span>
                                <br><small class="text-gray-500">${perm.spreadsheet_name} - ${perm.sheet_name}</small>
                                <div class="mt-1">${readBadge} ${writeBadge}</div>
                            </div>
                            <button onclick="revokePermission('column', '${perm.id}')" 
                                class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                });
                $('#currentColumnPermissions').html(columnHtml || '<p class="text-gray-500 text-sm">No permissions assigned</p>');
            }

            function assignSpreadsheetPermission() {
                const formData = $('#spreadsheetPermissionForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.spreadsheet.permissions.assign-spreadsheet') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            loadUserPermissions(currentUserId);
                            dataTable.ajax.reload(null, false);
                            $('#spreadsheetPermissionForm')[0].reset();
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function assignSheetPermission() {
                const formData = $('#sheetPermissionForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.spreadsheet.permissions.assign-sheet') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            loadUserPermissions(currentUserId);
                            dataTable.ajax.reload(null, false);
                            $('#sheetPermissionForm')[0].reset();
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function assignColumnPermission() {
                const formData = $('#columnPermissionForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.spreadsheet.permissions.assign-column') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            loadUserPermissions(currentUserId);
                            dataTable.ajax.reload(null, false);
                            $('#columnPermissionForm')[0].reset();
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function revokePermission(type, permissionId) {
                Swal.fire({
                    title: 'Revoke Permission?',
                    text: 'Are you sure you want to revoke this permission?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, revoke it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.spreadsheet.permissions.revoke') }}',
                            type: 'DELETE',
                            data: {
                                type: type,
                                permission_id: permissionId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Revoked!', response.message, 'success');
                                    loadUserPermissions(currentUserId);
                                    dataTable.ajax.reload(null, false);
                                }
                            },
                            error: function(xhr, status, error) {
                                AdminUtils.handleAjaxError(xhr, status, error);
                            }
                        });
                    }
                });
            }

            function showBulkAssignModal() {
                bulkAssignModal.show();
            }

            function performBulkAssign() {
                const formData = $('#bulkAssignForm').serialize();
                
                // This would require a separate bulk assign endpoint
                Swal.fire('Info', 'Bulk assign feature coming soon!', 'info');
            }

            function viewUserPermissions(userId) {
                manageUserPermissions(userId);
            }

            // Handle modal close events
            $(document).on('click', '[data-modal-hide="permission-modal"]', function() {
                permissionModal.hide();
            });

            $(document).on('click', '[data-modal-hide="bulk-assign-modal"]', function() {
                bulkAssignModal.hide();
            });
        </script>
    @endpush
</x-admin-app-layout>
