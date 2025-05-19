@extends('layouts.admin')

@section('title', 'Manajemen Materi')

@push('after-style')
@endpush

@include('admin.materi.modal')
@include('admin.materi.detail')

@section('content')
<!-- Count Cards Row -->
<div class="row mb-4">
    <!-- TWK Count Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Materi TWK</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $countTWK }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TIU Count Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Materi TIU</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $countTIU }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-brain fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TKP Count Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Materi TKP</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $countTKP }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Count Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Materi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $countTotal }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
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
                <h4 class="m-0 font-weight-bold float-left text-primary">Manajemen Materi</h4>
            </div>
            <div class="col-12 col-sm-6">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success float-left mt-3 mt-sm-0 float-sm-right shadow-sm" data-bs-toggle="modal" data-bs-target="#materiModal">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Materi
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
            <div class="col-md-5 mb-3">
                <label for="filter-kategori">Kategori</label>
                <select class="form-control" id="filter-kategori">
                    <option value="">Semua Kategori</option>
                    <option value="TWK">TWK</option>
                    <option value="TIU">TIU</option>
                    <option value="TKP">TKP</option>
                </select>
            </div>
            <div class="col-md-5 mb-3">
                <label for="filter-tipe">Tipe/Subkategori</label>
                <select class="form-control" id="filter-tipe">
                    <option value="">Semua Tipe</option>
                    @foreach($tipes as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button id="btn-reset-filter" class="btn btn-secondary btn-block">Reset</button>
            </div>
        </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Konten</th>
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

    // Initialize DataTable - PERBAIKAN: Simpan instance DataTable dalam variabel 'table'
let table = $('#dataTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('materi.index') }}",
        data: function(d) {
            d.kategori = $('#filter-kategori').val();
            d.tipe = $('#filter-tipe').val();
        }
    },
    columns: [
        { data: 'title', name: 'title' },
        { data: 'kategori', name: 'kategori' },
        { data: 'tipe', name: 'tipe' },
        { data: 'excerpt', name: 'excerpt' },
        { data: 'status', name: 'status', render: function(data) {
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
        }},
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ]
});

// Apply filters - SEKARANG AKAN BERFUNGSI
$('#filter-kategori, #filter-tipe').change(function() {
    table.draw();
});

// Reset filters - SEKARANG AKAN BERFUNGSI
$('#btn-reset-filter').click(function() {
    $('#filter-kategori').val('');
    $('#filter-tipe').val('');
    table.draw();
});
    // Edit material function to fill the form in the modal
    function editMaterial(material) {
        $('.modal-title').html('Edit Materi');
        $('#materialId').val(material.id);
        $('#title').val(material.title);
        $('#tipe').val(material.tipe);
        $('#tipe').trigger('change');
        $('#content').val(material.content);

        const baseUrl = window.location.origin;
        const updateUrl = `${baseUrl}/materi/${material.id}`;
        $('#materialForm').attr('action', updateUrl);
        $('#materialForm').find('input[name="_method"]').remove(); // Remove any existing _method input
        $('#materialForm').append('<input type="hidden" name="_method" value="PUT">'); // Add _method input for PUT

        $('#materiModal').modal('show');
    }

    function showDetailMaterial(material) {
        $('#modalTitle').text(material.title);
        $('#modalKategori').text(material.kategori);
        $('#modalTipe').text(material.tipe);
        $('#modalContent').html(material.content);
        $('#detailMateriModal').modal('show');
    }

    // Handle form submission via AJAX
    $('#materialForm').submit(function(e) {
        e.preventDefault();

        // Get the form action (POST or PUT)
        let formAction = $(this).attr('action');
        let formData = $(this).serialize();

        $.ajax({
            url: formAction,
            method: "POST",
            data: formData,
            success: function(response) {
                $('#materiModal').modal('hide');
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

    function changeStatus(id, status) {
        $.ajax({
            url: `/materi/change-status/${id}`,
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
                    icon: 'error',
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
            text: "Materi akan terhapus beserta data terkait",
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
        const url = '/materi/' + id;

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
                    text: 'Materi berhasil dihapus',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                $('#dataTable').DataTable().ajax.reload();
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

    // Reset form and error messages when modal is hidden
    $('#materiModal').on('hidden.bs.modal', function () {
        $('#materialForm').find('input, textarea, select').each(function() {
            $(this).val('');
        });

        $('.modal-title').html('Tambah Materi');

        $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');

        $('#materialForm').find('input[name="_method"]').remove(); // Remove _method input
        $('#materialForm').attr('action', '{{ route("materi.store") }}'); // Reset action to store
        $('#materialForm').attr('method', 'POST'); // Reset form method to POST

        // Dynamically generate CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        $('#materialForm').find('input[name="_token"]').remove(); // Remove any existing token
        $('#materialForm').prepend(`<input type="hidden" name="_token" value="${token}">`); // Add fresh token
    });

    // Show modal if there are validation errors on page load
    @if ($errors->any())
    $(document).ready(function() {
        $('#materiModal').modal('show');
    });
    @endif
</script>
@endpush