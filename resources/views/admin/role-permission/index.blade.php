<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Role & Permission Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Role & Permission Management (Spatie)</h3>
                            <p class="text-sm text-gray-600 mt-1">Kelola user roles dan permissions menggunakan Spatie Permission</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                onclick="showCreateBranchRoleModal()">
                                <i class="fas fa-plus mr-2"></i>Create Branch Role
                            </button>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fas fa-users text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-600">Total Users</p>
                                    <p class="text-lg font-semibold text-blue-900">{{ $users->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-user-shield text-green-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-600">Total Roles</p>
                                    <p class="text-lg font-semibold text-green-900">{{ $roles->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <i class="fas fa-building text-yellow-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-600">Branches</p>
                                    <p class="text-lg font-semibold text-yellow-900">{{ $branches->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <i class="fas fa-key text-purple-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-purple-600">Branch Roles</p>
                                    <p class="text-lg font-semibold text-purple-900">{{ $roles->filter(function($role) { return str_contains($role->name, 'branch_'); })->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="w-full overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="rolePermissionTable" class="table table-striped table-bordered w-full">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>User Info</th>
                                        <th>Employee Info</th>
                                        <th>Roles</th>
                                        <th class="text-center">Permissions</th>
                                        <th>Branch Access</th>
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

    <!-- Role Management Modal -->
    <div id="role-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-3xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="roleModalTitle" class="text-xl font-medium text-black">
                        Manage User Roles
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="role-modal">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Assign Role Section -->
                        <div class="space-y-4">
                            <h5 class="font-medium text-gray-900">Assign Role</h5>
                            <form id="assignRoleForm" class="space-y-3">
                                <input type="hidden" id="assign_user_id" name="user_id">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Available Roles</label>
                                    <select id="role_select" name="role_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Assign Role
                                </button>
                            </form>
                        </div>

                        <!-- Current Roles Section -->
                        <div class="space-y-4">
                            <h5 class="font-medium text-gray-900">Current Roles</h5>
                            <div id="currentRoles" class="space-y-2 max-h-64 overflow-y-auto">
                                <!-- Roles will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Preview -->
                    <div class="mt-6">
                        <h5 class="font-medium text-gray-900 mb-3">All Permissions</h5>
                        <div id="permissionsPreview" class="bg-gray-50 p-4 rounded-lg max-h-40 overflow-y-auto">
                            <!-- Permissions will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Branch Role Modal -->
    <div id="branch-role-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-medium text-black">
                        Create Branch Role
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="branch-role-modal">
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
                    <form id="branchRoleForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Branch</label>
                            <select name="branch_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id_branch }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role Type</label>
                            <select name="role_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select Type</option>
                                <option value="user">Branch User (Read Only)</option>
                                <option value="admin">Branch Admin (Read/Write)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Custom Role Name (Optional)</label>
                            <input type="text" name="role_name" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Leave empty for auto-generated name">
                        </div>

                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Create Role
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let dataTable;
            let roleModal;
            let branchRoleModal;
            let currentUserId = null;

            $(document).ready(function() {
                // Initialize modals
                initializeModals();
                initializeDataTable();
                bindEvents();
            });

            function initializeModals() {
                const roleModalEl = document.getElementById('role-modal');
                roleModal = new Modal(roleModalEl, {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true
                });

                const branchRoleModalEl = document.getElementById('branch-role-modal');
                branchRoleModal = new Modal(branchRoleModalEl, {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true
                });
            }

            function initializeDataTable() {
                dataTable = $('#rolePermissionTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('admin.system.permissions.data') }}',
                        type: 'GET',
                        error: function(xhr, error, thrown) {
                            AdminUtils.handleAjaxError(xhr, error, thrown);
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'name', name: 'name' },
                        { data: 'employee_info', name: 'employee_info', orderable: false, searchable: false },
                        { data: 'roles_display', name: 'roles_display', orderable: false, searchable: false },
                        { data: 'permissions_count', name: 'permissions_count', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'branch_access', name: 'branch_access', orderable: false, searchable: false },
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
            }

            function bindEvents() {
                // Form submissions
                $('#assignRoleForm').on('submit', function(e) {
                    e.preventDefault();
                    assignRole();
                });

                $('#branchRoleForm').on('submit', function(e) {
                    e.preventDefault();
                    createBranchRole();
                });
            }

            function manageUserRoles(userId) {
                currentUserId = userId;
                $('#assign_user_id').val(userId);
                
                // Load user permissions
                loadUserPermissions(userId);
                
                roleModal.show();
            }

            function loadUserPermissions(userId) {
                $.ajax({
                    url: '{{ route('admin.system.permissions.user-permissions', ['userId' => '__USER_ID__']) }}'.replace('__USER_ID__', userId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            displayUserInfo(response.data.user);
                            displayCurrentRoles(response.data.roles);
                            displayAllPermissions(response.data.all_permissions);
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

            function displayCurrentRoles(roles) {
                let rolesHtml = '';
                roles.forEach(role => {
                    const badgeClass = getRoleBadgeClass(role.name);
                    rolesHtml += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="badge ${badgeClass}">${role.name}</span>
                                <small class="text-gray-500 ml-2">${role.permissions_count} permissions</small>
                            </div>
                            <button onclick="removeRole(${role.id})" 
                                class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                });
                $('#currentRoles').html(rolesHtml || '<p class="text-gray-500 text-sm">No roles assigned</p>');
            }

            function displayAllPermissions(permissions) {
                let permissionsHtml = '';
                permissions.forEach(permission => {
                    const badge = permission.via_role ? 
                        '<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">via role</span>' :
                        '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">direct</span>';
                    permissionsHtml += `<div class="flex justify-between items-center mb-1">
                        <span class="text-sm">${permission.name}</span>
                        ${badge}
                    </div>`;
                });
                $('#permissionsPreview').html(permissionsHtml || '<p class="text-gray-500 text-sm">No permissions</p>');
            }

            function assignRole() {
                const formData = $('#assignRoleForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.system.permissions.assign-role') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            loadUserPermissions(currentUserId);
                            dataTable.ajax.reload(null, false);
                            $('#assignRoleForm')[0].reset();
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function removeRole(roleId) {
                Swal.fire({
                    title: 'Remove Role?',
                    text: 'Are you sure you want to remove this role from user?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.system.permissions.remove-role') }}',
                            type: 'DELETE',
                            data: {
                                user_id: currentUserId,
                                role_id: roleId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Removed!', response.message, 'success');
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

            function createBranchRole() {
                const formData = $('#branchRoleForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.system.permissions.create-branch-role') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            branchRoleModal.hide();
                            $('#branchRoleForm')[0].reset();
                            
                            // Reload role select
                            $('#role_select').append(new Option(response.data.name, response.data.id));
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            function showCreateBranchRoleModal() {
                branchRoleModal.show();
            }

            function viewUserPermissions(userId) {
                manageUserRoles(userId);
            }

            function getRoleBadgeClass(roleName) {
                if (roleName.includes('super-admin')) return 'badge-danger';
                if (roleName.includes('admin')) return 'badge-warning';
                if (roleName.includes('branch_')) return 'badge-info';
                return 'badge-secondary';
            }

            // Handle modal close events
            $(document).on('click', '[data-modal-hide="role-modal"]', function() {
                roleModal.hide();
            });

            $(document).on('click', '[data-modal-hide="branch-role-modal"]', function() {
                branchRoleModal.hide();
            });
        </script>
    @endpush
</x-admin-app-layout>
