<div id="sidebar"
    class="fixed left-0 top-0 h-full bg-gray-800 text-white transition-all duration-300 ease-in-out z-10 sidebar-collapsed"
    style="padding-top: var(--nav-height, 120px);">
    
    <!-- Sidebar Content -->
    <div class="overflow-y-auto h-full pt-2 pb-4">
        <!-- Toggle Button - Centered at top -->
        <div class="flex justify-center mb-4">
            <button id="sidebarToggle"
                class="bg-gray-700 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-600 transition-all duration-200 shadow-md border border-gray-600">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>

        <nav class="space-y-1">
            <!-- Dashboard -->
            <div class="sidebar-item mx-3">
                <a href="#"
                    class="sidebar-link flex items-center h-12 px-4 rounded-lg hover:bg-gray-700 transition-all duration-200 group">
                    <i
                        class="fas fa-home text-lg sidebar-icon text-gray-300 group-hover:text-white transition-colors"></i>
                    <span class="sidebar-text ml-3 whitespace-nowrap font-medium">Dashboard</span>
                </a>
            </div>

            <!-- Menu Header: Master Data -->
            <div class="sidebar-header mt-6 mb-2">
                <h3 class="sidebar-text text-gray-400 text-xs uppercase tracking-wider font-semibold px-4 py-2">Master Data</h3>
                <div class="sidebar-collapsed-only mx-4 border-t border-gray-600"></div>
            </div>

            <!-- Master Data (Submenu) -->
            <div class="sidebar-item px-3">
                <button
                    class="sidebar-toggle w-full flex items-center h-12 px-4 rounded-lg hover:bg-gray-700 transition-all duration-200 text-left group"
                    data-target="masterdata">
                    <i class="fas fa-database text-lg sidebar-icon text-gray-300 group-hover:text-white transition-colors"></i>
                    <span class="sidebar-text ml-3 whitespace-nowrap flex-1 font-medium">Master Data</span>
                    <i class="fas fa-chevron-down sidebar-text text-xs transition-all duration-200 sidebar-arrow text-gray-400 group-hover:text-white"></i>
                </button>
                <div class="sidebar-submenu hidden mt-1" id="submenu-masterdata">
                    <a href="{{ route('admin.master-data.pegawai.index') }}"
                        class="sidebar-link flex items-center h-10 pl-14 pr-4 mx-2 rounded-lg hover:bg-gray-600 transition-all duration-200 group">
                        <i class="fas fa-user-tie text-sm mr-3 text-gray-400 group-hover:text-blue-300 transition-colors"></i>
                        <span class="sidebar-text text-sm text-gray-300 group-hover:text-white">Pegawai</span>
                    </a>
                    <a href="{{ route('admin.master-data.cabang.index') }}"
                        class="sidebar-link flex items-center h-10 pl-14 pr-4 mx-2 rounded-lg hover:bg-gray-600 transition-all duration-200 group">
                        <i class="fas fa-building text-sm mr-3 text-gray-400 group-hover:text-blue-300 transition-colors"></i>
                        <span class="sidebar-text text-sm text-gray-300 group-hover:text-white">Cabang</span>
                    </a>
                    <a href="{{ route('admin.master-data.spreadsheet.index') }}"
                        class="sidebar-link flex items-center h-10 pl-14 pr-4 mx-2 rounded-lg hover:bg-gray-600 transition-all duration-200 group">
                        <i class="fas fa-table text-sm mr-3 text-gray-400 group-hover:text-blue-300 transition-colors"></i>
                        <span class="sidebar-text text-sm text-gray-300 group-hover:text-white">Spreadsheet</span>
                    </a>
                    <a href="{{ route('admin.master-data.spreadsheet-sheet.index') }}"
                        class="sidebar-link flex items-center h-10 pl-14 pr-4 mx-2 rounded-lg hover:bg-gray-600 transition-all duration-200 group">
                        <i class="fas fa-file-alt text-sm mr-3 text-gray-400 group-hover:text-blue-300 transition-colors"></i>
                        <span class="sidebar-text text-sm text-gray-300 group-hover:text-white">Sheet Config</span>
                    </a>
                    <a href="{{ route('admin.master-data.spreadsheet-column.index') }}"
                        class="sidebar-link flex items-center h-10 pl-14 pr-4 mx-2 rounded-lg hover:bg-gray-600 transition-all duration-200 group">
                        <i class="fas fa-columns text-sm mr-3 text-gray-400 group-hover:text-blue-300 transition-colors"></i>
                        <span class="sidebar-text text-sm text-gray-300 group-hover:text-white">Column Config</span>
                    </a>
                </div>
            </div>

            <!-- Menu Header: Spreadsheet Management -->
            <div class="sidebar-header mt-6 mb-2">
                <h3 class="sidebar-text text-gray-400 text-xs uppercase tracking-wider font-semibold px-4 py-2">Spreadsheet Management</h3>
                <div class="sidebar-collapsed-only mx-4 border-t border-gray-600"></div>
            </div>

            <!-- Spreadsheet Management (Submenu) -->
            <div class="sidebar-item mx-3">
                <button
                    class="sidebar-toggle w-full flex items-center h-12 px-4 rounded-lg hover:bg-gray-700 transition-all duration-200 text-left group"
                    data-target="spreadsheet">
                    <i class="fas fa-file-excel text-lg sidebar-icon text-gray-300 group-hover:text-white transition-colors"></i>
                    <span class="sidebar-text ml-3 whitespace-nowrap flex-1 font-medium">Spreadsheet</span>
                    <i class="fas fa-chevron-down sidebar-text text-xs transition-all duration-200 sidebar-arrow text-gray-400 group-hover:text-white"></i>
                </button>
                <div class="sidebar-submenu hidden mt-1" id="submenu-spreadsheet">
                    <a href="{{ route('admin.spreadsheet.reader') }}"
                        class="sidebar-link flex items-center h-10 pl-14 pr-4 mx-2 rounded-lg hover:bg-gray-600 transition-all duration-200 group">
                        <i class="fas fa-eye text-sm mr-3 text-gray-400 group-hover:text-green-300 transition-colors"></i>
                        <span class="sidebar-text text-sm text-gray-300 group-hover:text-white">Reader</span>
                    </a>
                </div>
            </div>
            {{-- ...existing code... --}}
        </nav>
    </div>
</div>

<style>
    /* Sidebar States */
    .sidebar-collapsed {
        width: 60px;
    }

    .sidebar-expanded {
        width: 250px;
    }

    /* Icon Only Mode - Hide all text and center icons */
    .sidebar-collapsed .sidebar-text {
        display: none;
    }

    .sidebar-collapsed .sidebar-link,
    .sidebar-collapsed .sidebar-toggle {
        justify-content: center;
        margin-left: 0;
        margin-right: 0;
        padding-left: 0;
        padding-right: 0;
    }

    .sidebar-collapsed .sidebar-icon {
        margin: 0;
    }

    .sidebar-collapsed .sidebar-submenu {
        display: none !important;
    }

    .sidebar-collapsed .sidebar-header {
        display: none;
    }

    .sidebar-collapsed .sidebar-collapsed-only {
        display: block;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    /* Expanded Mode */
    .sidebar-expanded .sidebar-text {
        display: block;
    }

    .sidebar-expanded .sidebar-collapsed-only {
        display: none;
    }

    /* Hover Behavior for Collapsed */
    .sidebar-collapsed:hover {
        width: 250px;
    }

    .sidebar-collapsed:hover .sidebar-text {
        display: block;
    }

    .sidebar-collapsed:hover .sidebar-link,
    .sidebar-collapsed:hover .sidebar-toggle {
        justify-content: flex-start;
        /* margin-left: 0.5rem;
        margin-right: 0.5rem;
        padding-left: 1rem;
        padding-right: 1rem; */
    }

    .sidebar-collapsed:hover .sidebar-icon {
        margin-right: 0;
    }

    .sidebar-collapsed:hover .sidebar-submenu.show {
        display: block !important;
    }

    .sidebar-collapsed:hover .sidebar-header {
        display: block;
    }

    .sidebar-collapsed:hover .sidebar-collapsed-only {
        display: none;
    }

    /* Active States */
    .sidebar-link.active,
    .sidebar-toggle.active {
        background-color: #1e40af;
        color: #ffffff;
        box-shadow: 0 2px 4px rgba(30, 64, 175, 0.3);
    }

    .sidebar-link.active i,
    .sidebar-toggle.active i {
        color: #ffffff;
    }

    /* Submenu active state */
    .sidebar-submenu .sidebar-link.active {
        background-color: #3b82f6;
        border-left: 3px solid #60a5fa;
    }

    /* Show State for Submenus */
    .sidebar-submenu.show {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    .sidebar-toggle.show {
        background-color: #374151;
    }

    .sidebar-toggle.show .sidebar-arrow {
        transform: rotate(180deg);
    }

    /* Toggle Button Animation - Update for new position */
    .sidebar-collapsed #sidebarToggle i {
        transform: rotate(0deg);
    }
    
    .sidebar-expanded #sidebarToggle i {
        transform: rotate(180deg);
    }

    /* Hide toggle button container when collapsed and not hovered */
    .sidebar-collapsed .sidebar-text {
        display: none;
    }
    
    .sidebar-collapsed:not(:hover) #sidebarToggle {
        width: 32px;
        height: 32px;
    }

    .sidebar-expanded #sidebarToggle,
    .sidebar-collapsed:hover #sidebarToggle {
        width: 32px;
        height: 32px;
    }

    /* Animations */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Smooth transitions */
    .sidebar-link,
    .sidebar-toggle,
    .sidebar-submenu,
    .sidebar-arrow {
        transition: all 0.3s ease;
    }

    /* Menu spacing */
    .sidebar-item:not(:last-child) {
        margin-bottom: 2px;
    }

    /* Header styling */
    .sidebar-header {
        position: relative;
    }

    .sidebar-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 1px;
        background: linear-gradient(to right, transparent, #4b5563, transparent);
        opacity: 0.3;
    }

    .sidebar-collapsed .sidebar-header::after {
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContent');

        function updateMainContentMargin() {
            const isExpanded = sidebar.classList.contains('sidebar-expanded');
            
            if (isExpanded) {
                mainContent.classList.remove('ml-15');
                mainContent.classList.add('ml-64');
            } else {
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-15');
            }
        }

        // Get saved state from localStorage
        const isExpanded = localStorage.getItem('sidebarExpanded') === 'true';

        // Set initial state
        if (isExpanded) {
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
        } else {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-collapsed');
        }

        updateMainContentMargin();

        // Toggle functionality
        toggleBtn.addEventListener('click', function() {
            const isCurrentlyExpanded = sidebar.classList.contains('sidebar-expanded');

            if (isCurrentlyExpanded) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                localStorage.setItem('sidebarExpanded', 'false');
                // Close all submenus when collapsing
                document.querySelectorAll('.sidebar-submenu').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.sidebar-toggle').forEach(btn => {
                    btn.classList.remove('show');
                });
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                localStorage.setItem('sidebarExpanded', 'true');
            }

            updateMainContentMargin();
        });

        // Remove hover effects that cause layout shift
        // Only show expanded content on hover for collapsed sidebar without affecting main content
        sidebar.addEventListener('mouseenter', function() {
            if (sidebar.classList.contains('sidebar-collapsed')) {
                // Don't change main content margin on hover
                // sidebar.style.width = '250px';
            }
        });

        sidebar.addEventListener('mouseleave', function() {
            if (sidebar.classList.contains('sidebar-collapsed')) {
                // Don't change main content margin on hover leave
                // sidebar.style.width = '60px';
                document.querySelectorAll('.sidebar-submenu').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.sidebar-toggle').forEach(btn => {
                    btn.classList.remove('show');
                });
            }
        });

        // Submenu toggle functionality
        document.querySelectorAll('.sidebar-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const submenu = document.getElementById(`submenu-${targetId}`);
                const isShow = submenu.classList.contains('show');

                // Close all other submenus
                document.querySelectorAll('.sidebar-submenu').forEach(menu => {
                    if (menu !== submenu) {
                        menu.classList.remove('show');
                    }
                });

                document.querySelectorAll('.sidebar-toggle').forEach(btn => {
                    if (btn !== this) {
                        btn.classList.remove('show');
                    }
                });

                // Toggle current submenu
                if (isShow) {
                    submenu.classList.remove('show');
                    this.classList.remove('show');
                } else {
                    submenu.classList.add('show');
                    this.classList.add('show');
                }
            });
        });

        // Set active states based on current URL
        const currentUrl = window.location.pathname;
        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (link.getAttribute('href') === currentUrl) {
                link.classList.add('active');

                // If it's a submenu item, also show its parent
                const parentSubmenu = link.closest('.sidebar-submenu');
                if (parentSubmenu) {
                    parentSubmenu.classList.add('show');
                    const parentToggle = parentSubmenu.previousElementSibling;
                    if (parentToggle && parentToggle.classList.contains('sidebar-toggle')) {
                        parentToggle.classList.add('show', 'active');
                    }
                }
            }
        });
    });
</script>
