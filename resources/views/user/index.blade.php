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
    </style>

</head>

<body class="font-sans">

    <!-- Div Header -->
    <div class="bg-[#9bd69e] py-4 px-10 mb-5 rounded-b-[2rem] flex items-center justify-between">
        <div class="text-[#1a1a1a] text-5xl"><i class="ti ti-user-question"></i></div>
        <div class="text-[2rem] font-bold text-[#1a1a1a]">Monitoring Leads</div>
        <div class="flex items-center gap-4">
            <div class="text-[#1a1a1a] text-5xl"><i class="ti ti-heart-rate-monitor"></i></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ml-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-semibold">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Div Layout -->
    <div class="flex gap-4 leading-6 px-4">

        <div class="flex-1">
            <div class="rounded-lg bg-[#9bd69e] p-4">
                <!-- Chart 1 -->
                <div id="pieChart1"></div>
                <div class="mt-4">
                    <div class="flex items-center gap-2 mb-2 bg-white p-2 rounded-lg">
                        <i class="ti ti-cash text-4xl"></i>
                        <span>Cash 50%</span>
                    </div>
                    <div class="flex items-center gap-2 mb-2 bg-white p-2 rounded-lg">
                        <i class="ti ti-wallet text-4xl"></i>
                        <span>Wallet 50%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-2" bis_skin_checked="1">
            <div class="flex justify-evenly mb-4 gap-4">
                <!-- Div Pie Chart 2 -->
                <div class="flex-1 rounded-lg bg-[#c8e6ca] p-4" id="pieChart2"></div>
                <!-- Div Pie Chart 3 -->
                <div class="flex-1 rounded-lg bg-[#c8e6ca] p-4" id="pieChart3"></div>
            </div>
            <!-- Div Line Chart -->
            <div class="rounded-lg bg-[#c8e6ca] p-4" id="lineChart"></div>
        </div>

        <!-- Div Calendar -->
        <div class="">
        <!-- <div class="flex-1"> -->
            <div class="rounded-lg bg-[#9bd69e] p-4">
                <div class="text-2xl font-semibold mb-2 text-center">Tanggal</div>

                <input type="text" id="flatpickr-range" class="" />
                <!-- <input type="text" id="flatpickr-range" class="hidden" /> -->
                <!-- <input type="text" id="flatpickr-range" class="" value="2024-06-01 to 2024-06-15" /> -->

                <!-- tempat kalender dirender -->
                <div id="flatpickr-range-container" class="rounded-lg p-2 shadow w-full flex justify-center"></div>
            </div>
        </div>




    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr('#flatpickr-range', {
            mode: 'range',               
            inline: true,                
            appendTo: document.getElementById('flatpickr-range-container'),
            dateFormat: 'Y-m-d',
            /* optional: tangani perubahan agar mudah dipakai di JS */
            onChange(selectedDates) {
                if (selectedDates.length === 2) {
                    const [startDate, endDate] = selectedDates;
                    console.log('Start:', startDate, 'End:', endDate);
                    // → bisa kamu kirim ke server, tampilkan ke UI, dll
                }
            }
        });
    </script>



    <script>
        $(document).ready(function () {
            // Pie Chart 1 - Lead Sources
            var pieOptions1 = {
                series: [35, 25, 20, 15, 5],
                chart: {
                    width: '100%',
                    type: 'pie',
                },
                labels: ['Website', 'Social Media', 'Referral', 'Email', 'Other'],
                colors: ['#1a1a1a', '#2d3748', '#4a5568', '#718096', '#a0aec0'],
                title: {
                    text: 'Lead Sources',
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

            var pieChart1 = new ApexCharts(document.querySelector("#pieChart1"), pieOptions1);
            pieChart1.render();

            // Pie Chart 2 - Lead Status
            var pieOptions2 = {
                series: [45, 30, 15, 10],
                chart: {
                    width: '100%',
                    type: 'donut',
                },
                labels: ['New', 'Contacted', 'Qualified', 'Converted'],
                colors: ['#1a1a1a', '#2d3748', '#4a5568', '#718096'],
                title: {
                    text: 'Lead Status',
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

            var pieChart2 = new ApexCharts(document.querySelector("#pieChart2"), pieOptions2);
            pieChart2.render();

            // Pie Chart 3 - Lead Conversion
            var pieOptions3 = {
                series: [65, 35],
                chart: {
                    width: '100%',
                    type: 'pie',
                },
                labels: ['Converted', 'Not Converted'],
                colors: ['#1a1a1a', '#718096'],
                title: {
                    text: 'Conversion Rate',
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

            var pieChart3 = new ApexCharts(document.querySelector("#pieChart3"), pieOptions3);
            pieChart3.render();

            // Line Chart - Leads Over Time
            var lineOptions = {
                series: [{
                    name: "Leads",
                    data: [30, 40, 35, 50, 49, 60, 70, 91, 125, 110, 135, 150]
                }],
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
                    text: 'Leads Over Time',
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
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
                }
            };

            var lineChart = new ApexCharts(document.querySelector("#lineChart"), lineOptions);
            lineChart.render();
        });
    </script>
</body>

</html>