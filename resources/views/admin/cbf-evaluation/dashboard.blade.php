{{-- resources/views/admin/cbf-evaluation/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'CBF Evaluation Dashboard')

@section('content')
<div class="container-fluid">
    
    @if(!$stats['has_data'])
        <!-- Alert jika belum ada data -->
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading"><i class="fas fa-info-circle"></i> Belum Ada Data Evaluasi</h4>
            <p>Sistem CBF Evaluation belum memiliki data untuk dievaluasi. Untuk mulai evaluasi:</p>
            <hr>
            <ol>
                <li>Pastikan ada pengguna yang sudah mengerjakan tryout</li>
                <li>Sistem akan otomatis log rekomendasi yang diberikan</li>
                <li>Pengguna perlu melakukan evaluasi untuk memberikan feedback</li>
            </ol>
            <div class="alert alert-warning mt-3">
                <small><i class="fas fa-exclamation-triangle"></i> Belum ada evaluasi yang tersedia.</small>
            </div>
        </div>
    @endif

    <!-- CBF Metrics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Accuracy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['accuracy'] }}%
                            </div>
                            @if(!$stats['has_data'])
                                <small class="text-muted">No data available</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Precision
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['precision'] }}%
                            </div>
                            @if(!$stats['has_data'])
                                <small class="text-muted">No data available</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crosshairs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Recall
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['recall'] }}%
                            </div>
                            @if(!$stats['has_data'])
                                <small class="text-muted">No data available</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                F1-Score
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['f1_score'] }}%
                            </div>
                            @if(!$stats['has_data'])
                                <small class="text-muted">No data available</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $stats['total_evaluations'] }}</h3>
                                <p class="text-muted">Total Evaluations</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ $stats['user_evaluations'] }}</h3>
                                <p class="text-muted">User Evaluations</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ $stats['total_users_reviewed'] }}</h3>
                                <p class="text-muted">Users Participated</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $stats['pending_evaluations'] }}</h3>
                                <p class="text-muted">Pending Expert Review</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($stats['has_data'])
    <!-- Confusion Matrix -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Confusion Matrix</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle"></th>
                                <th colspan="2">Predicted</th>
                            </tr>
                            <tr>
                                <th>Relevant</th>
                                <th>Not Relevant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th rowspan="2" class="align-middle" style="writing-mode: vertical-rl; text-orientation: mixed;">Actual</th>
                            </tr>
                            <tr>
                                <td><strong>Relevant</strong></td>
                                <td class="bg-success text-white"><strong>{{ $stats['tp'] }}</strong><br><small>True Positive</small></td>
                                <td class="bg-warning"><strong>{{ $stats['fn'] }}</strong><br><small>False Negative</small></td>
                            </tr>
                            <tr>
                                <td><strong>Not Relevant</strong></td>
                                <td class="bg-danger text-white"><strong>{{ $stats['fp'] }}</strong><br><small>False Positive</small></td>
                                <td class="bg-success text-white"><strong>{{ $stats['tn'] }}</strong><br><small>True Negative</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">CBF Performance Chart</h6>
                </div>
                <div class="card-body">
                    <canvas id="cbfMetricsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Evaluations Management Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data Evaluasi CBF</h6>
            <div>
                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Hapus Terpilih
                </button>
                <button type="button" class="btn btn-info btn-sm mt-1" onclick="refreshTable()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($stats['has_data'])
                <div class="alert alert-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Peringatan:</strong>
                    Menghapus evaluasi akan menghapus <strong>SEMUA</strong> evaluasi dari pengguna tersebut dan mereset status review mereka.
                    Gunakan fitur ini untuk menghapus data evaluasi yang tidak valid atau asal-asalan.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="evaluationTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="3%">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th width="15%">Pengguna</th>
                                <th width="15%">Email</th>
                                <th width="15%">Tryout</th>
                                <th width="20%">Materi</th>
                                <th width="8%">Kategori</th>
                                <th width="8%">Penilaian</th>
                                <th width="8%">Similarity</th>
                                <th width="10%">Tanggal</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted"></i>
                    <p class="text-muted mt-2">Belum ada data evaluasi yang tersedia</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Selengkapnya Evaluasi -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-info-circle"></i> Selengkapnya Evaluasi Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="deleteFromDetailBtn">
                    <i class="fas fa-trash"></i> Hapus Evaluasi User Ini
                </button>
            </div>
        </div>
    </div>
</div>

@if($stats['has_data'])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart untuk CBF Metrics
const ctx = document.getElementById('cbfMetricsChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Accuracy', 'Precision', 'Recall', 'F1-Score'],
        datasets: [{
            label: 'CBF Performance (%)',
            data: [{{ $stats['accuracy'] }}, {{ $stats['precision'] }}, {{ $stats['recall'] }}, {{ $stats['f1_score'] }}],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endif
@endsection
@push('after-script')
<script>
$(document).ready(function() {
    @if($stats['has_data'])
    // Initialize DataTable
    var table = $('#evaluationTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.cbf-evaluation.data") }}',
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="evaluation-checkbox" value="' + row.id + '">';
                }
            },
            { data: 'user_name', name: 'user.name' },
            { data: 'user_email', name: 'user.email' },
            { data: 'tryout_title', name: 'setSoal.title' },
            { data: 'material_title', name: 'material.title' },
            { data: 'material_category', name: 'material.kategori' },
            { data: 'feedback_badge', name: 'user_feedback', orderable: false },
            { data: 'similarity_score', name: 'similarity_score' },
            { data: 'evaluation_date', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[8, 'desc']],
        pageLength: 25,
        language: {
            processing: "Memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            zeroRecords: "Tidak ada data yang ditemukan"
        }
    });

    // Handle select all checkbox
    $('#selectAll').on('change', function() {
        $('.evaluation-checkbox').prop('checked', this.checked);
        toggleBulkDeleteButton();
    });

    // Handle individual checkbox
    $(document).on('change', '.evaluation-checkbox', function() {
        var checkedCheckboxes = $('.evaluation-checkbox:checked').length;
       
       $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
       toggleBulkDeleteButton();
   });

   function toggleBulkDeleteButton() {
       var checkedCount = $('.evaluation-checkbox:checked').length;
       if (checkedCount > 0) {
           $('#bulkDeleteBtn').show();
           $('#bulkDeleteBtn').text('Hapus Terpilih (' + checkedCount + ')');
       } else {
           $('#bulkDeleteBtn').hide();
       }
   }

   // Handle bulk delete
   $('#bulkDeleteBtn').on('click', function() {
       var selectedIds = [];
       $('.evaluation-checkbox:checked').each(function() {
           selectedIds.push($(this).val());
       });

       if (selectedIds.length === 0) {
           Swal.fire('Peringatan', 'Pilih minimal satu evaluasi untuk dihapus', 'warning');
           return;
       }

       Swal.fire({
           title: 'Konfirmasi Hapus',
           html: `
               <p>Anda akan menghapus <strong>${selectedIds.length}</strong> evaluasi terpilih.</p>
               <div class="alert alert-danger">
                   <strong>Peringatan:</strong> Ini akan menghapus SEMUA evaluasi dari pengguna terkait dan mereset status review mereka.
               </div>
               <p>Apakah Anda yakin?</p>
           `,
           icon: 'warning',
           showCancelButton: true,
           confirmButtonColor: '#d33',
           cancelButtonColor: '#3085d6',
           confirmButtonText: 'Ya, Hapus!',
           cancelButtonText: 'Batal'
       }).then((result) => {
           if (result.isConfirmed) {
               bulkDeleteEvaluations(selectedIds);
           }
       });
   });

   function bulkDeleteEvaluations(ids) {
       $.ajax({
           url: '{{ route("admin.cbf-evaluation.bulk-delete") }}',
           method: 'POST',
           data: {
               _token: '{{ csrf_token() }}',
               evaluation_ids: ids
           },
           beforeSend: function() {
               $('#bulkDeleteBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
           },
           success: function(response) {
               if (response.success) {
                   Swal.fire({
                       title: 'Berhasil!',
                       html: `
                           <p>${response.message}</p>
                           <div class="mt-3">
                               <strong>Detail:</strong>
                               <ul class="text-left">
                                   ${response.deleted_users.map(user => 
                                       `<li>${user.name} (${user.email}) - ${user.evaluation_count} evaluasi</li>`
                                   ).join('')}
                               </ul>
                           </div>
                       `,
                       icon: 'success',
                       confirmButtonText: 'OK'
                   }).then(() => {
                       table.ajax.reload();
                       $('#selectAll').prop('checked', false);
                       toggleBulkDeleteButton();
                   });
               } else {
                   Swal.fire('Error', response.message, 'error');
               }
           },
           error: function(xhr) {
               let errorMessage = 'Terjadi kesalahan saat menghapus evaluasi';
               if (xhr.responseJSON && xhr.responseJSON.message) {
                   errorMessage = xhr.responseJSON.message;
               }
               Swal.fire('Error', errorMessage, 'error');
           },
           complete: function() {
               $('#bulkDeleteBtn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Terpilih');
           }
       });
   }
   @endif
});

// Function untuk refresh table
function refreshTable() {
   @if($stats['has_data'])
   $('#evaluationTable').DataTable().ajax.reload();
   $('#selectAll').prop('checked', false);
   toggleBulkDeleteButton();
   @endif
   
   Swal.fire({
       title: 'Berhasil!',
       text: 'Data berhasil direfresh',
       icon: 'success',
       timer: 1500,
       showConfirmButton: false
   });
}

// Function untuk show detail evaluasi
function showEvaluationDetail(evaluationId) {
   $.ajax({
       url: `/admin/cbf-evaluation/${evaluationId}/detail`,
       method: 'GET',
       beforeSend: function() {
           $('#detailModalBody').html(`
               <div class="text-center">
                   <i class="fas fa-spinner fa-spin fa-2x"></i>
                   <p class="mt-2">Memuat detail evaluasi...</p>
               </div>
           `);
           $('#detailModal').modal('show');
       },
       success: function(response) {
           if (response.success) {
               const evaluation = response.evaluation;
               const userEvaluations = response.user_evaluations;
               
               let html = `
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <h6 class="fw-bold">Informasi Pengguna</h6>
                           <table class="table table-sm">
                               <tr><td><strong>Nama:</strong></td><td>${evaluation.user.name}</td></tr>
                               <tr><td><strong>Email:</strong></td><td>${evaluation.user.email}</td></tr>
                               <tr><td><strong>Status Review:</strong></td><td>
                                   <span class="badge ${evaluation.user.is_review ? 'bg-success' : 'bg-warning'}">
                                       ${evaluation.user.is_review ? 'Sudah Review' : 'Belum Review'}
                                   </span>
                               </td></tr>
                           </table>
                       </div>
                       <div class="col-md-6">
                           <h6 class="fw-bold">Statistik Evaluasi</h6>
                           <table class="table table-sm">
                               <tr><td><strong>Total Evaluasi:</strong></td><td>${response.total_evaluations}</td></tr>
                               <tr><td><strong>Relevan:</strong></td><td class="text-success">${response.relevant_count}</td></tr>
                               <tr><td><strong>Tidak Relevan:</strong></td><td class="text-danger">${response.not_relevant_count}</td></tr>
                           </table>
                       </div>
                   </div>
                   
                   <div class="mb-3">
                       <h6 class="fw-bold">Tryout: ${evaluation.set_soal.title}</h6>
                       <small class="text-muted">Tanggal evaluasi: ${new Date(evaluation.created_at).toLocaleDateString('id-ID', {
                           year: 'numeric', month: 'long', day: 'numeric',
                           hour: '2-digit', minute: '2-digit'
                       })}</small>
                   </div>
               `;
               
               if (evaluation.user_comment) {
                   html += `
                       <div class="mb-3">
                           <h6 class="fw-bold">Komentar Pengguna</h6>
                           <div class="alert alert-info">
                               ${evaluation.user_comment}
                           </div>
                       </div>
                   `;
               }
               
               html += `
                   <div class="mb-3">
                       <h6 class="fw-bold">Selengkapnya Evaluasi Materi</h6>
                       <div class="table-responsive">
                           <table class="table table-bordered table-sm">
                               <thead>
                                   <tr>
                                       <th>Materi</th>
                                       <th>Kategori</th>
                                       <th>Tipe</th>
                                       <th>Similarity</th>
                                       <th>Penilaian</th>
                                   </tr>
                               </thead>
                               <tbody>
               `;
               
               userEvaluations.forEach(eval => {
                   const feedbackBadge = eval.user_feedback === true ? 
                       '<span class="badge bg-success"><i class="fas fa-thumbs-up"></i> Relevan</span>' :
                       '<span class="badge bg-danger"><i class="fas fa-thumbs-down"></i> Tidak Relevan</span>';
                   
                   html += `
                       <tr>
                           <td>${eval.material.title.substring(0, 40)}${eval.material.title.length > 40 ? '...' : ''}</td>
                           <td><span class="badge bg-secondary">${eval.material.kategori}</span></td>
                           <td><small>${eval.material.tipe}</small></td>
                           <td>${(eval.similarity_score * 100).toFixed(1)}%</td>
                           <td>${feedbackBadge}</td>
                       </tr>
                   `;
               });
               
               html += `
                               </tbody>
                           </table>
                       </div>
                   </div>
               `;
               
               $('#detailModalBody').html(html);
               $('#deleteFromDetailBtn').data('user-id', evaluation.user_id);
               $('#deleteFromDetailBtn').data('user-name', evaluation.user.name);
           }
       },
       error: function(xhr) {
           $('#detailModalBody').html(`
               <div class="alert alert-danger">
                   <i class="fas fa-exclamation-triangle"></i>
                   Gagal memuat detail evaluasi: ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
               </div>
           `);
       }
   });
}

// Function untuk delete evaluasi
function deleteEvaluation(evaluationId, userName) {
   Swal.fire({
       title: 'Konfirmasi Hapus',
       html: `
           <p>Anda akan menghapus <strong>SEMUA</strong> evaluasi dari pengguna:</p>
           <div class="alert alert-info">
               <strong>${userName}</strong>
           </div>
           <div class="alert alert-danger">
               <strong>Peringatan:</strong> Tindakan ini akan menghapus semua evaluasi pengguna tersebut dan mereset status review mereka sehingga bisa memberikan evaluasi lagi.
           </div>
           <p>Apakah Anda yakin?</p>
       `,
       icon: 'warning',
       showCancelButton: true,
       confirmButtonColor: '#d33',
       cancelButtonColor: '#3085d6',
       confirmButtonText: 'Ya, Hapus!',
       cancelButtonText: 'Batal'
   }).then((result) => {
       if (result.isConfirmed) {
           $.ajax({
               url: `/admin/cbf-evaluation/${evaluationId}`,
               method: 'DELETE',
               data: {
                   _token: '{{ csrf_token() }}'
               },
               success: function(response) {
                   if (response.success) {
                       Swal.fire({
                           title: 'Berhasil!',
                           html: `
                               <p>${response.message}</p>
                               <div class="alert alert-success">
                                   <small><strong>Detail:</strong> ${response.deleted_count} record dihapus dari pengguna ${response.user_name}</small>
                               </div>
                           `,
                           icon: 'success',
                           confirmButtonText: 'OK'
                       }).then(() => {
                           @if($stats['has_data'])
                           $('#evaluationTable').DataTable().ajax.reload();
                           @endif
                       });
                   } else {
                       Swal.fire('Error', response.message, 'error');
                   }
               },
               error: function(xhr) {
                   let errorMessage = 'Terjadi kesalahan saat menghapus evaluasi';
                   if (xhr.responseJSON && xhr.responseJSON.message) {
                       errorMessage = xhr.responseJSON.message;
                   }
                   Swal.fire('Error', errorMessage, 'error');
               }
           });
       }
   });
}

// Delete from detail modal
$('#deleteFromDetailBtn').on('click', function() {
   const userId = $(this).data('user-id');
   const userName = $(this).data('user-name');
   
   $('#detailModal').modal('hide');
   
   setTimeout(() => {
       Swal.fire({
           title: 'Reset Status Review',
           html: `
               <p>Anda akan mereset status review dan menghapus semua evaluasi dari:</p>
               <div class="alert alert-info">
                   <strong>${userName}</strong>
               </div>
               <p>Pengguna ini akan dapat memberikan evaluasi lagi setelah direset.</p>
           `,
           icon: 'warning',
           showCancelButton: true,
           confirmButtonColor: '#d33',
           cancelButtonColor: '#3085d6',
           confirmButtonText: 'Ya, Reset!',
           cancelButtonText: 'Batal'
       }).then((result) => {
           if (result.isConfirmed) {
               resetUserReview(userId, userName);
           }
       });
   }, 300);
});

function resetUserReview(userId, userName) {
   $.ajax({
       url: '{{ route("admin.cbf-evaluation.reset-user") }}',
       method: 'POST',
       data: {
           _token: '{{ csrf_token() }}',
           user_id: userId
       },
       success: function(response) {
           if (response.success) {
               Swal.fire({
                   title: 'Berhasil!',
                   html: `
                       <p>${response.message}</p>
                       <div class="alert alert-success">
                           <small>Pengguna <strong>${response.user_name}</strong> sekarang dapat memberikan evaluasi lagi.</small>
                       </div>
                   `,
                   icon: 'success',
                   confirmButtonText: 'OK'
               }).then(() => {
                   @if($stats['has_data'])
                   $('#evaluationTable').DataTable().ajax.reload();
                   @endif
               });
           } else {
               Swal.fire('Error', response.message, 'error');
           }
       },
       error: function(xhr) {
           let errorMessage = 'Terjadi kesalahan saat mereset status review';
           if (xhr.responseJSON && xhr.responseJSON.message) {
               errorMessage = xhr.responseJSON.message;
           }
           Swal.fire('Error', errorMessage, 'error');
       }
   });
}
</script>
@endpush