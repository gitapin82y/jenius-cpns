<div class="modal fade" id="materiModal" tabindex="-1" role="dialog" aria-labelledby="materiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materiModalLabel">Tambah Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="materialForm" action="{{ route('materi.store') }}" method="POST">
                @csrf
                <input type="hidden" id="materialId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Judul Materi</label>
                        <input type="text" required class="form-control" id="title" name="title" value="{{ old('title') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipe">Kategori & Tipe</label>
                        <select name="tipe" id="tipe" class="form-control" required>
                            <option>Pilih Kategori & Tipe</option>
                            <!-- KATEGORI TWK -->
                            <option disabled>Tes Wawasan Kebangsaan (TWK)</option>
                            <option value="Nasionalisme" class="TWK">Nasionalisme</option>
                            <option value="Integritas" class="TWK">Integritas</option>
                            <option value="Bela Negara" class="TWK">Bela Negara</option>
                            <option value="Pilar Negara" class="TWK">Pilar Negara</option>
                            <option value="Bahasa Indonesia" class="TWK">Bahasa Indonesia</option>
                            <!-- KATEGORI TIU -->
                            <option disabled>Tes Intelegensi Umum (TIU)</option>
                            <option value="Verbal (Analogi)" class="TIU">Verbal (Analogi)</option>
                            <option value="Verbal (Silogisme)" class="TIU">Verbal (Silogisme)</option>
                            <option value="Verbal (Analisis)" class="TIU">Verbal (Analisis)</option>
                            <option value="Numerik (Hitung Cepat)" class="TIU">Numerik (Hitung Cepat)</option>
                            <option value="Numerik (Deret Angka)" class="TIU">Numerik (Deret Angka)</option>
                            <option value="Numerik (Perbandingan Kuantitatif)" class="TIU">Numerik (Perbandingan Kuantitatif)</option>
                            <option value="Numerik (Soal Cerita)" class="TIU">Numerik (Soal Cerita)</option>
                            <option value="Figural (Analogi)" class="TIU">Figural (Analogi)</option>
                            <option value="Figural (Ketidaksamaan)" class="TIU">Figural (Ketidaksamaan)</option>
                            <option value="Figural (Serial)" class="TIU">Figural (Serial)</option>
                            <!-- KATEGORI TKP -->
                            <option disabled>Tes Karakteristik Pribadi (TKP)</option>
                            <option value="Pelayanan Publik" class="TKP">Pelayanan Publik</option>
                            <option value="Jejaring Kerja" class="TKP">Jejaring Kerja</option>
                            <option value="Sosial Budaya" class="TKP">Sosial Budaya</option>
                            <option value="Teknologi Informasi dan Komunikasi (TIK)" class="TKP">Teknologi Informasi dan Komunikasi (TIK)</option>
                            <option value="Profesionalisme" class="TKP">Profesionalisme</option>
                            <option value="Anti Radikalisme" class="TKP">Anti Radikalisme</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kata_kunci">Kata Kunci</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="kata_kunci" name="kata_kunci" 
                                   placeholder="Pisahkan dengan koma, atau kosongkan untuk auto-generate" 
                                   value="{{ old('kata_kunci') }}">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="generateKeywords">
                                    <i class="fas fa-magic"></i> Auto
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Kata kunci akan digunakan untuk sistem rekomendasi. Kosongkan untuk generate otomatis.
                        </small>
                        <div id="keywordSuggestions" class="mt-2" style="display:none;">
                            <small class="text-muted">Saran kata kunci:</small>
                            <div id="keywordList" class="d-flex flex-wrap mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Isi Materi</label>
                        <textarea name="content" id="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
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