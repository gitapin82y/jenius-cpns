@extends('layouts.admin')

@section('title', 'Update Keywords')

@push('after-style')
<style>
.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.progress-custom {
    height: 8px;
    border-radius: 10px;
    background-color: rgba(255,255,255,0.3);
}

.progress-bar-custom {
    background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 10px;
}

.btn-update {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    color: white;
}

.preview-card {
    border: 1px solid #e3e6f0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fc;
}

.keyword-badge {
    background: #e74a3b;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    margin: 2px;
    display: inline-block;
}

.keyword-badge.new {
    background: #1cc88a;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h4 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key"></i> Update Keywords System
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-1">{{ $stats['total_materials'] }}</h3>
                        <p class="mb-2">Total Materi</p>
                        <small>{{ $stats['materials_with_keywords'] }} dengan keywords</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-book fa-2x opacity-75"></i>
                    </div>
                </div>
                <div class="progress progress-custom mt-3">
                    <div class="progress-bar progress-bar-custom" style="width: {{ $stats['total_materials'] > 0 ? ($stats['materials_with_keywords'] / $stats['total_materials']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-1">{{ $stats['total_soals'] }}</h3>
                        <p class="mb-2">Total Soal</p>
                        <small>{{ $stats['soals_with_keywords'] }} dengan keywords</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-question-circle fa-2x opacity-75"></i>
                    </div>
                </div>
                <div class="progress progress-custom mt-3">
                    <div class="progress-bar progress-bar-custom" style="width: {{ $stats['total_soals'] > 0 ? ($stats['soals_with_keywords'] / $stats['total_soals']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-1">{{ $stats['materials_without_keywords'] }}</h3>
                        <p class="mb-2">Materi Tanpa Keywords</p>
                        <small>Perlu diubah</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-1">{{ $stats['soals_without_keywords'] }}</h3>
                        <p class="mb-2">Soal Tanpa Keywords</p>
                        <small>Perlu diubah</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="font-weight-bold text-primary mb-0">Update Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <button class="btn btn-update btn-block" onclick="showPreview('all')">
                                <i class="fas fa-eye"></i> Preview All Changes
                            </button>
                            <small class="text-muted">Lihat perubahan yang akan dilakukan</small>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <button class="btn btn-update btn-block" onclick="updateKeywords('all')">
                                <i class="fas fa-sync-alt"></i> Update All Keywords
                            </button>
                            <small class="text-muted">Update semua materi & soal</small>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <button class="btn btn-update btn-block" onclick="updateKeywords('materials')">
                                <i class="fas fa-book"></i> Update Materials Only
                            </button>
                            <small class="text-muted">Hanya update materi</small>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <button class="btn btn-update btn-block" onclick="updateKeywords('soals')">
                                <i class="fas fa-question-circle"></i> Update Soals Only
                            </button>
                            <small class="text-muted">Hanya update soal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="row" id="previewSection" style="display:none;">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="font-weight-bold text-primary mb-0">
                        <i class="fas fa-eye"></i> Preview Changes
                    </h6>
                </div>
                <div class="card-body" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="row" id="resultsSection" style="display:none;">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="font-weight-bold text-success mb-0">
                        <i class="fas fa-check-circle"></i> Update Results
                    </h6>
                </div>
                <div class="card-body" id="resultsContent">
                    <!-- Results content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center text-white">
        <div class="spinner mb-3"></div>
        <h5>Processing Keywords...</h5>
        <p>Please wait while we update the keywords</p>
    </div>
</div>
@endsection

@push('after-script')
<script>
function showLoading() {
    $('#loadingOverlay').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
}

function showPreview(type) {
    showLoading();
    
    $.ajax({
        url: '{{ route("admin.keyword-update.preview") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            type: type,
            limit: 5
        },
        success: function(response) {
            hideLoading();
            
            if (response.success) {
                displayPreview(response.preview);
                $('#previewSection').show();
                $('html, body').animate({
                    scrollTop: $("#previewSection").offset().top
                }, 1000);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Terjadi kesalahan saat load preview', 'error');
        }
    });
}

function displayPreview(preview) {
    let html = '';
    
    if (preview.materials) {
        html += '<h6 class="text-primary mb-3"><i class="fas fa-book"></i> Preview Materials (5 samples)</h6>';
        preview.materials.forEach(function(item) {
            html += `
                <div class="preview-card">
                    <h6>${item.title} <span class="badge badge-info">${item.tipe}</span></h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Old Keywords:</strong><br>
                            ${item.old_keywords.map(k => `<span class="keyword-badge">${k}</span>`).join('')}
                        </div>
                        <div class="col-md-6">
                            <strong>New Keywords:</strong><br>
                            ${item.new_keywords.map(k => `<span class="keyword-badge new">${k}</span>`).join('')}
                        </div>
                    </div>
                    <small class="text-success">Reduced by keywords</small>
                </div>
            `;
        });
    }
    
    if (preview.soals) {
        html += '<h6 class="text-primary mb-3 mt-4"><i class="fas fa-question-circle"></i> Preview Soals (5 samples)</h6>';
        preview.soals.forEach(function(item) {
            html += `
                <div class="preview-card">
                    <h6>${item.pertanyaan} <span class="badge badge-secondary">${item.tipe}</span></h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Old Keywords :</strong><br>
                            ${item.old_keywords.map(k => `<span class="keyword-badge">${k}</span>`).join('')}
                        </div>
                        <div class="col-md-6">
                            <strong>New Keywords :</strong><br>
                            ${item.new_keywords.map(k => `<span class="keyword-badge new">${k}</span>`).join('')}
                        </div>
                    </div>
                    <small class="text-success">Reduced by keywords</small>
                </div>
            `;
        });
    }
    
    $('#previewContent').html(html);
}

function updateKeywords(type) {
    Swal.fire({
        title: 'Konfirmasi Update',
        text: `Apakah Anda yakin ingin update keywords untuk ${type}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Update!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            performUpdate(type);
        }
    });
}

function performUpdate(type) {
    showLoading();
    
    let url;
    switch(type) {
        case 'all':
            url = '{{ route("admin.keyword-update.all") }}';
            break;
        case 'materials':
            url = '{{ route("admin.keyword-update.materials") }}';
            break;
        case 'soals':
            url = '{{ route("admin.keyword-update.soals") }}';
            break;
    }
    
    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            hideLoading();
            
            if (response.success) {
                displayResults(response.data);
                $('#resultsSection').show();
                $('html, body').animate({
                    scrollTop: $("#resultsSection").offset().top
                }, 1000);
                
                Swal.fire('Success!', response.message, 'success');
                
                // Refresh page setelah 3 detik
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
         error: function(xhr, status, error) {
            hideLoading();
            console.log("AJAX Error:", {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            Swal.fire('Error', 'Terjadi kesalahan saat update keywords', 'error');
        }
    });
}

function displayResults(data) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Update Completed</h6>
                    <p>Keywords berhasil diubah pada: ${data.timestamp}</p>
                </div>
            </div>
        </div>
    `;
    
    if (data.materials) {
        html += `
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <h6 class="text-success">Materials Updated</h6>
                            <p class="mb-1">Total: ${data.materials.total}</p>
                            <p class="mb-1">Updated: ${data.materials.updated}</p>
                            <p class="mb-0">Failed: ${data.materials.failed}</p>
                        </div>
                    </div>
                </div>
        `;
    }
    
    if (data.soals) {
        html += `
                <div class="col-md-6">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <h6 class="text-info">Soals Updated</h6>
                            <p class="mb-1">Total: ${data.soals.total}</p>
                            <p class="mb-1">Updated: ${data.soals.updated}</p>
                            <p class="mb-0">Failed: ${data.soals.failed}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    $('#resultsContent').html(html);
}
</script>
@endpush