<!-- Letakkan ini di resources/views/admin/pengguna/modal.blade.php -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Tambah Pengguna</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        <i class="fas fa-times"></i>
    </button>
</div>
            <form id="userForm" action="{{ route('pengguna.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" required class="form-control" id="name" name="name" value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" required class="form-control" id="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                          <label class="d-block mb-2">Pernah mengikuti CPNS?</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_cpns" id="is_cpns_yes_admin" value="1" {{ old('is_cpns')=='1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_cpns_yes_admin">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_cpns" id="is_cpns_no_admin" value="0" {{ old('is_cpns','0')=='0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_cpns_no_admin">Tidak</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}">
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
