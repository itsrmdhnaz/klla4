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
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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
    </style>

</head>

<body class="font-sans">

    <!-- Div Layout -->
    <div class="flex gap-4 leading-6 px-4">

        <div class="flex-1">
            <div class="rounded-lg bg-[#9bd69e] p-4 relative">
                <!-- Chart 1 -->
                <div id="pieChart1"></div>
                <div class="mt-4">
                    <div class="flex items-center gap-2 mb-2 bg-white p-2 rounded-lg" id="cash-label">
                        <i class="ti ti-cash text-4xl"></i>
                        <span>Cash 50%</span>
                    </div>
                    <div class="flex items-center gap-2 mb-2 bg-white p-2 rounded-lg" id="credit-label">
                        <i class="ti ti-wallet text-4xl"></i>
                        <span>Credit 50%</span>
                    </div>
                </div>
                
                <!-- Loading overlay untuk chart 1 -->
                <div id="chart1-loading" class="hidden absolute inset-0 bg-white bg-opacity-90 rounded-lg loading-overlay">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="loading-spinner rounded-full h-12 w-12 border-b-2 border-green-600 mb-3"></div>
                        <span class="text-sm text-gray-600 font-medium">Memuat Payment Methods...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-2" bis_skin_checked="1">
            <div class="flex justify-evenly mb-4 gap-4">
                <!-- Div Pie Chart 2 -->
                <div class="flex-1 rounded-lg bg-[#c8e6ca] p-4 relative">
                    <div id="pieChart2"></div>
                    <!-- Loading overlay untuk chart 2 -->
                    <div id="chart2-loading" class="hidden absolute inset-0 bg-white bg-opacity-90 rounded-lg loading-overlay">
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="loading-spinner rounded-full h-10 w-10 border-b-2 border-green-600 mb-2"></div>
                            <span class="text-xs text-gray-600 font-medium">Memuat Programs...</span>
                        </div>
                    </div>
                </div>
                <!-- Div Pie Chart 3 -->
                <div class="flex-1 rounded-lg bg-[#c8e6ca] p-4 relative">
                    <div id="pieChart3"></div>
                    <!-- Loading overlay untuk chart 3 -->
                    <div id="chart3-loading" class="hidden absolute inset-0 bg-white bg-opacity-90 rounded-lg loading-overlay">
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="loading-spinner rounded-full h-10 w-10 border-b-2 border-green-600 mb-2"></div>
                            <span class="text-xs text-gray-600 font-medium">Memuat Models...</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Div Line Chart -->
            <div class="rounded-lg bg-[#c8e6ca] p-4 relative">
                <div id="lineChart"></div>
                <!-- Loading overlay untuk line chart -->
                <div id="line-chart-loading" class="hidden absolute inset-0 bg-white bg-opacity-90 rounded-lg loading-overlay">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="loading-spinner rounded-full h-12 w-12 border-b-2 border-green-600 mb-3"></div>
                        <span class="text-sm text-gray-600 font-medium">Memuat Status Over Time...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Div Calendar -->
        <div class="">
        <!-- <div class="flex-1"> -->
            <div class="rounded-lg bg-[#9bd69e] p-4 relative">
                <div class="text-2xl font-semibold mb-2 text-center">Tanggal</div>

                <input type="text" id="flatpickr-range" class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent shadow-sm mb-4" placeholder="Pilih rentang tanggal..." readonly />

                <!-- tempat kalender dirender -->
                <div id="flatpickr-range-container" class="rounded-lg p-2 shadow w-full flex justify-center"></div>
                
                <!-- Loading overlay untuk calendar section -->
                <div id="calendar-loading" class="hidden absolute inset-0 bg-white bg-opacity-80 rounded-lg loading-overlay">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="loading-spinner rounded-full h-8 w-8 border-b-2 border-green-600 mb-2"></div>
                        <span class="text-sm text-gray-600">Memuat data...</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Set default date range to current month
        const today = new Date();
        
        // Fix: Gunakan cara yang lebih eksplisit untuk menghitung first dan last day
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth(); // 0-based (0 = January, 6 = July)
        
        const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
        const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0); // Day 0 of next month = last day of current month
        
        console.log('Today:', today.toISOString().split('T')[0]);
        console.log('Current month:', currentMonth + 1); // +1 to make it 1-based
        console.log('First day calculated:', firstDayOfMonth.toISOString().split('T')[0]);
        console.log('Last day calculated:', lastDayOfMonth.toISOString().split('T')[0]);
        
        flatpickr('#flatpickr-range', {
            mode: 'range',               
            inline: true,                
            appendTo: document.getElementById('flatpickr-range-container'),
            dateFormat: 'Y-m-d',
            defaultDate: [firstDayOfMonth, lastDayOfMonth],
            onChange(selectedDates) {
                console.log('🔄 onChange triggered!');
                console.log('Raw selectedDates:', selectedDates);
                
                if (selectedDates.length === 2) {
                    const [startDateObj, endDateObj] = selectedDates;
                    
                    console.log('📅 Start Date Object:', startDateObj);
                    console.log('📅 End Date Object:', endDateObj);
                    console.log('📅 Start toString():', startDateObj.toString());
                    console.log('📅 End toString():', endDateObj.toString());
                    
                    // Fix timezone issue - gunakan getFullYear, getMonth, getDate untuk menghindari timezone shift
                    const startStr = `${startDateObj.getFullYear()}-${String(startDateObj.getMonth() + 1).padStart(2, '0')}-${String(startDateObj.getDate()).padStart(2, '0')}`;
                    const endStr = `${endDateObj.getFullYear()}-${String(endDateObj.getMonth() + 1).padStart(2, '0')}-${String(endDateObj.getDate()).padStart(2, '0')}`;
                    
                    console.log('✅ Final Start Date:', startStr);
                    console.log('✅ Final End Date:', endStr);
                    
                    // Update input display
                    document.getElementById('flatpickr-range').value = `${startStr} to ${endStr}`;
                    
                    // Update all charts when date range changes
                    updateAllCharts(startStr, endStr);
                }
            }
        });

        // Initialize charts on page load with current month filter
        $(document).ready(function() {
            // Fix timezone issue - gunakan manual date formatting
            const startDate = `${firstDayOfMonth.getFullYear()}-${String(firstDayOfMonth.getMonth() + 1).padStart(2, '0')}-${String(firstDayOfMonth.getDate()).padStart(2, '0')}`;
            const endDate = `${lastDayOfMonth.getFullYear()}-${String(lastDayOfMonth.getMonth() + 1).padStart(2, '0')}-${String(lastDayOfMonth.getDate()).padStart(2, '0')}`;
            
            console.log('🚀 Initial load - calculated dates:');
            console.log('Start Date:', startDate);
            console.log('End Date:', endDate);
            
            // Set initial input display
            document.getElementById('flatpickr-range').value = `${startDate} to ${endDate}`;
            
            updateAllCharts(startDate, endDate);
        });

        function updateAllCharts(startDate = null, endDate = null) {
            console.log('Updating all charts with date range:', startDate, 'to', endDate);
            
            // Show loading state for all charts
            showAllLoadingStates();
            
            updatePaymentMethodChart(startDate, endDate);
            updateProgramChart(startDate, endDate);
            updateModelChart(startDate, endDate);
            updateStatusChart(startDate, endDate);
        }

        function showAllLoadingStates() {
            // Show loading overlays
            document.getElementById('chart1-loading').classList.remove('hidden');
            document.getElementById('chart2-loading').classList.remove('hidden');
            document.getElementById('chart3-loading').classList.remove('hidden');
            document.getElementById('line-chart-loading').classList.remove('hidden');
            
            // Update labels to show loading
            const cashElement = document.querySelector('#cash-label span');
            const creditElement = document.querySelector('#credit-label span');
            
            if (cashElement) cashElement.textContent = 'Loading...';
            if (creditElement) creditElement.textContent = 'Loading...';
        }

        function hideLoadingState(chartId) {
            document.getElementById(chartId + '-loading').classList.add('hidden');
        }

        function updatePaymentMethodChart(startDate = null, endDate = null) {
            console.log('📤 SENDING Payment Method Request:', {
                url: '/api/analytics/payment-method',
                start_date: startDate,
                end_date: endDate
            });
            
            $.ajax({
                url: '/api/analytics/payment-method',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    console.log('✅ Payment method response:', response);
                    hideLoadingState('chart1');
                    if (response.success) {
                        renderPaymentMethodChart(response.data);
                        updatePaymentMethodLabels(response.data.percentages);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error fetching payment method data:', error);
                    console.error('Response:', xhr.responseText);
                    hideLoadingState('chart1');
                }
            });
        }

        function updateProgramChart(startDate = null, endDate = null) {
            $.ajax({
                url: '/api/analytics/program',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    console.log('Program response:', response);
                    hideLoadingState('chart2');
                    if (response.success) {
                        renderProgramChart(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching program data:', error);
                    console.error('Response:', xhr.responseText);
                    hideLoadingState('chart2');
                }
            });
        }

        function updateModelChart(startDate = null, endDate = null) {
            $.ajax({
                url: '/api/analytics/model',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    console.log('Model response:', response);
                    hideLoadingState('chart3');
                    if (response.success) {
                        renderModelChart(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching model data:', error);
                    console.error('Response:', xhr.responseText);
                    hideLoadingState('chart3');
                }
            });
        }

        function updateStatusChart(startDate = null, endDate = null) {
            $.ajax({
                url: '/api/analytics/status',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    console.log('Status response:', response);
                    hideLoadingState('line-chart');
                    if (response.success) {
                        renderStatusChart(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching status data:', error);
                    console.error('Response:', xhr.responseText);
                    hideLoadingState('line-chart');
                }
            });
        }
    </script>



    <script>
        // Global chart variables
        let pieChart1, pieChart2, pieChart3, lineChart;

        $(document).ready(function () {
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
                series: [1], // Dummy data to avoid empty chart
                chart: {
                    width: '100%',
                    type: 'pie',
                },
                labels: ['Loading...'],
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
                series: [1], // Dummy data to avoid empty chart
                chart: {
                    width: '100%',
                    type: 'donut',
                },
                labels: ['Loading...'],
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
            
            // Destroy and recreate chart to ensure proper update
            pieChart1.destroy();
            
            const options = {
                series: data.series,
                chart: {
                    width: '100%',
                    type: 'pie',
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
            
            // Destroy and recreate chart to ensure proper update
            pieChart2.destroy();
            
            const options = {
                series: data.series,
                chart: {
                    width: '100%',
                    type: 'donut',
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
            
            // Destroy and recreate chart to ensure proper update
            pieChart3.destroy();
            
            const options = {
                series: data.series,
                chart: {
                    width: '100%',
                    type: 'pie',
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
            
            // Destroy and recreate chart
            lineChart.destroy();
            
            const options = {
                series: data.series,
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#1a1a1a', '#2d3748', '#4a5568', '#718096', '#a0aec0'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: 'Status Over Time',
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
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: '#1a1a1a'
                    }
                }
            };
            
            lineChart = new ApexCharts(document.querySelector("#lineChart"), options);
            lineChart.render();
        }

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