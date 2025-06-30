<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Role & Permission Management (Spatie Teams)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex-shrink-0">
                            <h3 class="text-lg font-semibold text-black">Team-based Role & Permission Management</h3>
                            <p class="text-sm text-gray-600 mt-1">Kelola user roles dan permissions berdasarkan team (cabang) menggunakan Spatie Permission</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                onclick="showCreateTeamRoleModal()">
                                <i class="fas fa-plus mr-2"></i>Create Team Role
                            </button>
                            {{-- <button type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none whitespace-nowrap"
                                onclick="showPermissionAssignmentModal()">
                                <i class="fas fa-key mr-2"></i>Assign Permissions
                            </button> --}}
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
                                    <p class="text-sm font-medium text-yellow-600">Teams (Branches)</p>
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
                                    <p class="text-sm font-medium text-purple-600">Team Roles</p>
                                    <p class="text-lg font-semibold text-purple-900">{{ $roles->filter(function($role) { return $role->team_id !== null; })->count() }}</p>
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
                                        <th>Team Access</th>
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
        <div class="relative w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="roleModalTitle" class="text-xl font-medium text-black">
                        Manage User Roles & Teams
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

                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button type="button" 
                                class="role-tab active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600"
                                data-tab="assign-role">
                                Assign Role
                            </button>
                            <button type="button" 
                                class="role-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                data-tab="assign-team">
                                Assign to Team
                            </button>
                            <button type="button" 
                                class="role-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                data-tab="current-roles">
                                Current Roles
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Contents -->
                    <div id="assign-role-tab-content" class="role-tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Assign Role Section -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Assign Existing Role</h5>
                                <form id="assignRoleForm" class="space-y-3">
                                    <input type="hidden" id="assign_user_id" name="user_id">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Available Roles</label>
                                        <select id="role_select" name="role_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">
                                                    {{ $role->name }}
                                                    @if($role->team_id)
                                                        (Team: {{ $branches->where('id_branch', $role->team_id)->first()->branch_name ?? $role->team_id }})
                                                    @endif
                                                </option>
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
                    </div>

                    <div id="assign-team-tab-content" class="role-tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Assign to Team Section -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Assign User to Team</h5>
                                <form id="assignTeamForm" class="space-y-3">
                                    <input type="hidden" id="team_assign_user_id" name="user_id">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Team (Branch)</label>
                                        <select name="team_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Team</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id_branch }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                        Assign to Team
                                    </button>
                                </form>
                            </div>

                            <!-- Team Roles Info -->
                            <div class="space-y-4">
                                <h5 class="font-medium text-gray-900">Team Assignment Info</h5>
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm text-blue-800">
                                        <strong>Team User:</strong> Can read team-specific data<br>
                                        <strong>Team Admin:</strong> Can read and write team-specific data
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="current-roles-tab-content" class="role-tab-content hidden">
                        <!-- Permissions Preview -->
                        <div class="space-y-4">
                            <h5 class="font-medium text-gray-900">All Permissions</h5>
                            <div id="permissionsPreview" class="bg-gray-50 p-4 rounded-lg max-h-60 overflow-y-auto">
                                <!-- Permissions will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permission Assignment Modal -->
    <div id="permission-assignment-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-5xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-medium text-black">
                        Assign Permissions
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="permission-assignment-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-6">
                    <!-- Target Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b pb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                            <select id="assign_target_type" class="w-full border-gray-300 rounded-lg">
                                <option value="">Select Target</option>
                                <option value="user">User</option>
                                <option value="role">Role</option>
                                <option value="group">Group (Team)</option>
                            </select>
                        </div>
                        <div id="target_user_section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                            <select id="target_user" class="w-full border-gray-300 rounded-lg">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="target_role_section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Role</label>
                            <select id="target_role" class="w-full border-gray-300 rounded-lg">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name }}
                                        @if($role->team_id)
                                            (Team: {{ $branches->where('id_branch', $role->team_id)->first()->branch_name ?? $role->team_id }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="target_group_section" class="hidden">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Team</label>
                                    <select id="target_team" class="w-full border-gray-300 rounded-lg">
                                        <option value="">Select Team</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id_branch }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Group Type</label>
                                    <select id="target_group_type" class="w-full border-gray-300 rounded-lg">
                                        <option value="">Select Type</option>
                                        <option value="team-user">Team User</option>
                                        <option value="team-admin">Team Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hierarchy Selection -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900">Select Permissions</h4>
                        
                        <!-- Spreadsheet Level -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Spreadsheet</label>
                                <select id="permission_spreadsheet" class="w-full border-gray-300 rounded-lg">
                                    <option value="">All Spreadsheets</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sheet</label>
                                <select id="permission_sheet" multiple class="w-full border-gray-300 rounded-lg" disabled>
                                    <option value="">Select sheets...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                                <select id="permission_action" class="w-full border-gray-300 rounded-lg">
                                    <option value="read">Read</option>
                                    <option value="write">Write</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <!-- Column Selection -->
                        <div id="column_selection_section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Columns (Optional - select specific columns)</label>
                            <div id="columns_container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4">
                                <!-- Columns will be loaded here -->
                            </div>
                        </div>

                        <!-- Permission Preview -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 mb-2">Generated Permissions:</h5>
                            <div id="permission_preview" class="text-sm text-gray-600 min-h-[50px] max-h-32 overflow-y-auto bg-white p-3 rounded border">
                                Select target and permissions to see preview...
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" onclick="assignPermissions()"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Assign Permissions
                    </button>
                    <button data-modal-hide="permission-assignment-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Team Role Modal (Enhanced) -->
    <div id="team-role-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-medium text-black">
                        Create Team Role with Permissions
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="team-role-modal">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Team Name Input -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900">Role Information</h4>
                            <form id="teamRoleForm" class="space-y-4" onsubmit="event.preventDefault(); createTeamRoleWithPermissions();">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Team Name *</label>
                                    <input type="text" name="team_name" id="team_role_team_name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter team name (ex: klla4.bone)" required oninput="updateTeamRolePreview()">
                                    <p class="text-xs text-gray-500 mt-1">Nama unik untuk team ini, contoh: <b>klla4.bone</b></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Role Name *</label>
                                    <input type="text" name="role_name" id="team_role_name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter custom role name" required>
                                    <p class="text-xs text-gray-500 mt-1">Enter a unique name for this role</p>
                                </div>
                            </form>
                        </div>
                        <!-- Permission Selection -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900">Specific Permissions (Optional)</h4>
                            <p class="text-sm text-gray-600">Select specific sheets and columns for this role. If none selected, default read permissions will be applied.</p>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Spreadsheet</label>
                                <select id="team_role_spreadsheet" class="w-full border-gray-300 rounded-lg">
                                    <option value="">Select Spreadsheet</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sheets</label>
                                <select id="team_role_sheets" multiple class="w-full border-gray-300 rounded-lg" disabled>
                                    <option value="">Select sheets...</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                            </div>
                            <div id="team_role_columns_section" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Columns</label>
                                <div id="team_role_columns_container" class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                    <!-- Columns will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Permission Preview for Team Role -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h5 class="font-medium text-gray-900 mb-2">Role Permissions Preview:</h5>
                        <div id="team_role_permission_preview" class="text-sm text-gray-600 min-h-[50px] max-h-32 overflow-y-auto bg-white p-3 rounded border">
                            Configure role to see permissions preview...
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="submit" form="teamRoleForm" onclick="createTeamRoleWithPermissions()"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Create Role
                    </button>
                    <button data-modal-hide="team-role-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let dataTable;
            let roleModal;
            let teamRoleModal;
            let permissionAssignmentModal;
            let currentUserId = null;
            let hierarchyData = {};

            $(document).ready(function() {
                // Initialize modals
                initializeModals();
                initializeDataTable();
                bindEvents();
                loadHierarchyData();
            });

            function initializeModals() {
                const roleModalEl = document.getElementById('role-modal');
                roleModal = new Modal(roleModalEl, {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true
                });

                const teamRoleModalEl = document.getElementById('team-role-modal');
                teamRoleModal = new Modal(teamRoleModalEl, {
                    placement: 'bottom-right',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900/50 fixed inset-0 z-40',
                    closable: true
                });

                const permissionModalEl = document.getElementById('permission-assignment-modal');
                permissionAssignmentModal = new Modal(permissionModalEl, {
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
                        url: '{{ route('admin.system.roles-permissions.data') }}',
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
                        { data: 'team_access', name: 'team_access', orderable: false, searchable: false },
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
                // Tab switching
                $('.role-tab').on('click', function() {
                    const tabName = $(this).data('tab');
                    switchTab(tabName);
                });

                // Form submissions
                $('#assignRoleForm').on('submit', function(e) {
                    e.preventDefault();
                    assignRole();
                });

                $('#assignTeamForm').on('submit', function(e) {
                    e.preventDefault();
                    assignUserToTeam();
                });

                // Permission assignment target type change
                $('#assign_target_type').on('change', function() {
                    const targetType = $(this).val();
                    $('[id^="target_"][id$="_section"]').addClass('hidden');
                    
                    if (targetType) {
                        $(`#target_${targetType}_section`).removeClass('hidden');
                    }
                    updatePermissionPreview();
                });

                // Hierarchy selection events
                $('#permission_spreadsheet').on('change', function() {
                    loadSheetsForPermission($(this).val());
                    updatePermissionPreview();
                });

                $('#permission_sheet').on('change', function() {
                    const selectedSheets = $(this).val();
                    if (selectedSheets && selectedSheets.length > 0) {
                        loadColumnsForPermission(selectedSheets);
                        $('#column_selection_section').removeClass('hidden');
                    } else {
                        $('#column_selection_section').addClass('hidden');
                    }
                    updatePermissionPreview();
                });

                // Team role events
                $('#team_role_branch, #team_role_type').on('change', function() {
                    updateTeamRolePreview();
                });

                $('#team_role_spreadsheet').on('change', function() {
                    
                });

                // $('#team_role_sheets').on('change', function() {
                //     const selectedSheets = $(this).val();
                //     if (selectedSheets && selectedSheets.length > 0) {
                //         loadColumnsForTeamRole(selectedSheets);
                //         $('#team_role_columns_section').removeClass('hidden');
                //     } else {
                //         $('#team_role_columns_section').addClass('hidden');
                //     }
                //     updateTeamRolePreview();
                // });

                // Permission action and other changes
                $('#permission_action, #target_user, #target_role, #target_team, #target_group_type').on('change', function() {
                    updatePermissionPreview();
                });

                // Handle modal close events
                $(document).on('click', '[data-modal-hide="role-modal"]', function() {
                    roleModal.hide();
                });

                $(document).on('click', '[data-modal-hide="team-role-modal"]', function() {
                    teamRoleModal.hide();
                });

                $(document).on('click', '[data-modal-hide="permission-assignment-modal"]', function() {
                    permissionAssignmentModal.hide();
                });
            }

            function loadHierarchyData() {
                $.ajax({
                    url: '{{ route('admin.system.roles-permissions.hierarchy') }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            hierarchyData = response.data;
                            populateSpreadsheetSelects();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading hierarchy data:', error);
                    }
                });
            }

            function populateSpreadsheetSelects() {
                const spreadsheetSelects = ['#permission_spreadsheet', '#team_role_spreadsheet'];
                spreadsheetSelects.forEach(selector => {
                    $(selector).empty().append('<option value="">Select Spreadsheet</option>');
                    hierarchyData.forEach(spreadsheet => {
                        $(selector).append(new Option(spreadsheet.name, spreadsheet.id));
                    });
                });
            }

            // --- TEAM ROLE MODAL LOGIC ---

            // Load sheets for selected spreadsheet (team role modal)
            $('#team_role_spreadsheet').on('change', function() {
                const spreadsheetId = $(this).val();
                const sheetsSelect = $('#team_role_sheets');
                sheetsSelect.empty().prop('disabled', true);
                $('#team_role_columns_section').addClass('hidden');
                $('#team_role_columns_container').empty();

                if (!spreadsheetId) {
                    sheetsSelect.trigger('change');
                    return;
                }

                // Find spreadsheet in hierarchyData
                const spreadsheet = hierarchyData.find(s => String(s.id) === String(spreadsheetId));
                if (!spreadsheet) return;

                spreadsheet.sheets.forEach(sheet => {
                    const label = sheet.display_name || sheet.name;
                    sheetsSelect.append(new Option(label, sheet.id));
                });
                sheetsSelect.prop('disabled', false).trigger('change');
            });

            // Load columns for selected sheets (team role modal)
            $('#team_role_sheets').on('change', function() {
                const spreadsheetId = $('#team_role_spreadsheet').val();
                const selectedSheetIds = $(this).val() || [];
                const columnsContainer = $('#team_role_columns_container');
                columnsContainer.empty();

                if (!spreadsheetId || selectedSheetIds.length === 0) {
                    $('#team_role_columns_section').addClass('hidden');
                    return;
                }

                // Find spreadsheet in hierarchyData
                const spreadsheet = hierarchyData.find(s => String(s.id) === String(spreadsheetId));
                if (!spreadsheet) return;

                // For each selected sheet, show its columns
                selectedSheetIds.forEach(sheetId => {
                    const sheet = spreadsheet.sheets.find(sh => String(sh.id) === String(sheetId));
                    if (!sheet) return;
                    const sheetLabel = sheet.display_name || sheet.name;
                    if (sheet.columns && sheet.columns.length > 0) {
                        columnsContainer.append(`<div class="font-semibold mt-2 mb-1">${sheetLabel}</div>`);
                        sheet.columns.forEach(col => {
                            columnsContainer.append(`
                                <label class="flex items-center mb-1 ml-2">
                                    <input type="checkbox" class="team-role-column-checkbox" data-sheet-id="${sheet.id}" data-column-key="${col.range}" value="${col.id}">
                                    <span class="ml-2">${col.name} <span class="text-xs text-gray-400">(${col.range})</span></span>
                                </label>
                            `);
                        });
                    }
                });
                $('#team_role_columns_section').removeClass('hidden');
            });

            // Update role permission preview for team role modal
            function updateTeamRolePreview() {
                const teamName = $('#team_role_team_name').val();
                const spreadsheetId = $('#team_role_spreadsheet').val();
                const spreadsheet = hierarchyData.find(s => String(s.id) === String(spreadsheetId));
                const spreadsheetName = spreadsheet ? spreadsheet.name : '';
                const selectedSheetIds = $('#team_role_sheets').val() || [];
                const selectedColumns = $('.team-role-column-checkbox:checked');
                let permissions = [];
                let preview = '';

                if (!teamName || !spreadsheetId) {
                    preview = 'Isi nama team dan pilih spreadsheet untuk melihat preview...';
                } else if (selectedSheetIds.length === 0) {
                    // Spreadsheet level permission only
                    permissions.push(`${slugify(teamName)}`);
                } else {
                    selectedSheetIds.forEach(sheetId => {
                        const sheet = spreadsheet.sheets.find(sh => String(sh.id) === String(sheetId));
                        if (!sheet) return;
                        const sheetName = sheet.name;
                        // Sheet level permission
                        permissions.push(`${slugify(teamName)}.${slugify(sheetName)}`);
                        // Column level
                        selectedColumns.each(function() {
                            if (String($(this).data('sheet-id')) === String(sheetId)) {
                                const colRange = $(this).data('column-key');
                                // Find column name
                                const colObj = (sheet.columns || []).find(c => String(c.range) === String(colRange));
                                const colName = colObj ? slugify(colObj.name) : 'column';
                                permissions.push(`${slugify(teamName)}.${slugify(sheetName)}.${colName}.${colRange}`);
                            }
                        });
                    });
                }

                if (permissions.length > 0) {
                    preview = permissions.map(p => `<span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs mr-1 mb-1">${p}</span>`).join('');
                } else {
                    preview = 'No permissions selected.';
                }
                $('#team_role_permission_preview').html(preview);
            }

            // Helper: slugify a string (for permission naming)
            function slugify(str) {
                return String(str).toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
            }

            // Bind updateTeamRolePreview to relevant events
            $('#team_role_team_name, #team_role_spreadsheet, #team_role_sheets, #team_role_columns_container').on('change keyup', 'select, input', function() {
                updateTeamRolePreview();
            });
            $('#team_role_sheets').on('change', updateTeamRolePreview);
            $(document).on('change', '.team-role-column-checkbox', updateTeamRolePreview);

            // Global functions that are called from DataTable action buttons
            window.manageUserRoles = function(userId) {
                currentUserId = userId;
                
                // Set user IDs in forms
                $('#assign_user_id, #team_assign_user_id').val(userId);
                
                // Load user permissions
                loadUserPermissions(userId);
                
                roleModal.show();
            }

            window.viewUserPermissions = function(userId) {
                manageUserRoles(userId);
                switchTab('current-roles');
            }

            function loadUserPermissions(userId) {
                $.ajax({
                    url: '{{ route('admin.system.roles-permissions.user-permissions', ['userId' => '__USER_ID__']) }}'.replace('__USER_ID__', userId),
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
                    const teamInfo = role.team_id ? ` (Team: ${role.team_id})` : '';
                    rolesHtml += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="badge ${badgeClass}">${role.name}${teamInfo}</span>
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
                    url: '{{ route('admin.system.roles-permissions.assign-role') }}',
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

            function assignUserToTeam() {
                const formData = $('#assignTeamForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.system.roles-permissions.assign-user-to-team') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            loadUserPermissions(currentUserId);
                            dataTable.ajax.reload(null, false);
                            $('#assignTeamForm')[0].reset();
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
                            url: '{{ route('admin.system.roles-permissions.remove-role') }}',
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

            // Global function for creating team role
            window.showCreateTeamRoleModal = function() {
                // Reset form
                $('#team_role_spreadsheet').val('').trigger('change');
                $('#team_role_columns_section').addClass('hidden');
                updateTeamRolePreview();
                
                teamRoleModal.show();
            }

            function createTeamRole() {
                const formData = $('#teamRoleForm').serialize();
                
                $.ajax({
                    url: '{{ route('admin.system.roles-permissions.create-team-role') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            teamRoleModal.hide();
                            $('#teamRoleForm')[0].reset();
                            
                            // Reload role select in main modal
                            $('#role_select').append(new Option(response.data.name, response.data.id));
                            dataTable.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            // Update role permission preview for team role modal
            function updateTeamRolePreview() {
                const teamName = $('#team_role_team_name').val();
                const spreadsheetId = $('#team_role_spreadsheet').val();
                const spreadsheet = hierarchyData.find(s => String(s.id) === String(spreadsheetId));
                const spreadsheetName = spreadsheet ? spreadsheet.name : '';
                const selectedSheetIds = $('#team_role_sheets').val() || [];
                const selectedColumns = $('.team-role-column-checkbox:checked');
                let permissions = [];
                let preview = '';

                if (!teamName || !spreadsheetId) {
                    preview = 'Isi nama team dan pilih spreadsheet untuk melihat preview...';
                } else if (selectedSheetIds.length === 0) {
                    // Spreadsheet level permission only
                    permissions.push(`${slugify(teamName)}`);
                } else {
                    selectedSheetIds.forEach(sheetId => {
                        const sheet = spreadsheet.sheets.find(sh => String(sh.id) === String(sheetId));
                        if (!sheet) return;
                        const sheetName = sheet.name;
                        // Sheet level permission
                        permissions.push(`${slugify(teamName)}.${slugify(sheetName)}`);
                        // Column level
                        selectedColumns.each(function() {
                            if (String($(this).data('sheet-id')) === String(sheetId)) {
                                const colRange = $(this).data('column-key');
                                // Find column name
                                const colObj = (sheet.columns || []).find(c => String(c.range) === String(colRange));
                                const colName = colObj ? slugify(colObj.name) : 'column';
                                permissions.push(`${slugify(teamName)}.${slugify(sheetName)}.${colName}.${colRange}`);
                            }
                        });
                    });
                }

                if (permissions.length > 0) {
                    preview = permissions.map(p => `<span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs mr-1 mb-1">${p}</span>`).join('');
                } else {
                    preview = 'No permissions selected.';
                }
                $('#team_role_permission_preview').html(preview);
            }

            function createTeamRoleWithPermissions() {
                // Ambil data dari form
                const teamName = $('#team_role_team_name').val();
                const roleName = $('#team_role_name').val();
                const spreadsheetId = $('#team_role_spreadsheet').val();
                const spreadsheet = hierarchyData.find(s => String(s.id) === String(spreadsheetId));
                const selectedSheetIds = $('#team_role_sheets').val() || [];
                const selectedColumns = $('.team-role-column-checkbox:checked');
                let customPermissions = [];

                if (!teamName || !roleName) {
                    Swal.fire('Warning', 'Please fill Team Name and Role Name', 'warning');
                    return;
                }

                if (spreadsheet && selectedSheetIds.length > 0) {
                    selectedSheetIds.forEach(sheetId => {
                        const sheet = spreadsheet.sheets.find(sh => String(sh.id) === String(sheetId));
                        if (!sheet) return;
                        const sheetName = sheet.name;
                        // Sheet level permission
                        customPermissions.push(`${slugify(teamName)}.${slugify(sheetName)}`);
                        // Column level
                        selectedColumns.each(function() {
                            if (String($(this).data('sheet-id')) === String(sheetId)) {
                                const colRange = $(this).data('column-key');
                                const colObj = (sheet.columns || []).find(c => String(c.range) === String(colRange));
                                const colName = colObj ? slugify(colObj.name) : 'column';
                                customPermissions.push(`${slugify(teamName)}.${slugify(sheetName)}.${colName}.${colRange}`);
                            }
                        });
                    });
                } else {
                    // Default: team level permission
                    customPermissions.push(`${slugify(teamName)}`);
                }

                $.ajax({
                    url: '{{ route('admin.system.roles-permissions.create-team-role') }}',
                    type: 'POST',
                    data: {
                        team_name: teamName,
                        role_name: roleName,
                        custom_permissions: JSON.stringify(customPermissions)
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            teamRoleModal.hide();
                            $('#teamRoleForm')[0].reset();
                            $('#team_role_permission_preview').html('Configure role to see permissions preview...');
                            $('#team_role_columns_section').addClass('hidden');
                            $('#role_select').append(new Option(response.data.name, response.data.id));
                            dataTable.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            // Update event binding for team role preview
            $('#team_role_branch, #team_role_name').on('change keyup', function() {
                updateTeamRolePreview();
            });

            // Permission assignment functions (will be implemented later)
            function showPermissionAssignmentModal() {
                // Reset form
                $('#assign_target_type').val('').trigger('change');
                $('#permission_spreadsheet').val('').trigger('change');
                $('#permission_action').val('read');
                $('.column-checkbox').prop('checked', false);
                updatePermissionPreview();
                
                permissionAssignmentModal.show();
            }

            function updatePermissionPreview() {
                const targetType = $('#assign_target_type').val();
                const action = $('#permission_action').val();
                const spreadsheetId = $('#permission_spreadsheet').val();
                const sheetIds = $('#permission_sheet').val();
                const selectedColumns = $('.column-checkbox:checked');
                
                let permissions = [];
                let preview = '';
                
                if (!targetType) {
                    preview = 'Select target type to see permissions preview...';
                } else {
                    if (spreadsheetId && (!sheetIds || sheetIds.length === 0)) {
                        // Spreadsheet level permission
                        permissions.push(`spreadsheet.${spreadsheetId}.${action}`);
                    } else if (sheetIds && sheetIds.length > 0) {
                        // Sheet level permissions - use sheet names instead of IDs
                        sheetIds.forEach(sheetId => {
                            const sheetOption = $(`#permission_sheet option[value="${sheetId}"]`);
                            const sheetName = sheetOption.text().split(' (')[0]; // Remove branch info
                            const sheetSlug = sheetName.toLowerCase().replace(/\s+/g, '_');
                            
                            permissions.push(`sheet.${sheetSlug}.${action}`);
                            
                            // Column level permissions if any selected
                            selectedColumns.each(function() {
                                if ($(this).data('sheet-id') == sheetId) {
                                    const columnKey = $(this).data('column-key');
                                    permissions.push(`sheet.${sheetSlug}.column.${columnKey}.${action}`);
                                }
                            });
                        });
                    }
                    
                    if (permissions.length > 0) {
                        preview = permissions.map(p => `<span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs mr-1 mb-1">${p}</span>`).join('');
                    } else {
                        preview = 'Configure selections to generate permissions...';
                    }
                }
                
                $('#permission_preview').html(preview);
            }

            function assignPermissions() {
                const targetType = $('#assign_target_type').val();
                const action = $('#permission_action').val();
                const spreadsheetId = $('#permission_spreadsheet').val();
                const sheetIds = $('#permission_sheet').val();
                const selectedColumns = $('.column-checkbox:checked');
                
                if (!targetType) {
                    Swal.fire('Warning', 'Please select a target type', 'warning');
                    return;
                }
                
                // Generate permissions array
                let permissions = [];
                
                if (spreadsheetId && (!sheetIds || sheetIds.length === 0)) {
                    permissions.push(`spreadsheet.${spreadsheetId}.${action}`);
                } else if (sheetIds && sheetIds.length > 0) {
                    sheetIds.forEach(sheetId => {
                        const sheetOption = $(`#permission_sheet option[value="${sheetId}"]`);
                        const sheetName = sheetOption.text().split(' (')[0];
                        const sheetSlug = sheetName.toLowerCase().replace(/\s+/g, '_');
                        
                        permissions.push(`sheet.${sheetSlug}.${action}`);
                        
                        selectedColumns.each(function() {
                            if ($(this).data('sheet-id') == sheetId) {
                                const columnKey = $(this).data('column-key');
                                permissions.push(`sheet.${sheetSlug}.column.${columnKey}.${action}`);
                            }
                        });
                    });
                }
                
                if (permissions.length === 0) {
                    Swal.fire('Warning', 'Please select at least one permission to assign', 'warning');
                    return;
                }
                
                // Prepare request data based on target type
                let requestData = { permissions: permissions };
                let endpoint = '';
                
                switch (targetType) {
                    case 'user':
                        const userId = $('#target_user').val();
                        if (!userId) {
                            Swal.fire('Warning', 'Please select a user', 'warning');
                            return;
                        }
                        requestData.user_id = userId;
                        endpoint = '{{ route('admin.system.roles-permissions.assign-permission-to-user') }}';
                        break;
                        
                    case 'role':
                        const roleId = $('#target_role').val();
                        if (!roleId) {
                            Swal.fire('Warning', 'Please select a role', 'warning');
                            return;
                        }
                        requestData.role_id = roleId;
                        endpoint = '{{ route('admin.system.roles-permissions.assign-permission-to-role') }}';
                        break;
                        
                    case 'group':
                        const teamId = $('#target_team').val();
                        const groupType = $('#target_group_type').val();
                        if (!teamId || !groupType) {
                            Swal.fire('Warning', 'Please select team and group type', 'warning');
                            return;
                        }
                        requestData.team_id = teamId;
                        requestData.group_type = groupType;
                        endpoint = '{{ route('admin.system.roles-permissions.assign-permission-to-group') }}';
                        break;
                }
                
                // Send request
                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    data: requestData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            permissionAssignmentModal.hide();
                            dataTable.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr, status, error) {
                        AdminUtils.handleAjaxError(xhr, status, error);
                    }
                });
            }

            // Update the showPermissionAssignmentModal function
            function showPermissionAssignmentModal() {
                // Reset form
                $('#assign_target_type').val('').trigger('change');
                $('#permission_spreadsheet').val('').trigger('change');
                $('#permission_action').val('read');
                $('.column-checkbox').prop('checked', false);
                updatePermissionPreview();
                
                permissionAssignmentModal.show();
            }
        </script>
    @endpush
</x-admin-app-layout>

