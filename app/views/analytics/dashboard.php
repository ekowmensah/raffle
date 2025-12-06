<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-chart-bar"></i> Analytics Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Time Range Selector -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="changePeriod(7)">7 Days</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changePeriod(30)">30 Days</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changePeriod(90)">90 Days</button>
                    </div>
                </div>
            </div>

            <!-- Revenue & Sales Row -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Revenue Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-credit-card"></i> Payment Methods</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentMethodChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Performance -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trophy"></i> Campaign Performance Comparison</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="campaignPerformanceChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Player Analytics Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-plus"></i> Player Growth</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="playerGrowthChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Ticket Sales Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="ticketSalesTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hourly Pattern -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock"></i> Peak Hours Analysis (Last 7 Days)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="hourlyPatternChart" height="60"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
let currentPeriod = 30;
let charts = {};

function changePeriod(days) {
    currentPeriod = days;
    
    // Update button states
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Reload charts
    loadRevenueTrend();
    loadPlayerGrowth();
    loadTicketSales();
}

function loadRevenueTrend() {
    fetch(`<?= url('analytics/getRevenueTrend') ?>?days=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            if (charts.revenue) charts.revenue.destroy();
            
            charts.revenue = new Chart(document.getElementById('revenueTrendChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Revenue (GHS)',
                        data: data.revenue,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }, {
                        label: 'Transactions',
                        data: data.transactions,
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgba(255, 159, 64, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'GHS ' + value.toFixed(0);
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        });
}

function loadPaymentMethods() {
    fetch('<?= url('analytics/getPaymentMethodDistribution') ?>')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('paymentMethodChart'), {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.counts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        });
}

function loadCampaignPerformance() {
    fetch('<?= url('analytics/getCampaignPerformance') ?>')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('campaignPerformanceChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Revenue (GHS)',
                        data: data.revenue,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }, {
                        label: 'Tickets Sold',
                        data: data.tickets,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'GHS ' + value.toFixed(0);
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        });
}

function loadPlayerGrowth() {
    fetch(`<?= url('analytics/getPlayerGrowth') ?>?days=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            if (charts.players) charts.players.destroy();
            
            charts.players = new Chart(document.getElementById('playerGrowthChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'New Players',
                        data: data.new_players,
                        borderColor: 'rgb(153, 102, 255)',
                        backgroundColor: 'rgba(153, 102, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Cumulative',
                        data: data.cumulative,
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgba(255, 159, 64, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
}

function loadTicketSales() {
    fetch(`<?= url('analytics/getTicketSalesTrend') ?>?days=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            if (charts.tickets) charts.tickets.destroy();
            
            charts.tickets = new Chart(document.getElementById('ticketSalesTrendChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Tickets Sold',
                        data: data.tickets,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
}

function loadHourlyPattern() {
    fetch('<?= url('analytics/getHourlySalesPattern') ?>')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('hourlyPatternChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Transactions',
                        data: data.transactions,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
}

// Load all charts on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRevenueTrend();
    loadPaymentMethods();
    loadCampaignPerformance();
    loadPlayerGrowth();
    loadTicketSales();
    loadHourlyPattern();
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
