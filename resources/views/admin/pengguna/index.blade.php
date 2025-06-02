@extends('layouts.admin')
 
@section('title', 'Manajemen Pengguna')

@push('after-style')

@endpush

@include('admin.pengguna.modal')
@section('content')

    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 align-items-center justify-content-between row m-0">
            <div class="col-12 col-sm-6 p-0">
                <h4 class="m-0 font-weight-bold float-left text-primary">Manajemen Pengguna</h4>
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
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Phone</th>
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
    ajax: '{{ route("pengguna.index") }}',
    columns: [
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'phone', name: 'phone' }, // New
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ]
});


    // Ubah user function to fill the form in the modal
    function editUser(user) {
        $('.modal-title').html('Ubah Pengguna');
        $('#userId').val(user.id);
        $('#name').val(user.name);
        $('#email').val(user.email);
        $('#phone').val(user.phone);
        $('#password').val(user.password);

        const baseUrl = window.location.origin;
        const updateUrl = `${baseUrl}/pengguna/${user.id}`;
        $('#userForm').attr('action', updateUrl);
        $('#userForm').find('input[name="_method"]').remove(); // Remove any existing _method input
        $('#userForm').append('<input type="hidden" name="_method" value="PUT">'); // Add _method input for PUT

        $('#exampleModal').modal('show');
        $('#password').attr('placeholder', 'Masukkan Jika Ingin Ganti Password');
    }

    // Handle form submission via AJAX
    $('#userForm').submit(function(e) {
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

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Pengguna akan terhapus beserta data yang ada",
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
        const url = '/pengguna/' + id; // Update with your delete route

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
    $('#exampleModal').on('hidden.bs.modal', function () {
        $('#userForm').find('input, textarea, select').each(function() {
            $(this).val('');
        });

        $('.modal-title').html('Tambah Pengguna');

        $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');

        $('#password').attr('placeholder', '');
        $('#userForm').find('input[name="_method"]').remove(); // Remove _method input
        $('#userForm').attr('action', '{{ route("pengguna.store") }}'); // Reset action to store
        $('#userForm').attr('method', 'POST'); // Reset form method to POST

        // Dynamically generate CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        $('#userForm').find('input[name="_token"]').remove(); // Remove any existing token
        $('#userForm').prepend(`<input type="hidden" name="_token" value="${token}">`); // Add fresh token
    });

    // Show modal if there are validation errors on page load
    @if ($errors->any())
    $(document).ready(function() {
        $('#exampleModal').modal('show');
    });
    @endif
</script>
@endpush

