@extends('layouts.admin')

@section('title', 'Laporan Sistem')

@push('after-style')
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 align-items-center justify-content-between row m-0">
            <div class="col-12 col-sm-6 p-0">
                <h4 class="m-0 font-weight-bold float-left text-primary">Laporan Sistem</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Error Code</th>
                            <th>Tipe Error</th>
                            <th>Pesan Error</th>
                            <th>Waktu</th>
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
        ajax: '{{ route("system-error.index") }}',
        columns: [
            { data: 'username', name: 'username' },
            { data: 'error_code', name: 'error_code' },
            { data: 'error_type', name: 'error_type' },
            { data: 'error_message', name: 'error_message' },
            { data: 'formatted_time', name: 'formatted_time' }
        ],
        order: [[4, 'desc']] // Order by error_time descendingly
    });
</script>
@endpush