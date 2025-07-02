// Function to render model chart
function renderModelChart(data) {
    const ctx = document.getElementById('modelChart');
    if (!ctx) {
        console.error('Canvas element with id "modelChart" not found');
        return;
    }

    // Destroy existing chart if it exists
    if (window.modelChartInstance) {
        window.modelChartInstance.destroy();
    }

    const labels = data.map(item => item.model);
    const counts = data.map(item => item.count);

    window.modelChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Function to render status chart
function renderStatusChart(data) {
    const ctx = document.getElementById('statusChart');
    if (!ctx) {
        console.error('Canvas element with id "statusChart" not found');
        return;
    }

    // Destroy existing chart if it exists
    if (window.statusChartInstance) {
        window.statusChartInstance.destroy();
    }

    const labels = data.map(item => item.status);
    const counts = data.map(item => item.count);

    window.statusChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah',
                data: counts,
                backgroundColor: [
                    '#28a745',
                    '#dc3545',
                    '#ffc107',
                    '#17a2b8'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
