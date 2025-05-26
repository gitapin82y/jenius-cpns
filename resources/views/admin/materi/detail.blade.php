<!-- Selengkapnya Materi Modal -->
<div class="modal fade" id="detailMateriModal" tabindex="-1" aria-labelledby="detailMateriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailMateriModalLabel">Selengkapnya Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kategori & Tipe</label>
                    <div class="d-flex">
                        <strong id="modalKategori"></strong>
                        &nbsp;>&nbsp; 
                        <p id="modalTipe" class="mb-0"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label>Judul:</label>
                    <h5 id="modalTitle"></h5>
                </div>
                <div class="form-group">
                    <label>Kata Kunci:</label>
                    <div id="modalKataKunci"></div>
                </div>
                
                <div class="form-group">
                    <label>Isi Materi:</label>
                    <div id="modalContent" class="p-2 border rounded"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>