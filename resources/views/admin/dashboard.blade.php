@extends('layouts.admin')

@section('title', 'Dashboard')

@push('after-style')

@endpush

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Pengguna</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Tryouts Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Tryout Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTryouts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Materials Count Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Jumlah Materi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMaterials }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tryout Questions Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Jumlah Soal Tryout</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuestions }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Area Chart - Pengguna Tryout -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tren Pengguna Tryout (6 Bulan Terakhir)</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart - Penyelesaian Tryout -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Penyelesaian Tryout Resmi</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="tryoutCompletionPieChart"></canvas>
                </div>
                <div class="mt-5 text-center small">
                    {{-- <p>Dari total {{ $totalEligibleUsers }} pengguna dengan akses tryout</p> --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('after-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
// Area Chart - Pengguna Tryout
const ctx = document.getElementById("myAreaChart");
const myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: "Pengguna Tryout",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: {!! json_encode($userData) !!},
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value;
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    return 'Pengguna: ' + tooltipItem.yLabel;
                }
            }
        }
    }
});

// Pie Chart - Penyelesaian Tryout dengan persentase tampil langsung
const tryoutCompletionPieChart = document.getElementById("tryoutCompletionPieChart");
const completionData = {!! json_encode(array_map(function($item) { return $item['count']; }, $completedTryoutData)) !!};
const completionLabels = {!! json_encode(array_map(function($item) { return $item['title']; }, $completedTryoutData)) !!};
const completionPercentages = {!! json_encode(array_map(function($item) { return $item['percentage']; }, $completedTryoutData)) !!};

const tryoutCompletionChart = new Chart(tryoutCompletionPieChart, {
    type: 'doughnut',
    data: {
        labels: completionLabels,
        datasets: [{
            data: completionData,
            backgroundColor: [
                '#8DD14F', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69',
                '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#e02d1b'
            ],
            hoverBackgroundColor: [
                '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#e02d1b', '#3a3b45',
                '#224aba', '#13875c', '#24818c', '#c4910a', '#c81f12'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, data) {
                    const dataset = data.datasets[tooltipItem.datasetIndex];
                    const currentValue = dataset.data[tooltipItem.index];
                    const percentage = completionPercentages[tooltipItem.index];
                    return data.labels[tooltipItem.index] + ': ' + currentValue + ' pengguna ';
                }
            }
        },
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                usePointStyle: true,
                padding: 20
            }
        },
        cutoutPercentage: 80,
        // Plugin untuk menampilkan persentase di dalam donut chart
        plugins: [{
            beforeDraw: function(chart) {
                const width = chart.chart.width;
                const height = chart.chart.height;
                const ctx = chart.chart.ctx;
                
                ctx.restore();
                const fontSize = (height / 114).toFixed(2);
                ctx.font = fontSize + "em sans-serif";
                ctx.textBaseline = "middle";
                
                const text = chart.config.data.datasets[0].data.reduce((a, b) => a + b, 0) + " Pengguna";
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 2;
                
                ctx.fillText(text, textX, textY);
                ctx.save();
            }
        }]
    },
});
</script>
@endpush
