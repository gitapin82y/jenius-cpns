<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Soal</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        <i class="fas fa-times"></i>
    </button>
            </div>
            <div class="modal-body">
                <form id="soalForm" method="POST" action="{{ route('soal.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="set_soal_id" id="set_soal_id" value="{{ $setSoal->id }}">
                
                    <div class="form-group">
                        <label for="pertanyaan">Pertanyaan</label>
                        <input name="pertanyaan" id="pertanyaan" class="form-control" required></input>
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
                
                    <!-- The poin input field, disabled for TKP -->
                    <div class="form-group">
                        <label for="foto">Foto (Optional)</label>
                        <input type="file" name="foto" id="foto" accept=".png, .jpg, .jpeg" class="form-control">
                    </div>
                


                    <div class="form-group">
                                <label for="kata_kunci_soal">Kata Kunci</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="kata_kunci_soal" name="kata_kunci" 
                                           placeholder="Pisahkan dengan koma, atau kosongkan untuk auto-generate">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="generateSoalKeywords">
                                            <i class="fas fa-magic"></i> Auto
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Kata kunci untuk sistem rekomendasi CBF. Kosongkan untuk generate otomatis.
                                </small>
                                <div id="soalKeywordSuggestions" class="mt-2" style="display:none;">
                                    <small class="text-muted">Saran kata kunci:</small>
                                    <div id="soalKeywordList" class="d-flex flex-wrap mt-1"></div>
                                </div>
                            </div>
                
                    <div class="form-group">
                        <label for="jawaban_a">Jawaban A</label>
                        <div class="input-group">
                            <input type="text" name="jawaban_a" id="jawaban_a" class="form-control" required>
                            <input type="number" name="score_a" id="score_a" class="form-control TKP-fields" placeholder="Poin A" style="display: none; width: 50px; margin-left: 10px;">
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label for="jawaban_b">Jawaban B</label>
                        <div class="input-group">
                            <input type="text" name="jawaban_b" id="jawaban_b" class="form-control" required>
                            <input type="number" name="score_b" id="score_b" class="form-control TKP-fields" placeholder="Poin B" style="display: none; width: 50px; margin-left: 10px;">
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label for="jawaban_c">Jawaban C</label>
                        <div class="input-group">
                            <input type="text" name="jawaban_c" id="jawaban_c" class="form-control" required>
                            <input type="number" name="score_c" id="score_c" class="form-control TKP-fields" placeholder="Poin C" style="display: none; width: 50px; margin-left: 10px;">
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label for="jawaban_d">Jawaban D</label>
                        <div class="input-group">
                            <input type="text" name="jawaban_d" id="jawaban_d" class="form-control" required>
                            <input type="number" name="score_d" id="score_d" class="form-control TKP-fields" placeholder="Poin D" style="display: none; width: 50px; margin-left: 10px;">
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label for="jawaban_e">Jawaban E</label>
                        <div class="input-group">
                            <input type="text" name="jawaban_e" id="jawaban_e" class="form-control" required>
                            <input type="number" name="score_e" id="score_e" class="form-control TKP-fields" placeholder="Poin E" style="display: none; width: 50px; margin-left: 10px;">
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label for="jawaban_benar">Jawaban Benar</label>
                        <select name="jawaban_benar" id="jawaban_benar" class="form-control">
                            <option disabled selected>Pilih Jawaban Benar</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="poin">Poin</label>
                        <input type="number" name="poin" id="poin" class="form-control" disabled>
                    </div>
                
                    <div class="form-group">
                        <label for="pembahasan">Pembahasan</label>
                        <textarea name="pembahasan" id="pembahasan" class="form-control" required></textarea>
                    </div>
                
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
                
                <script>
                document.getElementById('tipe').addEventListener('change', function() {
                    const tipe = this.options[this.selectedIndex].className;
                    const poinField = document.getElementById('poin');
                    const TKPFields = document.querySelectorAll('.TKP-fields');
                    const jawabanSalah = document.querySelectorAll('.input-group .text-danger');
                    const jawabanBenar = document.getElementById('jawaban_benar');
                    jawabanSalah.forEach(field => field.style.display = 'none');
                    
                    if (tipe === 'TKP') {
                        poinField.disabled = true;
                        TKPFields.forEach(field => field.style.display = 'block');
                        poinField.value = 0;
                        jawabanBenar.disabled = true;
                        jawabanBenar.value = '';
                        // jawabanBenar.value = '';
                        // jawabanBenar.addEventListener('change', function() {
                        //     const selectedAnswer = this.value.toLowerCase();
                        //     const scoreInput = document.getElementById('score_' + selectedAnswer);
                        // });
                    } else {
                        poinField.disabled = false;
                        jawabanBenar.disabled = false;
                        TKPFields.forEach(field => field.style.display = 'none');
                    }
                });
                </script>
                
            </div>
        </div>
    </div>
</div>
