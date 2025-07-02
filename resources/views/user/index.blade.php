<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Leads</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.34.0/tabler-icons.min.css"
        integrity="sha512-MsO/oEO313SeWk87bUIzVZBnm8v7BK0/02G6e1YaJd6D1/yM7+rLASTnKpnbV8Qf9mrOxVN+o5REX2ix85FyJw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.7.0/apexcharts.min.js"
        integrity="sha512-xHtgQEymzQrphqglnjawC6hqXjIlzaGwK4h5xfYKIr/rM5CI9RXazkGZHzn5lIBxzU7vCb8uacAe5ACUGyz/Ow=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover,
        .flatpickr-day.selected.prevMonthDay,
        .flatpickr-day.startRange.prevMonthDay,
        .flatpickr-day.endRange.prevMonthDay,
        .flatpickr-day.selected.nextMonthDay,
        .flatpickr-day.startRange.nextMonthDay,
        .flatpickr-day.endRange.nextMonthDay {
            background: #9bd69e;
            -webkit-box-shadow: none;
            box-shadow: none;
            color: #fff;
            border-color: #9bd69e;
        }

        .flatpickr-day.today {
            border-color: #9bd69e;
        }

        .flatpickr-day.today:hover,
        .flatpickr-day.today:focus {
            border-color: #9bd69e;
            background: #9bd69e;
            color: #fff
        }

        /* Loading animation styles */
        .loading-overlay {
            backdrop-filter: blur(2px);
            transition: opacity 0.3s ease-in-out;
            z-index: 10;
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Smooth chart transitions */
        .apexcharts-canvas {
            transition: opacity 0.3s ease-in-out;
        }

        /* Enhanced input styling */
        #flatpickr-range {
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        #flatpickr-range:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Improved loading state dalam card */
        .chart-container {
            position: relative;
            min-height: 375px;
        }

        .chart-container .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .hidden {
            display: none !important;
        }

        /* Error state styling */
        .error-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            backdrop-filter: blur(2px);
        }

        .error-text {
            color: #dc2626;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
        }

        .retry-button {
            background: #ef4444;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .retry-button:hover {
            background: #dc2626;
        }

        /* Empty state styling */
        .empty-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(156, 163, 175, 0.1);
            border: 2px dashed #9ca3af;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .empty-text {
            color: #6b7280;
            font-weight: 500;
            text-align: center;
        }
    </style>

</head>

<body class="font-sans">

    <!-- Div Header -->
    <div class="bg-[#9bd69e] py-4 px-10 mb-5 rounded-b-[2rem] flex items-center justify-between">
        <div class="text-[#1a1a1a] text-5xl"><i class="ti ti-user-question"></i></div>
        <div class="text-[2rem] font-bold text-[#1a1a1a]">Monitoring Leads</div>
        <div class="text-[#1a1a1a] text-5xl"><i class="ti ti-heart-rate-monitor"></i></div>
    </div>

    <!-- Div Layout -->
    <div class="flex gap-4 px-4 leading-6">

        <div class="flex-1">
            <div class="rounded-lg bg-[#9bd69e] p-4">
                <!-- Chart 1 Container dengan Loading dan Error -->
                <div class="chart-container">
                    <div id="pieChart1"></div>

                    <!-- Loading overlay untuk chart 1 -->
                    <div id="chart1-loading" class="hidden loading-overlay">
                        <div class="w-12 h-12 mb-3 border-b-2 border-green-600 rounded-full loading-spinner"></div>
                        <span class="text-sm font-medium text-gray-600">Memuat Payment Methods...</span>
                    </div>

                    <!-- Error overlay untuk chart 1 -->
                    <div id="chart1-error" class="hidden error-overlay">
                        <div class="error-text">
                            <i class="mb-2 text-2xl ti ti-alert-triangle"></i>
                            <div>Gagal memuat data Payment Methods</div>
                        </div>
                        <button class="retry-button" onclick="retryLoadData()">
                            <i class="mr-1 ti ti-refresh"></i>Coba Lagi
                        </button>
                    </div>

                    <!-- Empty overlay untuk chart 1 -->
                    <div id="chart1-empty" class="hidden empty-overlay">
                        <div class="empty-text">
                            <i class="mb-2 text-3xl ti ti-database-off"></i>
                            <div>Tidak ada data Payment Methods</div>
                            <div class="mt-1 text-sm">untuk rentang tanggal yang dipilih</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center gap-2 p-2 mb-2 bg-white rounded-lg" id="cash-label">
                        <i class="text-4xl ti ti-cash"></i>
                        <span>Cash 0%</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 mb-2 bg-white rounded-lg" id="credit-label">
                        <i class="text-4xl ti ti-wallet"></i>
                        <span>Credit 0%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-2" bis_skin_checked="1">
            <div class="flex gap-4 mb-4 justify-evenly">
                <!-- Div Pie Chart 2 -->
                <div class="flex-1 rounded-lg bg-[#c8e6ca] p-4">
                    <div class="chart-container">
                        <div id="pieChart2"></div>

                        <!-- Loading overlay untuk chart 2 -->
                        <div id="chart2-loading" class="hidden loading-overlay">
                            <div class="w-10 h-10 mb-2 border-b-2 border-green-600 rounded-full loading-spinner"></div>
                            <span class="text-xs font-medium text-gray-600">Memuat Programs...</span>
                        </div>

                        <!-- Error overlay untuk chart 2 -->
                        <div id="chart2-error" class="hidden error-overlay">
                            <div class="error-text">
                                <i class="mb-2 text-2xl ti ti-alert-triangle"></i>
                                <div>Gagal memuat data Programs</div>
                            </div>
                            <button class="retry-button" onclick="retryLoadData()">
                                <i class="mr-1 ti ti-refresh"></i>Coba Lagi
                            </button>
                        </div>

                        <!-- Empty overlay untuk chart 2 -->
                        <div id="chart2-empty" class="hidden empty-overlay">
                            <div class="empty-text">
                                <i class="mb-1 text-2xl ti ti-database-off"></i>
                                <div class="text-sm">Tidak ada data Programs</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Div Pie Chart 3 -->
                <div class="flex-1 rounded-lg bg-[#c8e6ca] p-4">
                    <div class="chart-container">
                        <div id="pieChart3"></div>

                        <!-- Loading overlay untuk chart 3 -->
                        <div id="chart3-loading" class="hidden loading-overlay">
                            <div class="w-10 h-10 mb-2 border-b-2 border-green-600 rounded-full loading-spinner"></div>
                            <span class="text-xs font-medium text-gray-600">Memuat Models...</span>
                        </div>

                        <!-- Error overlay untuk chart 3 -->
                        <div id="chart3-error" class="hidden error-overlay">
                            <div class="error-text">
                                <i class="mb-2 text-2xl ti ti-alert-triangle"></i>
                                <div>Gagal memuat data Models</div>
                            </div>
                            <button class="retry-button" onclick="retryLoadData()">
                                <i class="mr-1 ti ti-refresh"></i>Coba Lagi
                            </button>
                        </div>

                        <!-- Empty overlay untuk chart 3 -->
                        <div id="chart3-empty" class="hidden empty-overlay">
                            <div class="empty-text">
                                <i class="mb-1 text-2xl ti ti-database-off"></i>
                                <div class="text-sm">Tidak ada data Models</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Div Line Chart -->
            <div class="rounded-lg bg-[#c8e6ca] p-4">
                <div class="chart-container">
                    <div id="lineChart"></div>

                    <!-- Loading overlay untuk line chart -->
                    <div id="line-chart-loading" class="hidden loading-overlay">
                        <div class="w-12 h-12 mb-3 border-b-2 border-green-600 rounded-full loading-spinner"></div>
                        <span class="text-sm font-medium text-gray-600">Memuat Status</span>
                    </div>

                    <!-- Error overlay untuk line chart -->
                    <div id="line-chart-error" class="hidden error-overlay">
                        <div class="error-text">
                            <i class="mb-2 text-2xl ti ti-alert-triangle"></i>
                            <div>Gagal memuat data Status</div>
                        </div>
                        <button class="retry-button" onclick="retryLoadData()">
                            <i class="mr-1 ti ti-refresh"></i>Coba Lagi
                        </button>
                    </div>

                    <!-- Empty overlay untuk line chart -->
                    <div id="line-chart-empty" class="hidden empty-overlay">
                        <div class="empty-text">
                            <i class="mb-2 text-3xl ti ti-database-off"></i>
                            <div>Tidak ada data Status</div>
                            <div class="mt-1 text-sm">untuk rentang tanggal yang dipilih</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Div Calendar -->
        <div class="">
            <!-- <div class="flex-1"> -->
            <div class="rounded-lg bg-[#9bd69e] p-4 relative">
                <div class="mb-2 text-2xl font-semibold text-center">Filter</div>

                <!-- Date Range Input -->
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="text" id="flatpickr-range"
                        class="w-full px-4 py-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                        placeholder="Pilih rentang tanggal..." readonly />
                </div>

                <!-- tempat kalender dirender -->
                <div id="flatpickr-range-container" class="flex justify-center w-full p-2 rounded-lg shadow"></div>

                <!-- Sales Select -->
                <div class="mt-2 mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Sales</label>
                    <select id="sales-select"
                        class="w-full px-4 py-2 text-gray-700 transition-all duration-300 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                        <option value="">Semua Sales</option>
                    </select>
                </div>

                <!-- Cancel button - hanya tampil ketika sedang loading -->
                <div id="cancel-loading-btn" class="hidden mt-4">
                    <button onclick="cancelAllRequests()"
                        class="w-full px-4 py-2 text-white transition-colors duration-200 bg-red-500 rounded-lg hover:bg-red-600">
                        <i class="mr-2 ti ti-x"></i>Batalkan
                    </button>
                </div>

                <!-- Loading overlay untuk calendar section -->
                <div id="calendar-loading"
                    class="absolute inset-0 hidden bg-white rounded-lg bg-opacity-80 loading-overlay">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="w-8 h-8 mb-2 border-b-2 border-green-600 rounded-full loading-spinner"></div>
                        <span class="text-sm text-gray-600">Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Global variables untuk debouncing dan request cancellation
        let debounceTimer = null;
        let activeRequests = {
            unified: null // Hanya satu request sekarang
        };
        let isUpdatingCharts = false;

        // Tambahan: Variabel untuk menyimpan tanggal sebelumnya
        let previousDateRange = null;
        let flatpickrInstance = null;

        // Tambahan: Tracking untuk completion - sekarang hanya 1 request
        let completedRequestsCount = 0;
        let totalRequestsExpected = 1; // Hanya 1 request unified
        let hasAnyRequestCompleted = false;

        // Set default date range to current month
        const today = new Date();

        // Fix: Gunakan cara yang lebih eksplisit untuk menghitung first dan last day
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth(); // 0-based (0 = January, 6 = July)

        const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
        const lastDayOfMonth = new Date(currentYear, currentMonth + 1,
            0); // Day 0 of next month = last day of current month

        console.log('Today:', today.toISOString().split('T')[0]);
        console.log('Current month:', currentMonth + 1); // +1 to make it 1-based
        console.log('First day calculated:', firstDayOfMonth.toISOString().split('T')[0]);
        console.log('Last day calculated:', lastDayOfMonth.toISOString().split('T')[0]);

        flatpickrInstance = flatpickr('#flatpickr-range', {
            mode: 'range',
            inline: true,
            appendTo: document.getElementById('flatpickr-range-container'),
            dateFormat: 'Y-m-d',
            defaultDate: [firstDayOfMonth, lastDayOfMonth],
            onOpen() {
                console.log('📅 Calendar opened - user is selecting dates');
                // Add visual feedback when calendar is being used
                const calendarContainer = document.getElementById('flatpickr-range-container');
                if (calendarContainer) {
                    calendarContainer.style.opacity = '0.7';
                }
            },
            onClose() {
                console.log('📅 Calendar closed');
                // Remove visual feedback
                const calendarContainer = document.getElementById('flatpickr-range-container');
                if (calendarContainer) {
                    calendarContainer.style.opacity = '1';
                }
            },
            onChange(selectedDates) {
                console.log('🔄 onChange triggered!');

                // Cek apakah sudah ada request yang berhasil - jika ya, blokir perubahan tanggal
                if (hasAnyRequestCompleted) {
                    console.log('⚠️ Date change blocked - request already completed');

                    // Kembalikan ke tanggal sebelumnya tanpa trigger onChange
                    if (previousDateRange) {
                        const [prevStart, prevEnd] = previousDateRange.split(' to ');
                        if (prevStart && prevEnd) {
                            setTimeout(() => {
                                flatpickrInstance.setDate([prevStart, prevEnd], false);
                                document.getElementById('flatpickr-range').value = previousDateRange;
                            }, 0);
                        }
                    }
                    return; // Stop execution
                }

                console.log('Raw selectedDates:', selectedDates);

                if (selectedDates.length === 2) {
                    const [startDateObj, endDateObj] = selectedDates;

                    console.log('📅 Start Date Object:', startDateObj);
                    console.log('📅 End Date Object:', endDateObj);
                    console.log('📅 Start toString():', startDateObj.toString());
                    console.log('📅 End toString():', endDateObj.toString());

                    // Fix timezone issue - gunakan getFullYear, getMonth, getDate untuk menghindari timezone shift
                    const startStr =
                        `${startDateObj.getFullYear()}-${String(startDateObj.getMonth() + 1).padStart(2, '0')}-${String(startDateObj.getDate()).padStart(2, '0')}`;
                    const endStr =
                        `${endDateObj.getFullYear()}-${String(endDateObj.getMonth() + 1).padStart(2, '0')}-${String(endDateObj.getDate()).padStart(2, '0')}`;

                    console.log('✅ Final Start Date:', startStr);
                    console.log('✅ Final End Date:', endStr);

                    // Update input display
                    document.getElementById('flatpickr-range').value = `${startStr} to ${endStr}`;

                    // Show immediate visual feedback
                    showDateChangeIndicator();

                    // Update sales options terlebih dahulu, baru kemudian update charts
                    // updateSalesOptions(startStr, endStr, () => {
                    //     // Setelah sales options selesai, baru update charts
                    //     debouncedUpdateCharts(startStr, endStr);
                    // });

                    // Simpan tanggal sebelumnya sebelum update
                    const currentInput = document.getElementById('flatpickr-range').value;
                    if (currentInput && currentInput.includes(' to ')) {
                        previousDateRange = currentInput;
                        console.log('💾 Saved previous date range:', previousDateRange);
                    }

                    // Update input display
                    document.getElementById('flatpickr-range').value = `${startStr} to ${endStr}`;

                    // Show immediate visual feedback
                    showDateChangeIndicator();

                    // Debounce untuk menghindari request bertumpuk
                    debouncedUpdateCharts(startStr, endStr);
                }
            }
        });

        function setupSalesSelectHandler() {
            const salesSelect = document.getElementById('sales-select');
            if (salesSelect) {
                salesSelect.addEventListener('change', function() {
                    // Cek apakah sudah ada request yang berhasil - jika ya, blokir perubahan sales
                    if (hasAnyRequestCompleted) {
                        console.log('⚠️ Sales change blocked - request already completed');
                        return;
                    }

                    console.log('🔄 Sales filter changed:', this.value);

                    // Get current date range
                    const currentRange = document.getElementById('flatpickr-range').value;
                    if (currentRange && currentRange.includes(' to ')) {
                        const [startDate, endDate] = currentRange.split(' to ');
                        debouncedUpdateCharts(startDate, endDate);
                    }
                });
            }
        }

        function populateSalesSelect(salesData) {
            const salesSelect = document.getElementById('sales-select');
            if (!salesSelect) return;

            // Simpan nilai yang sedang dipilih
            const currentValue = salesSelect.value;

            // Clear existing options except "Semua Sales"
            salesSelect.innerHTML = '<option value="">Semua Sales</option>';

            // Tambahkan sales options
            salesData.forEach(sales => {
                const option = document.createElement('option');
                option.value = sales;
                option.textContent = sales;
                salesSelect.appendChild(option);
            });

            // Restore nilai yang dipilih jika masih ada
            if (currentValue && salesData.includes(currentValue)) {
                salesSelect.value = currentValue;
            }

            console.log('📋 Sales select populated with', salesData.length, 'options');
        }

        // Fungsi untuk memberikan visual feedback saat user mengubah tanggal
        function showDateChangeIndicator() {
            const input = document.getElementById('flatpickr-range');
            if (input) {
                input.style.borderColor = '#f59e0b'; // Orange border
                input.style.backgroundColor = '#fef3c7'; // Light yellow background

                // Reset after delay
                setTimeout(() => {
                    input.style.borderColor = '';
                    input.style.backgroundColor = '';
                }, 1000);
            }
        }

        function updateAllChartsUnified(startDate = null, endDate = null) {
            const selectedSales = document.getElementById('sales-select').value;

            console.log('📤 SENDING Unified Analytics Request:', {
                url: '/api/analytics/all',
                start_date: startDate,
                end_date: endDate,
                sales: selectedSales
            });

            const requestData = {
                start_date: startDate,
                end_date: endDate
            };

            // Tambah parameter sales jika ada yang dipilih
            if (selectedSales && selectedSales.trim() !== '') {
                requestData.sales = selectedSales;
            }

            activeRequests.unified = $.ajax({
                url: '/api/analytics/all',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    console.log('✅ Unified analytics response:', response);
                    activeRequests.unified = null;

                    if (response.success && response.data) {
                        clearPreviousCharts();
                        const data = response.data;

                        // Render semua charts dengan data yang diterima
                        renderPaymentMethodChart(data.payment_methods);
                        updatePaymentMethodLabels(data.payment_methods.percentages);

                        renderProgramChart(data.programs);
                        renderModelChart(data.models);
                        renderStatusChart(data.status);

                        // Update sales options jika tersedia
                        if (data.available_sales && data.available_sales.length > 0) {
                            console.log('📋 Updating sales from unified data');
                            populateSalesSelect(data.available_sales);
                        }

                        // Mark completion
                        completedRequestsCount = 1;
                        hasAnyRequestCompleted = true;
                        console.log('🔒 Unified request completed - locking controls');
                        disableCancelButtonAndLockCalendar();
                    } else {
                        // Handle success=false case
                        console.error('❌ API returned success=false:', response);
                        clearPreviousCharts();
                        showAllErrorStates();
                    }
                },
                error: function(xhr, status, error) {
                    activeRequests.unified = null;
                    hideAllLoadingStates();

                    if (status !== 'abort') {
                        console.error('❌ Error fetching unified analytics data:', error);
                        console.error('Response:', xhr.responseText);
                        clearPreviousCharts();
                        showAllErrorStates();
                    }
                },
                complete: function() {
                    setTimeout(() => {
                        isUpdatingCharts = false;
                        hideAllLoadingStates();
                        unlockCalendarAfterCompletion();
                    }, 1000);
                }
            });
        }

        function hideAllLoadingStates() {
            const loadingStates = ['chart1-loading', 'chart2-loading', 'chart3-loading', 'line-chart-loading'];
            loadingStates.forEach(id => {
                hideLoadingState(id.replace('-loading', ''));
            });
        }

        function showAllErrorStates() {
            const errorStates = ['chart1-error', 'chart2-error', 'chart3-error', 'line-chart-error'];
            errorStates.forEach(id => {
                showErrorState(id.replace('-error', ''));
            });
        }

        function clearPreviousCharts() {
            console.log('🧹 Clearing all previous charts...');

            // Properly destroy chart instances
            if (pieChart1) {
                try {
                    pieChart1.destroy();
                    console.log('✅ PieChart1 destroyed');
                } catch (e) {
                    console.warn('⚠️ Error destroying pieChart1:', e);
                }
                pieChart1 = null;
            }

            if (pieChart2) {
                try {
                    pieChart2.destroy();
                    console.log('✅ PieChart2 destroyed');
                } catch (e) {
                    console.warn('⚠️ Error destroying pieChart2:', e);
                }
                pieChart2 = null;
            }

            if (pieChart3) {
                try {
                    pieChart3.destroy();
                    console.log('✅ PieChart3 destroyed');
                } catch (e) {
                    console.warn('⚠️ Error destroying pieChart3:', e);
                }
                pieChart3 = null;
            }

            if (lineChart) {
                try {
                    lineChart.destroy();
                    console.log('✅ LineChart destroyed');
                } catch (e) {
                    console.warn('⚠️ Error destroying lineChart:', e);
                }
                lineChart = null;
            }

            // Clear the chart containers
            const chartIds = ['pieChart1', 'pieChart2', 'pieChart3', 'lineChart'];
            chartIds.forEach(id => {
                const chartElement = document.getElementById(id);
                if (chartElement) {
                    chartElement.innerHTML = '';
                    console.log(`🧹 Cleared container: ${id}`);
                }
            });

            // Reset payment method labels to default
            const cashElement = document.querySelector('#cash-label span');
            const creditElement = document.querySelector('#credit-label span');
            if (cashElement) cashElement.textContent = 'Cash 0%';
            if (creditElement) creditElement.textContent = 'Credit 0%';

            console.log('✅ All charts cleared successfully');
        }

        function clearPreviousChart(chartId) {
            console.log(`🧹 Clearing chart with ID: ${chartId}`);

            // Petakan chart ID ke variabel chart yang digunakan
            const chartMap = {
                pieChart1: 'pieChart1',
                pieChart2: 'pieChart2',
                pieChart3: 'pieChart3',
                lineChart: 'lineChart'
            };

            // Ambil variabel chart yang terkait
            if (chartMap[chartId] && window[chartMap[chartId]]) {
                try {
                    window[chartMap[chartId]].destroy();
                    console.log(`✅ ${chartMap[chartId]} destroyed`);
                } catch (e) {
                    console.warn(`⚠️ Error destroying ${chartMap[chartId]}:`, e);
                }
                window[chartMap[chartId]] = null;
            }

            // Bersihkan elemen kontainernya
            const chartElement = document.getElementById(chartId);
            if (chartElement) {
                chartElement.innerHTML = '';
                console.log(`🧹 Cleared container: ${chartId}`);
            }
        }


        function hideAllErrorStates() {
            const errorStates = ['chart1-error', 'chart2-error', 'chart3-error', 'line-chart-error'];
            errorStates.forEach(id => {
                hideErrorState(id.replace('-error', ''));
            });
        }

        function showErrorState(chartId) {
            const element = document.getElementById(chartId + '-error');
            if (element) {
                element.classList.remove('hidden');
                console.log(`❌ Showing error state for ${chartId}`);
            }
        }

        function hideErrorState(chartId) {
            const element = document.getElementById(chartId + '-error');
            if (element) {
                element.classList.add('hidden');
            }
        }

        function showEmptyState(chartId) {
            const element = document.getElementById(chartId + '-empty');
            if (element) {
                element.classList.remove('hidden');
                console.log(`📭 Showing empty state for ${chartId}`);
            }
        }

        function hideEmptyState(chartId) {
            const element = document.getElementById(chartId + '-empty');
            if (element) {
                element.classList.add('hidden');
            }
        }

        function retryLoadData() {
            console.log('🔄 User clicked retry - reloading data');

            // Hide all error states
            hideAllErrorStates();

            // Reset flags to allow new request
            hasAnyRequestCompleted = false;
            completedRequestsCount = 0;

            // Get current date range and retry
            const currentRange = document.getElementById('flatpickr-range').value;
            if (currentRange && currentRange.includes(' to ')) {
                const [startDate, endDate] = currentRange.split(' to ');
                updateAllCharts(startDate, endDate);
            } else {
                // Use default current month if no range
                const today = new Date();
                const currentYear = today.getFullYear();
                const currentMonth = today.getMonth();
                const firstDay = new Date(currentYear, currentMonth, 1);
                const lastDay = new Date(currentYear, currentMonth + 1, 0);

                const startDate =
                    `${firstDay.getFullYear()}-${String(firstDay.getMonth() + 1).padStart(2, '0')}-${String(firstDay.getDate()).padStart(2, '0')}`;
                const endDate =
                    `${lastDay.getFullYear()}-${String(lastDay.getMonth() + 1).padStart(2, '0')}-${String(lastDay.getDate()).padStart(2, '0')}`;

                updateAllCharts(startDate, endDate);
            }
        }

        function cancelAllActiveRequests() {
            console.log('🛑 Cancelling all active requests');
            Object.keys(activeRequests).forEach(key => {
                if (activeRequests[key]) {
                    activeRequests[key].abort();
                    activeRequests[key] = null;
                }
            });
        }

        // Fungsi debouncing untuk update charts
        function debouncedUpdateCharts(startDate, endDate) {
            // Cancel timer sebelumnya jika ada
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            // Cancel semua request yang sedang berjalan
            cancelAllActiveRequests();

            // Set timer baru dengan delay yang lebih pendek untuk responsivitas yang lebih baik
            debounceTimer = setTimeout(() => {
                console.log('🚀 Executing debounced update charts');
                updateAllCharts(startDate, endDate);
            }, 300); // Reduced from 500ms to 300ms for better responsiveness
        }


        // Initialize charts on page load with current month filter
        $(document).ready(function() {
            // Fix timezone issue - gunakan manual date formatting
            const startDate =
                `${firstDayOfMonth.getFullYear()}-${String(firstDayOfMonth.getMonth() + 1).padStart(2, '0')}-${String(firstDayOfMonth.getDate()).padStart(2, '0')}`;
            const endDate =
                `${lastDayOfMonth.getFullYear()}-${String(lastDayOfMonth.getMonth() + 1).padStart(2, '0')}-${String(lastDayOfMonth.getDate()).padStart(2, '0')}`;

            console.log('🚀 Initial load - calculated dates:');
            console.log('Start Date:', startDate);
            console.log('End Date:', endDate);

            // Set initial input display dan simpan sebagai previous
            const initialRange = `${startDate} to ${endDate}`;
            document.getElementById('flatpickr-range').value = initialRange;
            previousDateRange = initialRange;

            updateAllCharts(startDate, endDate);
            setupSalesSelectHandler();
        });

        function updateAllCharts(startDate = null, endDate = null) {
            console.log('Updating all charts with date range:', startDate, 'to', endDate);
            // Disable cancel button dan lock calendar
            disableCancelButtonAndLockCalendar();

            // Set flag bahwa sedang update charts
            isUpdatingCharts = true;

            // Reset completion tracking - reset semua flag untuk request baru
            completedRequestsCount = 0;
            hasAnyRequestCompleted = false; // Reset flag saat mulai request baru

            // Cancel semua request yang sedang berjalan sebelumnya
            cancelAllActiveRequests();

            // Show loading state untuk semua charts
            showAllLoadingStates();

            // Track completion status - ubah logika untuk disable setelah 1 request berhasil
            const checkCompleted = () => {
                completedRequestsCount++;
                console.log(`✅ Request completed: ${completedRequestsCount}/${totalRequestsExpected}`);

                // Disable cancel button dan lock calendar setelah request pertama berhasil
                // if (completedRequestsCount === 1 && !hasAnyRequestCompleted) {
                //     hasAnyRequestCompleted = true;
                //     console.log('🔒 First request completed - locking calendar and disabling cancel');


                // }

                // Check jika semua selesai untuk cleanup
                if (completedRequestsCount >= totalRequestsExpected) {
                    isUpdatingCharts = false;
                    console.log('✅ All charts updated successfully');

                    // Hide cancel button setelah semua selesai
                    setTimeout(() => {
                        // const cancelBtn = document.getElementById('cancel-loading-btn');
                        // if (cancelBtn) {
                        //     cancelBtn.classList.add('hidden');
                        //     const buttonText = cancelBtn.querySelector('i').nextSibling;
                        //     if (buttonText) {
                        //         buttonText.textContent = ' Batalkan';
                        //     }
                        // }

                        unlockCalendarAfterCompletion();
                    }, 1000);
                }
            };

            // Update semua charts dengan tracking completion
            // updatePaymentMethodChart(startDate, endDate, checkCompleted);
            // updateProgramChart(startDate, endDate, checkCompleted);
            // updateModelChart(startDate, endDate, checkCompleted);
            // updateStatusChart(startDate, endDate, checkCompleted);
            updateAllChartsUnified(startDate, endDate);
        }

        // Fungsi untuk unlock calendar setelah semua request selesai
        function unlockCalendarAfterCompletion() {
            // Unlock calendar
            const calendarContainer = document.getElementById('flatpickr-range-container');
            if (calendarContainer) {
                calendarContainer.style.pointerEvents = '';
                calendarContainer.style.opacity = '';
                calendarContainer.style.filter = '';
            }

            // Unlock input field
            const inputField = document.getElementById('flatpickr-range');
            if (inputField) {
                inputField.style.backgroundColor = '';
                inputField.style.cursor = '';
                inputField.disabled = false;
            }

            const salesSelect = document.getElementById('sales-select');
            if (salesSelect) {
                salesSelect.disabled = false;
                salesSelect.style.backgroundColor = '';
                salesSelect.style.cursor = '';
                salesSelect.style.opacity = '';
            }

            // Reset flag untuk memungkinkan request baru
            hasAnyRequestCompleted = false;

            console.log('🔓 Calendar unlocked - ready for new date selection');
        }

        function showAllLoadingStates() {
            // Show loading overlays dengan slight delay untuk smooth transition
            const loadingStates = ['chart1-loading', 'chart2-loading', 'chart3-loading', 'line-chart-loading'];
            loadingStates.forEach((id, index) => {
                setTimeout(() => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.classList.remove('hidden');
                    }
                }, index * 50); // Stagger the loading appearance
            });

            // Show cancel button dan pastikan enabled
            setTimeout(() => {
                // const cancelBtn = document.getElementById('cancel-loading-btn');
                // if (cancelBtn) {
                //     cancelBtn.classList.remove('hidden');
                //     // Pastikan button enabled saat loading
                //     const button = cancelBtn.querySelector('button');
                //     if (button) {
                //         button.disabled = false;
                //         button.classList.remove('opacity-50', 'cursor-not-allowed');
                //     }
                // }
            }, 1000); // Show after 1 second

            // Update labels to show loading
            const cashElement = document.querySelector('#cash-label span');
            const creditElement = document.querySelector('#credit-label span');

            if (cashElement) cashElement.textContent = 'Loading...';
            if (creditElement) creditElement.textContent = 'Loading...';
        }

        function hideLoadingState(chartId) {
            const element = document.getElementById(chartId + '-loading');
            if (element && !element.classList.contains('hidden')) {
                console.log(`🔄 Hiding loading state for ${chartId}`);

                // Add immediate smooth fade out
                element.style.transition = 'opacity 0.3s ease-out';
                element.style.opacity = '0';

                setTimeout(() => {
                    element.classList.add('hidden');
                    element.style.opacity = '1'; // Reset for next time
                    element.style.transition = ''; // Reset transition
                }, 300);
            }

            // Fix: hapus log yang menggunakan variabel undefined
            console.log(
                `📊 Chart ${chartId} completed. Total completed: ${completedRequestsCount}/${totalRequestsExpected}`);
        }

        // Hapus fungsi disableCancelButton() yang tidak terpakai dan replace dengan yang benar

        // Fungsi baru untuk disable cancel button dan lock calendar setelah 1 request berhasil
        function disableCancelButtonAndLockCalendar() {
            // Disable cancel button
            // const cancelBtn = document.getElementById('cancel-loading-btn');
            // if (cancelBtn) {
            //     const button = cancelBtn.querySelector('button');
            //     if (button) {
            //         button.disabled = true;
            //         button.classList.add('opacity-50', 'cursor-not-allowed');

            //         // Update text button
            //         const buttonText = button.querySelector('i').nextSibling;
            //         if (buttonText) {
            //             buttonText.textContent = ' Terkunci';
            //         }
            //     }
            // }

            // Lock sales select
            const salesSelect = document.getElementById('sales-select');
            if (salesSelect) {
                salesSelect.disabled = true;
                salesSelect.style.backgroundColor = '#f3f4f6';
                salesSelect.style.cursor = 'not-allowed';
                salesSelect.style.opacity = '0.5';
            }

            // Lock calendar - disable interaction dengan visual feedback
            const calendarContainer = document.getElementById('flatpickr-range-container');
            if (calendarContainer) {
                calendarContainer.style.pointerEvents = 'none';
                calendarContainer.style.opacity = '0.5';
                calendarContainer.style.filter = 'grayscale(1)';
            }

            // Lock input field
            const inputField = document.getElementById('flatpickr-range');
            if (inputField) {
                inputField.style.backgroundColor = '#f3f4f6';
                inputField.style.cursor = 'not-allowed';
                inputField.disabled = true;
            }

            console.log('🔒 Calendar and cancel button locked');
        }

        // Fungsi untuk unlock calendar dan enable cancel button (untuk testing/debugging)
        function unlockCalendarAndEnableCancel() {
            // Unlock calendar
            const calendarContainer = document.getElementById('flatpickr-range-container');
            if (calendarContainer) {
                calendarContainer.style.pointerEvents = '';
                calendarContainer.style.opacity = '';
                calendarContainer.style.filter = '';
            }

            // Unlock input field
            const inputField = document.getElementById('flatpickr-range');
            if (inputField) {
                inputField.style.backgroundColor = '';
                inputField.style.cursor = '';
                inputField.disabled = false;
            }

            const salesSelect = document.getElementById('sales-select');
            if (salesSelect) {
                salesSelect.disabled = false;
                salesSelect.style.backgroundColor = '';
                salesSelect.style.cursor = '';
                salesSelect.style.opacity = '1';
            }

            // Reset flags
            hasAnyRequestCompleted = false;
            completedRequestsCount = 0;

            console.log('🔓 Calendar and cancel button unlocked');
        }

        // Fungsi untuk cancel semua request dari UI
        function cancelAllRequests() {
            console.log('🚫 User manually cancelled all requests');

            // Cek apakah sudah ada request yang berhasil - jika ya, tidak bisa cancel
            if (hasAnyRequestCompleted) {
                console.log('⚠️ Cannot cancel - at least one request already completed');
                return;
            }

            // Cancel timer debounce
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            // Cancel semua active requests
            cancelAllActiveRequests();

            // Kembalikan ke tanggal sebelumnya jika ada
            if (previousDateRange) {
                const [prevStart, prevEnd] = previousDateRange.split(' to ');
                if (prevStart && prevEnd && flatpickrInstance) {
                    // Kembalikan kalender dan input ke range sebelumnya tanpa trigger onChange
                    flatpickrInstance.setDate([prevStart, prevEnd], false);
                    document.getElementById('flatpickr-range').value = previousDateRange;

                    console.log('↩️ Restored previous date range:', previousDateRange);
                } else {
                    console.warn('⚠️ Gagal mengembalikan tanggal sebelumnya - format tidak valid');
                }
            }


            // Hide semua loading states
            const allLoadingStates = ['chart1-loading', 'chart2-loading', 'chart3-loading', 'line-chart-loading'];
            allLoadingStates.forEach(id => {
                hideLoadingState(id.replace('-loading', ''));
            });

            // Hide cancel button
            // const cancelBtn = document.getElementById('cancel-loading-btn');
            // if (cancelBtn) {
            //     cancelBtn.classList.add('hidden');
            // }

            // Reset flags
            isUpdatingCharts = false;
            completedRequestsCount = 0;
            // Reset hasAnyRequestCompleted juga saat cancel manual
            hasAnyRequestCompleted = false;

            // Show notification
            console.log('✅ All requests cancelled by user');
        }

        function updatePaymentMethodChart(startDate = null, endDate = null, onComplete = null) {
            const selectedSales = document.getElementById('sales-select').value;
            console.log('📤 SENDING Payment Method Request:', {
                url: '/api/analytics/payment-method',
                start_date: startDate,
                end_date: endDate,
                sales: selectedSales
            });

            // Cancel request sebelumnya jika ada
            if (activeRequests.payment) {
                activeRequests.payment.abort();
            }

            const requestData = {
                start_date: startDate,
                end_date: endDate
            };

            // Tambah parameter sales jika ada yang dipilih
            if (selectedSales && selectedSales.trim() !== '') {
                requestData.sales = selectedSales;
            }

            activeRequests.payment = $.ajax({
                url: '/api/analytics/payment-method',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    console.log('✅ Payment method response:', response);
                    activeRequests.payment = null; // Clear reference

                    if (response.success) {
                        renderPaymentMethodChart(response.data);
                        updatePaymentMethodLabels(response.data.percentages);
                    }
                    if (onComplete) onComplete();
                },
                error: function(xhr, status, error) {
                    activeRequests.payment = null; // Clear reference
                    hideLoadingState('chart1');

                    // Jangan log error jika request dibatalkan
                    if (status !== 'abort') {
                        console.error('❌ Error fetching payment method data:', error);
                        console.error('Response:', xhr.responseText);
                    }
                    if (onComplete) onComplete();
                },
                complete: function() {
                    hideLoadingState('chart1');
                }
            });
        }

        function updateProgramChart(startDate = null, endDate = null, onComplete = null) {
            // Cancel request sebelumnya jika ada
            if (activeRequests.program) {
                activeRequests.program.abort();
            }

            const selectedSales = document.getElementById('sales-select').value;
            const requestData = {
                start_date: startDate,
                end_date: endDate
            };

            // Tambah parameter sales jika ada yang dipilih
            if (selectedSales && selectedSales.trim() !== '') {
                requestData.sales = selectedSales;
            }

            activeRequests.program = $.ajax({
                url: '/api/analytics/program',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    console.log('Program response:', response);
                    activeRequests.program = null; // Clear reference

                    if (response.success) {
                        renderProgramChart(response.data);
                    } else {
                        hideLoadingState('chart2');
                    }
                    if (onComplete) onComplete();
                },
                error: function(xhr, status, error) {
                    activeRequests.program = null; // Clear reference
                    hideLoadingState('chart2');

                    // Jangan log error jika request dibatalkan
                    if (status !== 'abort') {
                        console.error('Error fetching program data:', error);
                        console.error('Response:', xhr.responseText);
                    }
                    if (onComplete) onComplete();
                }
            });
        }

        function updateModelChart(startDate = null, endDate = null, onComplete = null) {
            // Cancel request sebelumnya jika ada
            if (activeRequests.model) {
                activeRequests.model.abort();
            }

            const selectedSales = document.getElementById('sales-select').value;
            const requestData = {
                start_date: startDate,
                end_date: endDate
            };

            // Tambah parameter sales jika ada yang dipilih
            if (selectedSales && selectedSales.trim() !== '') {
                requestData.sales = selectedSales;
            }

            activeRequests.model = $.ajax({
                url: '/api/analytics/model',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    console.log('Model response:', response);
                    activeRequests.model = null; // Clear reference

                    if (response.success) {
                        renderModelChart(response.data);
                    } else {
                        hideLoadingState('chart3');
                    }
                    if (onComplete) onComplete();
                },
                error: function(xhr, status, error) {
                    activeRequests.model = null; // Clear reference
                    hideLoadingState('chart3');

                    // Jangan log error jika request dibatalkan
                    if (status !== 'abort') {
                        console.error('Error fetching model data:', error);
                        console.error('Response:', xhr.responseText);
                    }
                    if (onComplete) onComplete();
                }
            });
        }

        function updateStatusChart(startDate = null, endDate = null, onComplete = null) {
            // Cancel request sebelumnya jika ada
            if (activeRequests.status) {
                activeRequests.status.abort();
            }

            const selectedSales = document.getElementById('sales-select').value;
            const requestData = {
                start_date: startDate,
                end_date: endDate
            };

            // Tambah parameter sales jika ada yang dipilih
            if (selectedSales && selectedSales.trim() !== '') {
                requestData.sales = selectedSales;
            }


            activeRequests.status = $.ajax({
                url: '/api/analytics/status',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    console.log('Status response:', response);
                    activeRequests.status = null; // Clear reference

                    if (response.success) {
                        renderStatusChart(response.data);
                        if (response.data.available_sales && (!selectedSales || selectedSales.trim() === '')) {
                            console.log('📋 Updating sales from status chart data');
                            populateSalesSelect(response.data.available_sales);
                        }
                    } else {
                        hideLoadingState('line-chart');
                    }
                    if (onComplete) onComplete();
                },
                error: function(xhr, status, error) {
                    activeRequests.status = null; // Clear reference
                    hideLoadingState('line-chart');

                    // Jangan log error jika request dibatalkan
                    if (status !== 'abort') {
                        console.error('Error fetching status data:', error);
                        console.error('Response:', xhr.responseText);
                    }
                    if (onComplete) onComplete();
                }
            });
        }
    </script>



    <script>
        // Global chart variables
        let pieChart1, pieChart2, pieChart3, lineChart;

        $(document).ready(function() {
            // Initialize empty charts first
            initializeCharts();
        });

        function initializeCharts() {
            // Initialize empty charts that will be populated later
            pieChart1 = new ApexCharts(document.querySelector("#pieChart1"), getEmptyPieOptions('Payment Methods'));
            pieChart1.render();

            pieChart2 = new ApexCharts(document.querySelector("#pieChart2"), getEmptyDonutOptions('Programs'));
            pieChart2.render();

            pieChart3 = new ApexCharts(document.querySelector("#pieChart3"), getEmptyPieOptions('Models'));
            pieChart3.render();

            lineChart = new ApexCharts(document.querySelector("#lineChart"), getEmptyLineOptions('Status Over Time'));
            lineChart.render();
        }

        function getEmptyPieOptions(title) {
            return {
                series: [],
                chart: {
                    width: '100%',
                    type: 'pie',
                },
                labels: [],
                colors: ['#e5e7eb'],
                title: {
                    text: title,
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        color: '#1a1a1a'
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: '#1a1a1a',
                        useSeriesColors: false
                    }
                }
            };
        }

        function getEmptyDonutOptions(title) {
            return {
                series: [], // Dummy data to avoid empty chart
                chart: {
                    width: '100%',
                    type: 'donut',
                },
                labels: [],
                colors: ['#e5e7eb'],
                title: {
                    text: title,
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        color: '#1a1a1a'
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: '#1a1a1a',
                        useSeriesColors: false
                    }
                }
            };
        }

        function getEmptyLineOptions(title) {
            return {
                series: [],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#1a1a1a'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: title,
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        color: '#1a1a1a'
                    }
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: [],
                    labels: {
                        style: {
                            colors: '#1a1a1a'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#1a1a1a'
                        }
                    }
                },
                noData: {
                    text: 'Loading...'
                }
            };
        }

        function renderPaymentMethodChart(data) {
            console.log('Rendering payment method chart:', data);

            // Hide all states first
            hideLoadingState('chart1');
            hideErrorState('chart1');
            hideEmptyState('chart1');

            // Validation data
            if (!data || !data.series || !data.labels) {
                console.warn('Invalid payment method data:', data);

                showEmptyState('chart1');
                return;
            }

            // Check if data is empty
            if (data.series.length === 0 || data.series.every(val => val === 0)) {
                console.log('📭 Payment method data is empty');
                showEmptyState('chart1');
                return;
            }

            const currentLabels = pieChart1?.w?.config?.labels;
            const isLabelChanged = JSON.stringify(currentLabels) !== JSON.stringify(data.labels);

            if (isLabelChanged) {
                recreatePaymentChart(data);
            } else {
                pieChart1.updateSeries(data.series);
            }
        }

        function recreatePaymentChart(data) {
            if (pieChart1) pieChart1.destroy();

            const options = {
                series: data.series,
                chart: {
                    width: '100%',
                    type: 'pie',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 600
                    }
                },
                labels: data.labels,
                colors: ['#1a1a1a', '#2d3748', '#4a5568', '#718096', '#a0aec0'],
                title: {
                    text: 'Payment Methods',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        color: '#1a1a1a'
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: '#1a1a1a',
                        useSeriesColors: false
                    }
                }
            };

            pieChart1 = new ApexCharts(document.querySelector("#pieChart1"), options);
            pieChart1.render();
        }

        function renderProgramChart(data) {
            console.log('Rendering program chart:', data);

            // Hide all states first
            hideLoadingState('chart2');
            hideErrorState('chart2');
            hideEmptyState('chart2');

            // Validasi minimal
            if (!data || !Array.isArray(data.series) || !Array.isArray(data.labels)) {
                console.warn('Invalid program chart data:', data);
                showEmptyState('chart2');
                return;
            }

            // Check if data is empty
            if (data.series.length === 0 || data.series.every(val => val === 0)) {
                console.log('📭 Program data is empty');
                showEmptyState('chart2');
                return;
            }

            // Jika chart sudah dibuat
            if (pieChart2) {
                const currentLabels = pieChart2?.w?.config?.labels ?? [];

                const labelsChanged = JSON.stringify(currentLabels) !== JSON.stringify(data.labels);

                if (labelsChanged) {
                    console.log('🔁 Labels changed, recreating program chart...');
                    recreateProgramChart(data);
                } else {
                    try {
                        pieChart2.updateSeries(data.series, true);
                    } catch (err) {
                        console.warn('⚠️ Error updating series, recreating chart:', err);
                        recreateProgramChart(data);
                    }
                }
            } else {
                recreateProgramChart(data);
            }
        }

        function recreateProgramChart(data) {
            if (pieChart2) pieChart2.destroy();

            const options = {
                series: data.series,
                chart: {
                    width: '100%',
                    type: 'pie',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 600
                    }
                },
                labels: data.labels,
                colors: ['#1a1a1a', '#2d3748', '#4a5568', '#718096', '#a0aec0'],
                title: {
                    text: 'Programs',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        color: '#1a1a1a'
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: '#1a1a1a',
                        useSeriesColors: false
                    }
                }
            };

            pieChart2 = new ApexCharts(document.querySelector("#pieChart2"), options);
            pieChart2.render();
        }

        function renderModelChart(data) {
            console.log('Rendering model chart:', data);

            // Hide all states first
            hideLoadingState('chart3');
            hideErrorState('chart3');
            hideEmptyState('chart3');

            if (!data || !Array.isArray(data.series) || !Array.isArray(data.labels)) {
                console.warn('Invalid model chart data:', data);
                showEmptyState('chart3');
                return;
            }

            // Check if data is empty
            if (data.series.length === 0 || data.series.every(val => val === 0)) {
                console.log('📭 Model data is empty');
                showEmptyState('chart3');
                return;
            }

            if (pieChart3) {
                const currentLabels = pieChart3?.w?.config?.labels ?? [];
                const labelsChanged = JSON.stringify(currentLabels) !== JSON.stringify(data.labels);

                if (labelsChanged) {
                    console.log('🔁 Labels changed, recreating model chart...');
                    recreateModelChart(data);
                } else {
                    try {
                        pieChart3.updateSeries(data.series, true);
                    } catch (error) {
                        console.warn('⚠️ Failed to update series, recreating chart:', error);
                        recreateModelChart(data);
                    }
                }
            } else {
                recreateModelChart(data);
            }
        }

        function recreateModelChart(data) {
            if (pieChart3) pieChart3.destroy();

            const options = {
                series: data.series,
                chart: {
                    width: '100%',
                    type: 'pie',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 600
                    }
                },
                labels: data.labels,
                colors: ['#1a1a1a', '#2d3748', '#4a5568', '#718096', '#a0aec0'],
                title: {
                    text: 'Models',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        color: '#1a1a1a'
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: '#1a1a1a',
                        useSeriesColors: false
                    }
                }
            };

            pieChart3 = new ApexCharts(document.querySelector("#pieChart3"), options);
            pieChart3.render();
        }

        function renderStatusChart(data) {
            console.log('Rendering status chart:', data);

            // Hide all states first
            hideLoadingState('line-chart');
            hideErrorState('line-chart');
            hideEmptyState('line-chart');

            if (!data || !Array.isArray(data.series) || !Array.isArray(data.categories)) {
                console.warn('Invalid status chart data:', data);
                showEmptyState('line-chart');
                return;
            }

            // Check if data is empty
            if (data.series.length === 0 || (data.series[0] && data.series[0].data && data.series[0].data.every(val =>
                    val === 0))) {
                console.log('📭 Status data is empty');
                showEmptyState('line-chart');
                return;
            }

            if (lineChart) {
                const currentCategories = lineChart?.w?.config?.xaxis?.categories ?? [];
                const categoriesChanged = JSON.stringify(currentCategories) !== JSON.stringify(data.categories);

                if (categoriesChanged) {
                    console.log('🔁 Categories changed, recreating status chart...');
                    recreateStatusChart(data);
                } else {
                    try {
                        lineChart.updateSeries(data.series, true);
                    } catch (error) {
                        console.warn('⚠️ Failed to update series, recreating chart:', error);
                        recreateStatusChart(data);
                    }
                }
            } else {
                recreateStatusChart(data);
            }
        }

        function recreateStatusChart(data) {
            if (lineChart) lineChart.destroy();

            const options = {
                series: data.series,
                chart: {
                    height: 350,
                    type: 'bar',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 600
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        endingShape: 'rounded'
                    }
                },
                colors: ['#34d399'], // Warna hijau modern
                dataLabels: {
                    enabled: true
                },
                title: {
                    text: 'Ringkasan Status',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        color: '#1a1a1a'
                    }
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: data.categories,
                    title: {
                        text: 'Status',
                        style: {
                            color: '#1a1a1a',
                            fontWeight: 600
                        }
                    },
                    labels: {
                        style: {
                            colors: '#1a1a1a'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah',
                        style: {
                            color: '#1a1a1a',
                            fontWeight: 600
                        }
                    },
                    labels: {
                        style: {
                            colors: '#1a1a1a'
                        }
                    }
                },
                legend: {
                    show: false
                }
            };

            lineChart = new ApexCharts(document.querySelector("#lineChart"), options);
            lineChart.render();
        }

        // Fungsi untuk mengupdate label Cash dan Credit
        function updatePaymentMethodLabels(percentages) {
            console.log('Updating payment method labels:', percentages);

            // Update the Cash and Credit percentage displays
            const cashElement = document.querySelector('#cash-label span');
            const creditElement = document.querySelector('#credit-label span');

            if (cashElement) {
                cashElement.textContent = `Cash ${percentages.Cash || 0}%`;
            }
            if (creditElement) {
                creditElement.textContent = `Credit ${percentages.Credit || 0}%`;
            }
        }
    </script>
</body>

</html>
