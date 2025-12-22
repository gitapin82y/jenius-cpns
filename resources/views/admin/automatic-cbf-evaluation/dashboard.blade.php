@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-robot"></i> Dashboard Evaluasi Otomatis CBF
        </h1>
    </div>

    <!-- Alert jika ada error -->
    @if(isset($stats['error_message']))
    <div class="alert alert-danger">
        <strong>Error:</strong> {{ $stats['error_message'] }}
    </div>
    @endif

    <!-- Info Box -->
    <div class="alert alert-info">
        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Tentang Evaluasi Otomatis</h6>
        <p class="mb-0">
            Sistem secara otomatis mengevaluasi relevansi rekomendasi berdasarkan <strong>threshold similarity >= 0.6</strong>.
            <br><strong>Relevan (TP)</strong> jika similarity >= 0.6. <strong>Tidak Relevan (FP)</strong> jika similarity < 0.6.
            <br>User dapat memberikan penilaian manual yang akan <strong>menimpa evaluasi otomatis</strong> untuk penelitian.
        </p>
    </div>

    <!-- Manual Evaluation Summary -->
    @if(isset($manualStats) && is_object($manualStats))
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-users"></i> Evaluasi Manual oleh Pengguna
            </h6>
        </div>
        <div class="card-body">
            @if(property_exists($manualStats, 'has_data') && $manualStats->has_data)
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Catatan:</strong> Evaluasi manual memiliki <strong>prioritas lebih tinggi</strong> dibanding evaluasi otomatis untuk penelitian. Data ini digunakan untuk menghitung precision akhir.
                </div>

                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Dinilai Manual
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $manualStats->total_manual }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    True Positive (Manual)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $manualStats->manual_tp }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-danger h-100">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    False Positive (Manual)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $manualStats->manual_fp }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Rata-rata Precision
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($manualStats->manual_precision, 2) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>Belum Ada Evaluasi Manual</h5>
                    <p class="mb-0">Belum ada pengguna yang memberikan penilaian manual terhadap rekomendasi materi.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- User Manual Evaluation Table -->
    @if(isset($userStats) && $userStats->isNotEmpty())
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table"></i> Evaluasi Manual Per Pengguna
            </h6>
            <button type="button" class="btn btn-info btn-sm" onclick="location.reload()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="userManualStatsTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th>Total Dinilai</th>
                            <th>TP</th>
                            <th>FP</th>
                            <th>Precision (%)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userStats as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $user->user_name }}</strong></td>
                            <td>{{ $user->user_email }}</td>
                            <td>
                                <span class="badge badge-primary badge-pill">
                                    {{ $user->total_evaluated }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-pill">
                                    {{ $user->tp }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-danger badge-pill">
                                    {{ $user->fp }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $precision = $user->user_precision;
                                    $badgeColor = 'success';
                                    if ($precision < 50) {
                                        $badgeColor = 'danger';
                                    } elseif ($precision < 70) {
                                        $badgeColor = 'warning';
                                    }
                                @endphp
                                <span class="badge badge-{{ $badgeColor }} badge-lg">
                                    {{ number_format($precision, 2) }}%
                                </span>
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-info btn-sm" 
                                        onclick="showUserDetail({{ $user->user_id }})">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <th colspan="3" class="text-right"><strong>Total / Rata-rata:</strong></th>
                            <th><strong>{{ $userStats->sum('total_evaluated') }}</strong></th>
                            <th><strong>{{ $userStats->sum('tp') }}</strong></th>
                            <th><strong>{{ $userStats->sum('fp') }}</strong></th>
                            <th>
                                <strong>{{ number_format($userStats->avg('user_precision'), 2) }}%</strong>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Export Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-file-export"></i> Export Data Manual (Untuk Penelitian)
            </h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                <i class="fas fa-info-circle"></i> 
                Export ini <strong>HANYA</strong> mencakup data yang sudah dinilai manual oleh user. Data ini digunakan untuk analisis precision dalam penelitian.
            </p>
            
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('admin.export.user-manual-evaluations') }}" 
                       class="btn btn-success btn-block btn-lg">
                        <i class="fas fa-download"></i> Export Data Manual Lengkap (CSV)
                    </a>
                    <small class="text-muted">
                        Berisi: user, soal, materi, similarity, user_feedback, dll
                    </small>
                </div>
                
                <div class="col-md-6">
                    <a href="{{ route('admin.export.user-manual-precision') }}" 
                       class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-chart-bar"></i> Export Precision Per User (CSV)
                    </a>
                    <small class="text-muted">
                        Berisi: user_id, total_evaluated, TP, FP, precision
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison: Automatic vs Manual -->
    @if(isset($autoStats) && isset($manualStats) && 
        is_object($autoStats) && is_object($manualStats) && 
        property_exists($autoStats, 'has_data') && $autoStats->has_data &&
        property_exists($manualStats, 'has_data') && $manualStats->has_data)
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-balance-scale"></i> Perbandingan: Evaluasi Otomatis vs Manual
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Metode Evaluasi</th>
                            <th>Total Evaluasi</th>
                            <th>TP</th>
                            <th>FP</th>
                            <th>Precision (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Otomatis (Threshold 0.6)</strong></td>
                            <td>{{ $autoStats->total_evaluations }}</td>
                            <td class="text-success"><strong>{{ $autoStats->total_tp }}</strong></td>
                            <td class="text-danger"><strong>{{ $autoStats->total_fp }}</strong></td>
                            <td><span class="badge badge-info badge-lg">{{ number_format($autoStats->average_precision, 2) }}%</span></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Manual (User Feedback)</strong></td>
                            <td>{{ $manualStats->total_manual }}</td>
                            <td class="text-success"><strong>{{ $manualStats->manual_tp }}</strong></td>
                            <td class="text-danger"><strong>{{ $manualStats->manual_fp }}</strong></td>
                            <td><span class="badge badge-success badge-lg">{{ number_format($manualStats->manual_precision, 2) }}%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-warning mt-3">
                <strong><i class="fas fa-exclamation-triangle"></i> Catatan untuk Penelitian:</strong>
                <ul class="mb-0 mt-2">
                    <li>Gunakan data <strong>Manual (User Feedback)</strong> sebagai ground truth untuk precision akhir</li>
                    <li>Data otomatis dapat digunakan sebagai pembanding untuk menunjukkan efektivitas threshold 0.6</li>
                    <li>Jika ada perbedaan signifikan, jelaskan di BAB V sebagai limitasi sistem otomatis</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards (Otomatis) -->
    @if(isset($autoStats) && is_object($autoStats))
    <div class="row">
        <!-- Total Evaluasi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Evaluasi (Otomatis)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $autoStats->total_evaluations ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pengguna
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $autoStats->total_users ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- True Positive (TP) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                TP (Otomatis)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $autoStats->total_tp ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- False Positive (FP) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                FP (Otomatis)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $autoStats->total_fp ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Precision Card -->
    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Rata-rata Precision (Otomatis - Threshold 0.6)
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ number_format($autoStats->average_precision ?? 0, 2) }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: {{ $autoStats->average_precision ?? 0 }}%"
                                            aria-valuenow="{{ $autoStats->average_precision ?? 0 }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- User Precision Table (Otomatis) -->
    @if(isset($autoStats) && is_object($autoStats) && property_exists($autoStats, 'has_data') && $autoStats->has_data)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Precision Per Pengguna (Evaluasi Otomatis)</h6>
            <button type="button" class="btn btn-info btn-sm" onclick="refreshAutomaticTable()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="userPrecisionTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th>Total Rekomendasi</th>
                            <th>TP</th>
                            <th>FP</th>
                            <th>Precision (%)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Export Section (Otomatis) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-export"></i> Export Data Otomatis (Untuk Perbandingan)
            </h6>
        </div>
        <div class="card-body">
            <p class="text-muted">Export data evaluasi otomatis (berdasarkan threshold 0.6) untuk analisis perbandingan.</p>
            
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('admin.export.automatic-cbf-evaluations') }}" 
                       class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Export Data Lengkap (CSV)
                    </a>
                    <small class="text-muted">Berisi: user, soal, materi, keywords, intersection, classification</small>
                </div>
                
                <div class="col-md-6">
                    <a href="{{ route('admin.export.automatic-precision-per-user') }}" 
                       class="btn btn-primary btn-block">
                        <i class="fas fa-chart-bar"></i> Export Precision Per User (CSV)
                    </a>
                    <small class="text-muted">Berisi: user_id, total_recommendations, TP, FP, precision</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail User -->
<div class="modal fade" id="userDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user"></i> Detail Evaluasi Pengguna
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="userDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let manualTable;
let automaticTable;

$(document).ready(function() {
    // Initialize Manual Stats Table
    @if(isset($userStats) && $userStats->isNotEmpty())
    manualTable = $('#userManualStatsTable').DataTable({
        "pageLength": 25,
        "order": [[6, "desc"]], // Sort by precision descending
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
    @endif

    // Initialize Automatic Table
    @if(isset($autoStats) && is_object($autoStats) && property_exists($autoStats, 'has_data') && $autoStats->has_data)
    automaticTable = $('#userPrecisionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.automatic-cbf-evaluation.user-data') }}",
        columns: [
            { data: 'user_name', name: 'users.name' },
            { data: 'user_email', name: 'users.email' },
            { data: 'total_recommendations', name: 'total_recommendations' },
            { data: 'tp', name: 'tp' },
            { data: 'fp', name: 'fp' },
            { 
                data: 'precision', 
                name: 'precision',
                render: function(data) {
                    let color = 'success';
                    if (data < 50) color = 'danger';
                    else if (data < 70) color = 'warning';
                    
                    return `<span class="badge badge-${color}">${data}%</span>`;
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']]
    });
    @endif
});

function refreshAutomaticTable() {
    if (automaticTable) {
        automaticTable.ajax.reload();
    }
}

function showUserDetail(userId) {
    $('#userDetailModal').modal('show');
    $('#userDetailContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: `/admin/automatic-cbf-evaluation/user/${userId}/detail`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Informasi Pengguna</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Nama:</strong></td><td>${response.user.name}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${response.user.email}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Statistik</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Total:</strong></td><td>${response.stats.total}</td></tr>
                                <tr><td><strong>TP:</strong></td><td class="text-success">${response.stats.tp}</td></tr>
                                <tr><td><strong>FP:</strong></td><td class="text-danger">${response.stats.fp}</td></tr>
                                <tr><td><strong>Precision:</strong></td><td><strong>${response.stats.precision}%</strong></td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-3">Riwayat Evaluasi</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Tryout</th>
                                    <th>Soal</th>
                                    <th>Materi</th>
                                    <th>Similarity</th>
                                    <th>Auto</th>
                                    <th>Manual</th>
                                    <th>Final</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                response.evaluations.forEach(eval => {
                    const autoBadge = eval.classification === 'TP' 
                        ? '<span class="badge badge-success">TP</span>'
                        : '<span class="badge badge-danger">FP</span>';
                    
                    let manualBadge = '-';
                    if (eval.user_feedback !== null) {
                        manualBadge = eval.user_feedback 
                            ? '<span class="badge badge-success">Relevan</span>'
                            : '<span class="badge badge-danger">Tidak</span>';
                    }
                    
                    const finalBadge = eval.final_classification === 'TP'
                        ? '<span class="badge badge-success">TP</span>'
                        : '<span class="badge badge-danger">FP</span>';
                    
                    html += `
                        <tr>
                            <td>${eval.set_soal.title}</td>
                            <td>${eval.soal.kategori} - ${eval.soal.tipe}</td>
                            <td>${eval.material.title.substring(0, 30)}...</td>
                            <td>${(eval.similarity_score * 100).toFixed(2)}%</td>
                            <td>${autoBadge}</td>
                            <td>${manualBadge}</td>
                            <td>${finalBadge}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                $('#userDetailContent').html(html);
            } else {
                $('#userDetailContent').html(`
                    <div class="alert alert-danger">
                        ${response.message}
                    </div>
                `);
            }
        },
        error: function() {
            $('#userDetailContent').html(`
                <div class="alert alert-danger">
                    Gagal mengambil data. Silakan coba lagi.
                </div>
            `);
        }
    });
}
</script>
@endsection