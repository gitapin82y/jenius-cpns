@extends('layouts.admin')

@section('title', 'Manajemen Set Soal')

@push('after-style')
@endpush

@include('admin.setsoal.modal')

@section('content')
    


    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 align-items-center justify-content-between row m-0">
            <div class="col-12 col-sm-6 p-0">
                <h4 class="m-0 font-weight-bold float-left text-primary">Manajemen Set Soal</h4>
            </div>
            <div class="col-12 col-sm-6">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success float-left mt-3 mt-sm-0 float-sm-right shadow-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
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
                            <th>Kategori</th>
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
    // Initialize DataTable
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("setsoal.index") }}',
        columns: [
            { data: 'title', name: 'title' },
            { data: 'jumlah_soal', name: 'jumlah_soal' },
            { data: 'kategori', name: 'jumlah_soal' },
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



    // Ubah setSoal function to fill the form in the modal
    function editSetSoal(setSoal) {
        $('.modal-title').html('Ubah Set Soal');
        $('#setSoalId').val(setSoal.id);
        $('#title').val(setSoal.title);

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
