<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Set Soal</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        <i class="fas fa-times"></i>
    </button>
            </div>
            <form id="setSoalForm" action="{{ route('setsoal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" required class="form-control" id="title" name="title" value="{{ old('title') }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="kategori">Kategori</label>
                        <select name="kategori" id="kategori" class="form-control" required>
                            <option value="Tryout">Tryout</option>
                            <option value="Latihan">Latihan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Buat Set Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>
