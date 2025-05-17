<!-- Detail Soal Modal -->
<div class="modal fade" id="detailSoalModal" tabindex="-1" aria-labelledby="detailSoalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Soal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Foto:</label>
                    <div id="modalFoto"></div>
                </div>
                <div class="form-group">
                    <label>Kategori & Tipe</label>
                    <div class="d-flex">
                        <strong id="modalKategori"></strong>
                        &nbsp;>&nbsp; 
                        <p id="modalTipe"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label>Pertanyaan <small><span id="modalPoin"></span></small> :</label>
                    <p id="modalPertanyaan"></p>
                </div>
                <div class="form-group">
                    <label>Jawaban</label>
                    <div class="d-flex">
                        <p class="mr-1">A.)</p>
                        <p id="modalJawabanA"></p>
                        &nbsp;
                        <small id="modalPoinJawabanA"></small>
                    </div>
                    <div class="d-flex">
                        <p class="mr-1">B.)</p>
                        <p id="modalJawabanB"></p>
                        &nbsp;
                        <small id="modalPoinJawabanB"></small>
                    </div>
                    <div class="d-flex">
                        <p class="mr-1">C.)</p>
                        <p id="modalJawabanC"></p>
                        &nbsp;
                        <small id="modalPoinJawabanC"></small>
                    </div>
                    <div class="d-flex">
                        <p class="mr-1">D.)</p>
                        <p id="modalJawabanD"></p>
                        &nbsp;
                        <small id="modalPoinJawabanD"></small>
                    </div>
                    <div class="d-flex">
                        <p class="mr-1">E.)</p>
                        <p id="modalJawabanE"></p>
                        &nbsp;
                        <small id="modalPoinJawabanE"></small>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <label>Jawaban Benar:</label>
                    &nbsp;
                    <p id="modalJawabanBenar" class="text-success"></p>
                </div>
                <div class="form-group">
                    <label>Pembahasan:</label>
                    <p id="modalPembahasan"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
