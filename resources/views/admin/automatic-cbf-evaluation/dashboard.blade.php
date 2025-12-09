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
            Sistem secara otomatis mengevaluasi relevansi rekomendasi berdasarkan <strong>kesamaan kata kunci</strong> (keyword intersection) antara soal dan materi.
            <br><strong>Relevan (TP)</strong> jika ada minimal 1 kata kunci yang sama. <strong>Tidak Relevan (FP)</strong> jika tidak ada kata kunci yang sama.
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Evaluasi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Evaluasi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_evaluations'] }}
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
                                {{ $stats['total_users'] }}
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
                                True Positive (TP)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_tp'] }}
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
                                False Positive (FP)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_fp'] }}
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
                                Rata-rata Precision
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ number_format($stats['average_precision'], 2) }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: {{ $stats['average_precision'] }}%"
                                            aria-valuenow="{{ $stats['average_precision'] }}"
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

    <!-- User Precision Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Precision Per Pengguna</h6>
            <button type="button" class="btn btn-info btn-sm" onclick="refreshTable()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            @if($stats['has_data'])
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
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted"></i>
                    <p class="text-muted mt-2">Belum ada data evaluasi otomatis</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Export Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-export"></i> Export Data untuk Analisis Python
            </h6>
        </div>
        <div class="card-body">
            <p class="text-muted">Export data evaluasi otomatis untuk analisis menggunakan Python.</p>
            
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
let table;

$(document).ready(function() {
    @if($stats['has_data'])
    // Initialize DataTable
    table = $('#userPrecisionTable').DataTable({
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

function refreshTable() {
    if (table) {
        table.ajax.reload();
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
                                    <th>Intersection</th>
                                    <th>Similarity</th>
                                    <th>Klasifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                response.evaluations.forEach(eval => {
                    const badge = eval.classification === 'TP' 
                        ? '<span class="badge badge-success">TP</span>'
                        : '<span class="badge badge-danger">FP</span>';
                    
                    html += `
                        <tr>
                            <td>${eval.set_soal.title}</td>
                            <td>${eval.soal.kategori} - ${eval.soal.tipe}</td>
                            <td>${eval.material.title.substring(0, 40)}...</td>
                            <td>${eval.intersection_count} kata</td>
                            <td>${(eval.similarity_score * 100).toFixed(2)}%</td>
                            <td>${badge}</td>
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