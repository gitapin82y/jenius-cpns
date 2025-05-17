@extends('layouts.admin')

@section('title', 'Manajemen Soal')

@push('after-style')
<style>
    .modal-body {
    overflow-wrap: break-word; /* Memecah kata yang panjang */
    word-break: break-all; /* Memecah kata jika diperlukan */
}
.input-group > .custom-file, .input-group > .custom-select, .input-group > .form-control, .input-group > .form-control-plaintext{
    width: 60%;
}
</style>
@endpush

@include('admin.soal.modal')
@include('admin.soal.detail')

@section('content')
    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 align-items-center justify-content-between row m-0">
            <div class="col-12 col-sm-2">
                <a href="{{url('setsoal')}}" class="btn btn-warning float-left mt-3 mt-sm-0 shadow-sm">
                    <i class="fas fa-chevron-left fa-sm text-white-50"></i> Kembali
                </a>
            </div>
            <div class="col-12 col-sm-8">
                <h5 class="m-0 mt-3 mt-sm-0 font-weight-bold text-left text-sm-center text-primary"><small class="text-secondary">Manajemen Soal</small> <br> {{ $setSoal->title }}</h5>
            </div>
            <div class="col-12 col-sm-2">
                <button type="button" class="btn btn-success float-left mt-3 mt-sm-0 float-sm-right shadow-sm" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Soal
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Poin</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban Benar</th>
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
            ajax: '{{ route("soal.index", $setSoal->id) }}',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // Sequential numbering starts from 1
                    }
                },
                { data: 'kategori', name: 'kategori' },
                { data: 'tipe', name: 'tipe' },
                { data: 'poin', name: 'poin' },
                { data: 'pertanyaan', name: 'pertanyaan' },
                { data: 'jawaban_benar', name: 'jawaban_benar' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        // Handle form submission via AJAX
        $('#soalForm').submit(function(e) {
            e.preventDefault();
            // Get the form action (POST or PUT)
            let formAction = $(this).attr('action');
            var formData = new FormData(this);

            $.ajax({
                url: formAction,
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
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
                        input.after(`<br><div class="text-danger">${messages[0]}</div>`);
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

        function editSoal(soal) {
            if (soal.kategori == 'TKP') {
                $('.TKP-fields').show(); // Menampilkan semua field score
                $('#poin').prop('disabled', true); // Disable field poin
                $('#jawaban_benar').prop('disabled', true); 

                $('#jawaban_benar').val('');
                $('#poin').val('');
            $('#jawaban_benar').trigger('change');
            } else {
            $('#poin').val(soal.poin);
                $('#jawaban_benar').val(soal.jawaban_benar);
            $('#jawaban_benar').trigger('change');
                $('.TKP-fields').hide(); // Sembunyikan field score jika bukan TKP
                $('#poin').prop('disabled', false); // Enable field poin
                $('#jawaban_benar').prop('disabled', false); 
            }

        $('.modal-title').html('Edit Soal');
        $('#kategori').val(soal.kategori);
        $('#kategori').trigger('change');
            $('#tipe').val(soal.tipe);
            $('#tipe').trigger('change');
            $('#pertanyaan').val(soal.pertanyaan);
            $('#jawaban_a').val(soal.jawaban_a);
            $('#score_a').val(soal.score_a);
            
            $('#jawaban_b').val(soal.jawaban_b);
            $('#score_b').val(soal.score_b);
            
            $('#jawaban_c').val(soal.jawaban_c);
            $('#score_c').val(soal.score_c);
            
            $('#jawaban_d').val(soal.jawaban_d);
            $('#score_d').val(soal.score_d);
            
            $('#jawaban_e').val(soal.jawaban_e);
            $('#score_e').val(soal.score_e);
            $('#pembahasan').val(soal.pembahasan);

        const baseUrl = window.location.origin;
        const updateUrl = `${baseUrl}/soal/${soal.id}`;
        $('#soalForm').attr('action', updateUrl);
        $('#soalForm').find('input[name="_method"]').remove(); // Remove any existing _method input
        $('#soalForm').append('<input type="hidden" name="_method" value="PUT">'); // Add _method input for PUT

        $('#exampleModal').modal('show');
    }

    

    function showDetailSoalModal(soal) {
    $('#modalPertanyaan').text(soal.pertanyaan);
    $('#modalJawabanA').text(soal.jawaban_a);
    $('#modalJawabanB').text(soal.jawaban_b);
    $('#modalJawabanC').text(soal.jawaban_c);
    $('#modalJawabanD').text(soal.jawaban_d);
    $('#modalJawabanE').text(soal.jawaban_e);
    $('#modalPembahasan').text(soal.pembahasan);
    $('#modalKategori').text(soal.kategori);
    $('#modalTipe').text(soal.tipe);

    if(soal.kategori == "TKP"){
    $('#modalJawabanBenar').text('A-E');
    $('#modalPoin').text('');
    $('#modalPoinJawabanA').text('('+soal.score_a+' Poin)');
    $('#modalPoinJawabanB').text('('+soal.score_b+' Poin)');
    $('#modalPoinJawabanC').text('('+soal.score_c+' Poin)');
    $('#modalPoinJawabanD').text('('+soal.score_d+' Poin)');
    $('#modalPoinJawabanE').text('('+soal.score_e+' Poin)');
    }else{
    $('#modalJawabanBenar').text(soal.jawaban_benar);
    $('#modalPoin').text('('+ soal.poin+' Poin)');
    $('#modalPoinJawabanA').text('');
    $('#modalPoinJawabanB').text('');
    $('#modalPoinJawabanC').text('');
    $('#modalPoinJawabanD').text('');
    $('#modalPoinJawabanE').text('');
    }

    $('.modal-title').html('Detail Soal');

    if (soal.foto) {
        $('#modalFoto').html(`<img src="/storage/${soal.foto}" alt="Soal Foto" class="img-fluid">`);
    } else {
        $('#modalFoto').html('<p>Tidak ada foto tersedia</p>');
    }

    $('#detailSoalModal').modal('show');
}

           
    $('#exampleModal').on('hidden.bs.modal', function () {
        $('#soalForm').find('input, textarea, select').each(function() {
                $(this).val('');
            });

            var setSoalId = @json($setSoal->id);
            $('#set_soal_id').val(setSoalId);
          

            $('.modal-title').html('Tambah Soal');
            
            $('.text-danger').remove();
            $('.is-invalid').removeClass('is-invalid');

            $('#soalForm').find('input[name="_method"]').remove(); // Remove _method input
            $('#soalForm').attr('action', '{{ route("soal.store") }}'); // Reset action to store
            $('#soalForm').attr('method', 'POST'); // Reset form method to POST

            // Dynamically generate CSRF token
            const token = $('meta[name="csrf-token"]').attr('content');
            $('#soalForm').find('input[name="_token"]').remove(); // Remove any existing token
            $('#soalForm').prepend(`<input type="hidden" name="_token" value="${token}">`); // Add fresh token
        });
</script>
@endpush
