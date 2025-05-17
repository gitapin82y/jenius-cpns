@extends('layouts.admin')

@section('title', 'Manajemen Set Soal')

@push('after-style')
@endpush

@include('admin.setsoal.modal')

@section('content')
    
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Free ( Jumlah Set Soal )</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countFree">{{ $countFree }} <small class="text-muted">Set Soal</small></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ceban ( Jumlah Set Soal )</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countCeban">{{ $countCeban }} <small class="text-muted">Set Soal</small></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Saban ( Jumlah Set Soal )</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countSaban">{{ $countSaban }} <small class="text-muted">Set Soal</small></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Gocap ( Jumlah Set Soal )</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countGocap">{{ $countGocap }} <small class="text-muted">Set Soal</small></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 align-items-center justify-content-between row m-0">
            <div class="col-12 col-sm-6 p-0">
                <h4 class="m-0 font-weight-bold float-left text-primary">Manajemen Set Soal</h4>
            </div>
            <div class="col-12 col-sm-6">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success float-left mt-3 mt-sm-0 float-sm-right shadow-sm" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Data
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Jumlah Soal</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
<script>
    $('#paket_id').select2({
        placeholder: "Pilih Paket",
        allowClear: true
    });
    // Initialize DataTable
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("setsoal.index") }}',
        columns: [
            { data: 'title', name: 'title' },
            { data: 'jumlah_soal', name: 'jumlah_soal' },
            { data: 'paket_id', name: 'paket_id',render: function(data) {
                let paketClass = '';
                switch(data) {
                    case 'Free':
                        paketClass = 'badge-primary';
                        break;
                    case 'Ceban':
                        paketClass = 'badge-success';
                        break;
                    case 'Saban':
                        paketClass = 'badge-info';
                        break;
                    case 'Gocap':
                        paketClass = 'badge-warning';
                        break;
                    default:
                        paketClass = 'badge-secondary';
                }
                return `<span class="badge ${paketClass}">${data}</span>`;
            } },
            { data: 'status', name: 'status',render: function(data) {
                let status = '';
                switch(data) {
                    case 'Draf':
                        status = 'badge-warning';
                        break;
                    case 'Publish':
                        status = 'badge-success';
                        break;
                    default:
                        status = 'badge-secondary';
                }
                return `<span class="badge ${status}">${data}</span>`;
            } },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    function updateCounts() {
    $.ajax({
        url: '{{ route("setsoal.counts") }}',
        method: 'GET',
        success: function(response) {
            $('#countFree').text(response.countFree + ' Set Soal');
            $('#countCeban').text(response.countCeban + ' Set Soal');
            $('#countSaban').text(response.countSaban + ' Set Soal');
            $('#countGocap').text(response.countGocap + ' Set Soal');
        }
        });
    }



    // Edit setSoal function to fill the form in the modal
    function editSetSoal(setSoal) {
        $('.modal-title').html('Edit Set Soal');
        $('#setSoalId').val(setSoal.id);
        $('#title').val(setSoal.title);
        $('#paket_id').val(setSoal.paket_id);
        $('#paket_id').trigger('change');

        const baseUrl = window.location.origin;
        const updateUrl = `${baseUrl}/setsoal/${setSoal.id}`;
        $('#setSoalForm').attr('action', updateUrl);
        $('#setSoalForm').find('input[name="_method"]').remove(); // Remove any existing _method input
        $('#setSoalForm').append('<input type="hidden" name="_method" value="PUT">'); // Add _method input for PUT

        $('#exampleModal').modal('show');
    }

    // Handle form submission via AJAX
    $('#setSoalForm').submit(function(e) {
        e.preventDefault();

        // Get the form action (POST or PUT)
        let formAction = $(this).attr('action');
        let formData = $(this).serialize();

        $.ajax({
            url: formAction,
            method: "POST",
            data: formData,
            success: function(response) {
                $('#exampleModal').modal('hide');
                $('#dataTable').DataTable().ajax.reload();
                updateCounts();
                Swal.fire({
                toast: true,
                icon: 'success',
                text: response.message,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                });
                // alert('Data set_soal berhasil disimpan.');
            },
            error: function(response) {
                // Clear previous error messages
                $('.text-danger').remove();
                $('.is-invalid').removeClass('is-invalid');

                // Show error messages
                let errors = response.responseJSON.errors;
                $.each(errors, function(field, messages) {
                    let input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.after(`<div class="text-danger">${messages[0]}</div>`);
                });
                Swal.fire({
                toast: true,
                icon: 'error',
                text: 'Ulangi, Terdapat Kesalahan!',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                });
            }
        });
    });

    // Reset form and error messages when modal is hidden
    $('#exampleModal').on('hidden.bs.modal', function () {
        $('#setSoalForm').find('input, textarea, select').each(function() {
            $(this).val('');
        });

        $('.modal-title').html('Tambah Set Soal');
        $('#paket_id').val('').trigger('change');

        $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');

        $('#setSoalForm').find('input[name="_method"]').remove(); // Remove _method input
        $('#setSoalForm').attr('action', '{{ route("setsoal.store") }}'); // Reset action to store
        $('#setSoalForm').attr('method', 'POST'); // Reset form method to POST

        // Dynamically generate CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        $('#setSoalForm').find('input[name="_token"]').remove(); // Remove any existing token
        $('#setSoalForm').prepend(`<input type="hidden" name="_token" value="${token}">`); // Add fresh token
    });

    
    function changeStatus(id, status) {
        $.ajax({
            url: `/setsoal/change-status/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                $('#dataTable').DataTable().ajax.reload();
                Swal.fire({
                toast: true,
                icon: 'success',
                text: response.message,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                });
            },
            error: function(xhr) {
                Swal.fire({
                toast: true,
                icon: 'success',
                text: 'Ulangi, Terdapat Kesalahan!',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                });
            }
        });
}

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Set soal akan terhapus beserta isi soal tryout",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteData(id);
            }
        });
    }

    function deleteData(id) {
        const url = '/setsoal/' + id; // Update with your delete route

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                Swal.fire({
                toast: true,
                icon: 'success',
                text: 'Data berhasil dihapus',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                });
                $('#dataTable').DataTable().ajax.reload();
                updateCounts();


            } else {
                Swal.fire({
                toast: true,
                icon: 'error',
                text: 'Terdapat kesalahan',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                });
            }
        })
        .catch(() => {
            Swal.fire(
                'Gagal!',
                'Terjadi kesalahan saat menghapus data.',
                'error'
            );
        });
    }

    // Show modal if there are validation errors on page load
    @if ($errors->any())
    $(document).ready(function() {
        $('#exampleModal').modal('show');
    });
    @endif
</script>


@endpush
