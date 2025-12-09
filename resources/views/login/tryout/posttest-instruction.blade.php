@extends('layouts.public')
@section('title', 'post test')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-clipboard-check"></i> Instruksi POSTTEST</h4>
                </div>
                <div class="card-body">
                    <h5>Anda akan mengerjakan POSTTEST</h5>
                    
                    <div class="alert alert-info mt-3">
                        <h6><strong>📋 Data Pretest Anda:</strong></h6>
                        <ul class="mb-0">
                            <li>Skor Pretest: <strong>{{ $pretest->twk_score + $pretest->tiu_score + $pretest->tkp_score }}</strong></li>
                            <li>Tanggal: {{ \Carbon\Carbon::parse($pretest->created_at)->format('d F Y, H:i') }}</li>
                            <li>Set Soal: {{ $setsoal->title }}</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><strong>⚠️ Perhatian:</strong></h6>
                        <ol class="mb-0">
                            <li>Posttest akan menggunakan <strong>soal yang sama</strong> dengan pretest</li>
                            <li>Pastikan Anda sudah <strong>mempelajari semua materi</strong> yang direkomendasikan</li>
                            <li>Kerjakan dengan <strong>serius dan fokus</strong></li>
                            <li>Hasil akan dibandingkan dengan pretest untuk menghitung peningkatan</li>
                        </ol>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="ready">
                        <label class="form-check-label" for="ready">
                            Saya sudah siap dan telah mempelajari semua materi yang direkomendasikan
                        </label>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tryout.result', $setsoal->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        
                        {{-- ✅ GUNAKAN ROUTE YANG BENAR: tryout.index --}}
                        <a href="{{ route('tryout.index', $setsoal->id) }}?test_type=posttest&pretest_id={{ $pretest->id }}" 
                           class="btn btn-success btn-lg"
                           id="startPosttestBtn">
                            <i class="fas fa-play"></i> Mulai POSTTEST
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('startPosttestBtn').addEventListener('click', function(e) {
    const checkbox = document.getElementById('ready');
    if (!checkbox.checked) {
        e.preventDefault();
        alert('Harap centang pernyataan kesediaan terlebih dahulu!');
        return false;
    }
    
    if (!confirm('Anda yakin sudah siap mengerjakan POSTTEST?\n\nPastikan sudah mempelajari semua materi yang direkomendasikan.')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection