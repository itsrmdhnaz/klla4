<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- DataTables CSS with Bootstrap 5 styling -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Select2 CSS - Default styling only -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --nav-height: 120px;
            /* fallback value */
        }

        body {
            padding-top: var(--nav-height);
        }

        /* Only keep essential DataTables and component styles */
        .dataTables_wrapper {
            padding: 20px 0;
            width: 100% !important;
        }
        
        .dataTables_wrapper .row {
            width: 100% !important;
            margin: 0 !important;
        }
        
        .dataTables_wrapper .col-sm-12 {
            width: 100% !important;
        }
        
        .dataTables_filter {
            margin-bottom: 20px;
            text-align: right;
        }
        
        .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 12px;
            margin-left: 8px;
            width: 250px !important;
        }
        
        .dataTables_length {
            text-align: left;
        }
        
        .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 6px 10px;
            margin: 0 8px;
        }
        
        .dataTables_info {
            text-align: left;
            padding-top: 10px;
        }
        
        .dataTables_paginate {
            text-align: right;
            padding-top: 10px;
        }
        
        table.dataTable {
            width: 100% !important;
            margin: 0 !important;
            clear: both;
        }
        
        table.dataTable thead th,
        table.dataTable tbody td {
            white-space: nowrap;
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) > td {
            background-color: #f8f9fa;
        }
        
        .table > :not(caption) > * > * {
            padding: 12px 8px;
            border-bottom-width: 1px;
            border-color: #e5e7eb;
        }
        
        .table thead th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #374151;
            border-color: #e5e7eb;
            text-align: left;
        }

        /* Fix responsive issues */
        @media (max-width: 768px) {
            .dataTables_filter,
            .dataTables_length,
            .dataTables_info,
            .dataTables_paginate {
                text-align: center;
                margin-bottom: 10px;
            }
            
            .dataTables_filter input {
                width: 100% !important;
                max-width: 200px;
            }
            
            table.dataTable thead th,
            table.dataTable tbody td {
                white-space: normal;
                word-wrap: break-word;
            }
        }

        /* Container fixes */
        .container-fluid {
            width: 100% !important;
            padding-left: 15px;
            padding-right: 15px;
        }

        .max-w-7xl {
            max-width: none !important;
            width: 100% !important;
        }

        /* Common Button Styles */
        .btn-action {
            padding: 6px 10px;
            margin: 0 2px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background-color: #f59e0b;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #d97706;
        }
        
        .btn-delete {
            background-color: #ef4444;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #dc2626;
        }
        
        /* Common Badge Styles */
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6b7280;
            color: white;
        }
        
        .badge-success {
            background-color: #10b981;
            color: white;
        }
        
        .badge-danger {
            background-color: #ef4444;
            color: white;
        }

        /* Remove custom Select2 styling - use default */
        .select2-container {
            width: 100% !important;
        }

        /* Only keep essential Select2 styling */
        .select2-container.is-invalid .select2-selection {
            border-color: #ef4444;
        }

        /* Enhanced Select2 styling for tags */
        .select2-container .select2-selection--single .select2-selection__rendered {
            color: #374151;
        }

        .select2-container .select2-results__option[data-select2-tag="true"] {
            background-color: #dbeafe;
            border-left: 3px solid #3b82f6;
        }

        .select2-container .select2-results__option--highlighted[data-select2-tag="true"] {
            background-color: #3b82f6;
            color: white;
        }

        /* Select2 tags styling */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e5e7eb;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #6b7280;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ef4444;
        }

        /* Common Form Styles */
        .form-control.is-invalid {
            border-color: #ef4444 !important;
        }

        .invalid-feedback {
            display: none;
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback.show {
            display: block;
        }

        /* Common Loading Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Page specific styles can be added here */
        @stack('styles')
    </style>
</head>

<body class="font-sans antialiased">
    <x-admin-navigation />
    <x-admin-sidebar />

    <div id="mainContent" class="bg-gray-100 transition-all duration-300 ease-in-out ml-15">
        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="p-6">
            {{ $slot }}
        </main>
    </div>

    <!-- Common Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Common JavaScript functions and configurations
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Common DataTable language configuration
            window.dataTableLanguage = {
                processing: "Memuat data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                emptyTable: "Tidak ada data tersedia",
                zeroRecords: "Tidak ada data yang cocok",
                loadingRecords: "Memuat...",
                aria: {
                    sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                    sortDescending: ": aktifkan untuk mengurutkan kolom turun"
                }
            };

            // Common Select2 language configuration
            window.select2Language = {
                noResults: function() {
                    return "Tidak ada data yang ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                },
                removeAllItems: function() {
                    return "Hapus semua item";
                }
            };
        });

        // Common utility functions
        window.AdminUtils = {
            // Show loading overlay
            showLoading: function() {
                if (!document.querySelector('.loading-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'loading-overlay';
                    overlay.innerHTML = '<div class="loading-spinner"></div>';
                    document.body.appendChild(overlay);
                }
            },

            // Hide loading overlay
            hideLoading: function() {
                const overlay = document.querySelector('.loading-overlay');
                if (overlay) {
                    overlay.remove();
                }
            },

            // Clear validation errors
            clearValidationErrors: function(form) {
                $(form + ' input, ' + form + ' select').removeClass('border-red-500 focus:ring-red-500 focus:border-red-500')
                    .addClass('border-gray-300 focus:ring-blue-500 focus:border-blue-500');
                
                // Clear Select2 error states
                $('.select2-container').removeClass('is-invalid');
                
                $('.invalid-feedback').addClass('hidden').text('');
            },

            // Show validation errors
            showValidationErrors: function(errors, form) {
                this.clearValidationErrors(form);
                console.log('Validation errors:', errors); // Debug log
                
                $.each(errors, function(field, messages) {
                    console.log('Processing field:', field, 'messages:', messages); // Debug log
                    
                    if (field === 'branches' || field === 'primary_branch' || $(form + ' #' + field).hasClass('select2-hidden-accessible')) {
                        // Handle Select2 validation errors
                        const select2Container = $(form + ' #' + field).next('.select2-container');
                        select2Container.addClass('is-invalid');
                        
                        const feedback = $(form + ' #' + field).parent().find('.invalid-feedback');
                        if (feedback.length) {
                            feedback.removeClass('hidden').addClass('show').text(messages[0]);
                        }
                    } else {
                        // Handle regular input validation errors
                        const input = $(form + ' #' + field);
                        const feedback = input.parent().find('.invalid-feedback');
                        
                        if (input.length && feedback.length) {
                            input.removeClass('border-gray-300 focus:ring-blue-500 focus:border-blue-500')
                                .addClass('border-red-500 focus:ring-red-500 focus:border-red-500');
                            feedback.removeClass('hidden').addClass('show').text(messages[0]);
                        }
                    }
                });
            },

            // Initialize Select2 with minimal options
            initSelect2: function(selector, options = {}) {
                const defaultOptions = {
                    allowClear: true,
                    width: '100%',
                    language: window.select2Language
                };
                
                return $(selector).select2($.extend(defaultOptions, options));
            },

            // Common AJAX error handler
            handleAjaxError: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                
                if (xhr.status === 422) {
                    // Validation errors - let the calling function handle this
                    return xhr.responseJSON.errors;
                } else if (xhr.status === 403) {
                    Swal.fire('Akses Ditolak!', 'Anda tidak memiliki izin untuk melakukan aksi ini.', 'error');
                } else if (xhr.status === 404) {
                    Swal.fire('Tidak Ditemukan!', 'Data yang diminta tidak ditemukan.', 'error');
                } else if (xhr.status === 500) {
                    Swal.fire('Error Server!', 'Terjadi kesalahan pada server. Silakan coba lagi.', 'error');
                } else {
                    Swal.fire('Error!', 'Terjadi kesalahan: ' + error, 'error');
                }
                
                return null;
            }
        };
    </script>

    <!-- Page specific scripts -->
    @stack('scripts')
</body>

</html>
