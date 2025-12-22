@extends('layouts.public')

@section('title', 'Hasil Tryout SKD')

@push('after-style')
<style>
.box-shadow {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.scrollable {
    max-height: 345px;
    overflow-y: auto;
}
.badge-category {
    background-color: #0dcaf01d;
    color: #8DD14F;
    padding: 10px;
    border-radius: 10px;
}
.total_score {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}
.badge-lulus {
    background-color: rgb(81, 113, 255);
    color: #ffff;
    padding: 5px 10px;
    margin-left: 10px;
    border-radius: 8px;
    font-size: 18px;
}

.bg-score {
    background-image: url('/assets/img/banner-score.png');
    background-position: top;
    background-repeat: no-repeat;
    background-size: cover;
    width: 100%;
    border-radius: 10px;
}
.recommendation-item {
    border-left: 4px solid #8DD14F;
    padding: 10px;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}
.recommendation-item a{
    color: #8DD14F;
}
.similarity-score {
    font-size: 12px;
    color: #6c757d;
}
.video-item {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 10px;
    transition: transform 0.2s;
}
.video-item a{
    color: #8DD14F;
}
.video-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.video-thumbnail {
    width: 60px;
    height: 45px;
    object-fit: cover;
    border-radius: 4px;
}
.cbf-table {
    font-size: 12px;
}
.cbf-table th, .cbf-table td {
    padding: 4px 8px;
    text-align: center;
}
.vector-1 {
    background-color: #d4edda;
    font-weight: bold;
}
.vector-0 {
    background-color: #f8d7da;
}
.step-indicator {
    background: linear-gradient(45deg, #8DD14F, #5B902B);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    margin: 5px 0;
}
</style>
@endpush

@section('content')
<div class="container py-4">

    <div class="row g-3">
        <!-- Kiri - Grafik -->
        <div class="col-lg-12 p-0">
           <!-- Header Score Section -->
<div class="card mb-3 p-0" style="border: none">
    <div class="card-body row bg-score mx-0 text-white">
       <div class="pt-md-0 col-12 col-md-6 align-self-center">
        <h5 class="fw-bold text-white">
            Total Poin Hasil {{ $tryoutInfo['is_tryout_resmi'] ? 'Tryout CPNS' : 'Latihan ' . implode(', ', $tryoutInfo['kategori_focus']) }}
        </h5>
        <h2 class="fw-bold text-white">
            @php
                $totalScore = 0;
                if ($tryoutInfo['is_tryout_resmi']) {
                    $totalScore = ($hasilTryout->twk_score ?? 0) + ($hasilTryout->tiu_score ?? 0) + ($hasilTryout->tkp_score ?? 0);
                } else {
                    // Hitung score hanya untuk kategori yang relevan
                    foreach($tryoutInfo['kategori_focus'] as $kategori) {
                        $totalScore += $hasilTryout->{strtolower($kategori) . '_score'} ?? 0;
                    }
                }
            @endphp
            {{ $totalScore }}
            <small style="font-size: 16px;color:white;">/ {{ $hasilTryout->total_poin }}</small>
             <p class="badge-lulus my-0 d-inline-block">
                <small>{{ ($totalScore >= ($hasilTryout->total_poin * 0.6)) ? 'Lulus' : 'Tidak Lulus' }}</small>
            </p>
        </h2>
        <div class="mb-2">
            <strong>Total Soal</strong>
             <div class="col-12 text-start">
                <span class="badge rounded-pill mt-2 py-2 px-4 bg-success">Benar: {{ $hasilTryout->total_benar }}</span>
                <span class="badge rounded-pill mt-2 py-2 px-4 bg-danger">Salah: {{ $hasilTryout->total_salah }}</span>
                <span class="badge rounded-pill mt-2 py-2 px-4 bg-secondary">Kosong: {{ $hasilTryout->total_kosong }}</span>
            </div>
        </div>
        <a href="{{ route('tryout.pembahasan', $skbSetSoal->id) }}" class="btn bg-white text-primary mt-2">Lihat Pembahasan {{ $tryoutInfo['is_tryout_resmi'] ? 'Tryout' : 'Latihan' }} dan Rekomendasi Materi</a>
    </div>
      <div class="pt-md-0 pt-4 col-12 col-md-6 text-md-end text-start d-none d-md-block">
        <img src="{{asset('assets/img/banner-logo.png')}}" alt="Score Image">
    </div>
    </div>
</div>

<!-- Grafik Kategori -->
<div class="card">
    <div class="card-header bg-light">
        <strong>
            @if($tryoutInfo['is_tryout_resmi'])
                Total Poin Per-kategori
            @else
                Hasil {{ implode(', ', $tryoutInfo['kategori_focus']) }}
            @endif
        </strong>
    </div>
    <div class="card-body">
        <canvas id="chartKategori" height="150"></canvas>
        <div class="row text-center my-3">
            <div class="col-12 fw-bold mb-1">
                Hasil Nilai
            </div>
            <div class="col-12 d-flex flex-wrap justify-content-center">
                @if($tryoutInfo['is_tryout_resmi'])
                    <!-- Tryout Resmi: Tampilkan semua kategori -->
                    <div class="col badge bg-primary mx-1 mb-2">TWK {{ $hasilTryout->twk_score }} Poin</div>
                    <div class="col badge bg-primary mx-1 mb-2">TIU {{ $hasilTryout->tiu_score }} Poin</div>
                    <div class="col badge bg-primary mx-1 mb-2">TKP {{ $hasilTryout->tkp_score }} Poin</div>
                @else
                    <!-- Latihan: Tampilkan hanya kategori yang relevan -->
                    @foreach($tryoutInfo['kategori_focus'] as $kategori)
                        <div class="col badge bg-primary mx-1 mb-2">
                            {{ $kategori }} {{ $hasilTryout->{strtolower($kategori) . '_score'} }} Poin
                        </div>
                    @endforeach
                @endif
            </div>
            
            @if($tryoutInfo['is_tryout_resmi'])
                <!-- Passing Grade hanya untuk tryout resmi -->
                <div class="col-12 fw-bold mt-3 mb-1">
                    Nilai Ambang Batas (Estimasi)
                </div>
                <div class="col-12 d-flex flex-wrap justify-content-center">
                    <div class="col badge bg-secondary mx-1 mb-2">TWK 65 Poin</div>
                    <div class="col badge bg-secondary mx-1 mb-2">TIU 80 Poin</div>
                    <div class="col badge bg-secondary mx-1 mb-2">TKP 166 Poin</div>
                </div>
            @else
                <!-- Info untuk latihan -->
                <div class="col-12 mt-3">
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Ini adalah hasil latihan {{ implode(', ', $tryoutInfo['kategori_focus']) }}. 
                            Nilai ambang batas akan ditampilkan pada tryout resmi.
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
         
        </div>


     
    </div>

    <div class="row">
        <!-- Card Evaluasi Sistem Rekomendasi Materi -->
{{-- @php
    $hasRecommendations = collect($recommendations['recommendations'] ?? [])->flatten(1)->isNotEmpty();
    $userHasReviewed = Auth::user()->is_review; // Cek dari kolom is_review di tabel users
@endphp

@if($hasRecommendations && !$userHasReviewed)
    <!-- Tampilkan card evaluasi jika belum pernah mengevaluasi -->
    <div class="card mt-4">
        <div class="card-body">
            <h6 class="fw-bold">Evaluasi Sistem Rekomendasi Materi</h6>
            <p class="mb-2">Bantu kami meningkatkan kualitas rekomendasi materi dengan memberikan penilaian relevansi materi yang direkomendasikan.</p>
            
            <div class="alert alert-info">
                <small>
                    <i class="fas fa-info-circle"></i> 
                    Anda hanya dapat memberikan penilaian <strong>satu kali</strong> untuk membantu penelitian kami.
                </small>
            </div>
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cbfEvaluationModal">
                <i class="fas fa-star"></i> Berikan Penilaian Rekomendasi
            </button>
        </div>
    </div>
@elseif($hasRecommendations && $userHasReviewed)
    <!-- Tampilkan pesan terima kasih dengan link ke UAT form -->
    <div class="card mt-4 border-success">
        <div class="card-body">
            <h6 class="fw-bold text-success">
                <i class="fas fa-check-circle"></i> Evaluasi Sistem Rekomendasi Materi
            </h6>
            <div class="alert alert-success mb-0">
                <h6 class="alert-heading">Terima kasih atas evaluasi Anda!</h6>
                <p class="mb-3">Anda telah memberikan penilaian untuk sistem rekomendasi materi. Kontribusi Anda sangat membantu penelitian ini.</p>
                
                @php
                    // Ambil statistik evaluasi user untuk semua tryout yang pernah dievaluasi
                    $userEvaluations = \App\Models\CBFEvaluation::where('user_id', Auth::id())
                        ->where('evaluation_source', 'user')
                        ->whereNotNull('user_feedback')
                        ->get();
                    
                    $totalEvaluated = $userEvaluations->count();
                    $relevantCount = $userEvaluations->where('user_feedback', true)->count();
                    $notRelevantCount = $userEvaluations->where('user_feedback', false)->count();
                    
                    // Ambil evaluasi terakhir untuk tanggal
                    $lastEvaluation = $userEvaluations->sortByDesc('created_at')->first();
                @endphp
                
                <div class="row text-center mb-3">
                    <div class="col-md-4">
                        <div class="text-primary">
                            <i class="fas fa-list-check fa-2x"></i>
                            <h5 class="mt-2">{{ $totalEvaluated }}</h5>
                            <small>Total Dievaluasi</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-success">
                            <i class="fas fa-thumbs-up fa-2x"></i>
                            <h5 class="mt-2">{{ $relevantCount }}</h5>
                            <small>Relevan</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-danger">
                            <i class="fas fa-thumbs-down fa-2x"></i>
                            <h5 class="mt-2">{{ $notRelevantCount }}</h5>
                            <small>Tidak Relevan</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Call to Action untuk UAT Form -->
                <div class="bg-light p-3 rounded">
                    <h6 class="text-primary">
                        <i class="fas fa-clipboard-list"></i> Langkah Selanjutnya
                    </h6>
                    <p class="mb-2">Untuk melengkapi penelitian, kami mengundang Anda mengisi kuesioner User Acceptance Testing (UAT) mengenai pengalaman menggunakan sistem ini.</p>
                    
                    <button type="button" class="btn btn-primary" id="openUATForm">
                        <i class="fas fa-external-link-alt"></i> Isi Kuesioner
                    </button>
                    
                    <button type="button" class="btn btn-outline-secondary mt-2" data-bs-toggle="modal" data-bs-target="#cbfEvaluationModal">
                        <i class="fas fa-eye"></i> Riwayat Penilaian
                    </button>
                </div>
            </div>
        </div>
    </div>
@elseif(!$hasRecommendations && $userHasReviewed)
    <!-- Jika tidak ada rekomendasi tapi user sudah pernah review -->
    <div class="card mt-4 border-info">
        <div class="card-body">
            <h6 class="fw-bold text-info">
                <i class="fas fa-trophy"></i> Hasil Optimal & Evaluasi Selesai
            </h6>
            <div class="alert alert-info mb-0">
                <p class="mb-2">
                    <i class="fas fa-star text-warning"></i> 
                    Selamat! Semua jawaban Anda optimal untuk tryout ini.
                </p>
                <p class="mb-3">
                    Anda juga telah berkontribusi dalam penelitian dengan memberikan evaluasi sistem rekomendasi.
                </p>
                
                <button type="button" class="btn btn-primary btn-sm" id="openUATForm">
                    <i class="fas fa-external-link-alt"></i> Isi Kuesioner UAT
                </button>
            </div>
        </div>
    </div>
@elseif(!$hasRecommendations && !$userHasReviewed)
    <!-- Jika tidak ada rekomendasi dan belum pernah review -->
    <div class="card mt-4 border-info">
        <div class="card-body">
            <h6 class="fw-bold text-info">
                <i class="fas fa-info-circle"></i> Tidak Ada Rekomendasi
            </h6>
            <div class="alert alert-info mb-0">
                <p class="mb-0">
                    <i class="fas fa-trophy text-warning"></i> 
                    Selamat! Tidak ada rekomendasi materi karena semua jawaban Anda sudah optimal.
                </p>
                <small class="text-muted">
                    Untuk berkontribusi dalam penelitian, silakan ikuti tryout lain atau tunggu rekomendasi dari tryout berikutnya.
                </small>
            </div>
        </div>
    </div>
@endif --}}




    </div>


{{-- SECTION: PRETEST-POSTTEST --}}
@if($hasilTryout->test_type === 'regular' || $hasilTryout->test_type === 'pretest')
    {{-- Jika ini tryout regular atau pretest, tampilkan tombol untuk ambil posttest --}}
    
    @if($hasilTryout->test_type === 'regular')
    <div class="card mt-4 border-info">
        <div class="card-header bg-info text-white">
            <h5><i class="fas fa-graduation-cap"></i> Ingin Mengukur Peningkatan Kemampuan?</h5>
        </div>
        <div class="card-body">
            <p>Anda bisa mengambil <strong>Pretest-Posttest</strong> untuk mengukur efektivitas pembelajaran!</p>
            
            <div class="alert alert-info">
                <strong>Cara Kerja Pretest-Posttest:</strong>
                <ol class="mb-0 mt-2">
                    <li>Jadikan tryout ini sebagai <strong>PRETEST</strong></li>
                    <li>Pelajari semua materi yang direkomendasikan</li>
                    <li>Kerjakan <strong>POSTTEST</strong> dengan soal berbeda</li>
                    <li>Lihat <strong>peningkatan skor</strong> Anda (Gain Score & N-Gain)</li>
                </ol>
            </div>
            
            <form action="{{ route('tryout.set-as-pretest', $skbSetSoal->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-info btn-lg">
                    <i class="fas fa-check-circle"></i> Jadikan Sebagai PRETEST
                </button>
            </form>
        </div>
    </div>
    @endif
    
    @if($hasilTryout->test_type === 'pretest')
    <div class="card mt-4 border-success">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-check-double"></i> Pretest Berhasil!</h5>
        </div>
        <div class="card-body">
            <p>Anda telah menyelesaikan <strong>PRETEST</strong> dengan skor: <strong>{{ $hasilTryout->twk_score + $hasilTryout->tiu_score + $hasilTryout->tkp_score }}</strong></p>
            
            <div class="alert alert-warning">
                <strong>📚 Langkah Selanjutnya:</strong>
                <ol class="mb-0 mt-2">
                    <li>Pelajari <strong>semua materi yang direkomendasikan</strong> di bawah</li>
                    <li>Luangkan waktu minimal <strong>3-7 hari</strong> untuk belajar</li>
                    <li>Klik tombol di bawah untuk ambil <strong>POSTTEST</strong></li>
                </ol>
            </div>
            
            <a href="{{ route('tryout.posttest', $skbSetSoal->id) }}" class="btn btn-success btn-lg">
                <i class="fas fa-play-circle"></i> Ambil POSTTEST Sekarang
            </a>
            
            <p class="text-muted mt-2 mb-0">
                <small>
                    <i class="fas fa-info-circle"></i> Posttest akan menggunakan soal berbeda dengan tingkat kesulitan yang setara
                </small>
            </p>
        </div>
    </div>
    @endif
@endif

@if($hasilTryout->test_type === 'posttest' && $hasilTryout->pretest_id)
    {{-- Tampilkan hasil perbandingan pretest vs posttest --}}
    @php
        $pretest = \App\Models\HasilTryout::find($hasilTryout->pretest_id);
        $pretestScore = $pretest->twk_score + $pretest->tiu_score + $pretest->tkp_score;
        $posttestScore = $hasilTryout->twk_score + $hasilTryout->tiu_score + $hasilTryout->tkp_score;
        $gainScore = $hasilTryout->gain_score;
        $nGain = $hasilTryout->normalized_gain;
        $nGainPct = $nGain * 100;
        
        if ($nGain < 0.3) {
            $category = 'Rendah';
            $categoryColor = 'danger';
        } elseif ($nGain <= 0.7) {
            $category = 'Sedang';
            $categoryColor = 'warning';
        } else {
            $category = 'Tinggi';
            $categoryColor = 'success';
        }
    @endphp
    
    <div class="card mt-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-chart-line"></i> Hasil Pretest vs Posttest</h5>
        </div>
        <div class="card-body">
            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h6 class="text-muted">Skor Pretest</h6>
                            <h2 class="text-secondary">{{ $pretestScore }}</h2>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($pretest->created_at)->format('d M Y') }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted">Skor Posttest</h6>
                            <h2 class="text-success">{{ $posttestScore }}</h2>
                            <small class="text-muted">Hari ini</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-{{ $gainScore >= 0 ? 'success' : 'danger' }}">
                        <div class="card-body">
                            <h6 class="text-muted">Peningkatan</h6>
                            <h2 class="text-{{ $gainScore >= 0 ? 'success' : 'danger' }}">
                                {{ $gainScore > 0 ? '+' : '' }}{{ $gainScore }}
                            </h2>
                            <small class="text-muted">poin</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="mb-3"><strong>📊 Normalized Gain (N-Gain)</strong></h6>
                    
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-{{ $categoryColor }}" 
                             role="progressbar" 
                             style="width: {{ min($nGainPct, 100) }}%"
                             aria-valuenow="{{ $nGainPct }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>{{ number_format($nGainPct, 2) }}%</strong>
                        </div>
                    </div>
                    
                    <p class="mb-2">
                        <strong>Kategori:</strong> 
                        <span class="badge badge-{{ $categoryColor }} badge-lg">{{ $category }}</span>
                    </p>
                    
                    <p class="mb-2 text-muted">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            N-Gain mengukur peningkatan relatif terhadap potensi maksimal Anda
                        </small>
                    </p>
                    
                    <div class="alert alert-{{ $categoryColor }} mb-0 mt-3">
                        @if($category === 'Tinggi')
                            <h6><i class="fas fa-trophy"></i> <strong>Selamat!</strong></h6>
                            <p class="mb-0">Peningkatan Anda sangat signifikan! Sistem rekomendasi sangat efektif membantu pembelajaran Anda.</p>
                        @elseif($category === 'Sedang')
                            <h6><i class="fas fa-thumbs-up"></i> <strong>Bagus!</strong></h6>
                            <p class="mb-0">Ada peningkatan yang baik. Terus pelajari materi yang direkomendasikan untuk hasil lebih optimal.</p>
                        @else
                            <h6><i class="fas fa-book-reader"></i> <strong>Tetap Semangat!</strong></h6>
                            <p class="mb-0">Pelajari materi lebih dalam dan coba latihan soal lebih banyak. Konsistensi adalah kunci!</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-3 text-center">
                <a href="{{ route('tryout.pretest-posttest-history') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history"></i> Lihat Riwayat Pretest-Posttest
                </a>
            </div>
        </div>
    </div>
@endif

</div>


@endsection

@push('after-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
   // Data dari Controller
const hasilTryout = @json($hasilTryout);
const tryoutInfo = @json($tryoutInfo);

// Prepare chart data berdasarkan jenis tryout
let chartLabels = [];
let chartData = [];
let chartColors = [];

if (tryoutInfo.is_tryout_resmi) {
    // Tryout Resmi: Tampilkan semua kategori
    chartLabels = ['TWK', 'TIU', 'TKP'];
    chartData = [hasilTryout.twk_score, hasilTryout.tiu_score, hasilTryout.tkp_score];
    chartColors = ['#6c757d', '#0d6efd', '#198754'];
} else {
    // Latihan: Tampilkan hanya kategori yang relevan
    const categoryColors = {
        'TWK': '#6c757d',
        'TIU': '#0d6efd', 
        'TKP': '#198754'
    };
    
    tryoutInfo.kategori_focus.forEach(kategori => {
        chartLabels.push(kategori);
        const scoreField = kategori.toLowerCase() + '_score';
        chartData.push(hasilTryout[scoreField] || 0);
        chartColors.push(categoryColors[kategori] || '#6c757d');
    });
}

// Chart.js untuk grafik utama
const ctx = document.getElementById('chartKategori');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Nilai',
            data: chartData,
            backgroundColor: chartColors
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            title: {
                display: true,
                text: tryoutInfo.is_tryout_resmi ? 'Grafik Hasil Tryout CPNS' : `Grafik Hasil Latihan ${tryoutInfo.kategori_focus.join(', ')}`
            }
        }
    }
});

// Data TWK untuk Highcharts (hanya jika ada TWK)
if (tryoutInfo.is_tryout_resmi || tryoutInfo.kategori_focus.includes('TWK')) {
    const twkData = [
        { name: 'Nasionalisme', y: hasilTryout.nasionalisme },
        { name: 'Integritas', y: hasilTryout.integritas },
        { name: 'Bela Negara', y: hasilTryout.bela_negara },
        { name: 'Pilar Negara', y: hasilTryout.pilar_negara },
        { name: 'Bahasa Indonesia', y: hasilTryout.bahasa_indonesia },
    ];
}

// Data TIU untuk Highcharts (hanya jika ada TIU)
if (tryoutInfo.is_tryout_resmi || tryoutInfo.kategori_focus.includes('TIU')) {
    const tiuData = [
        { name: 'Verbal (Analogi)', y: hasilTryout.verbal_analogi },
        { name: 'Verbal (Silogisme)', y: hasilTryout.verbal_silogisme },
        { name: 'Verbal (Analisis)', y: hasilTryout.verbal_analisis },
        { name: 'Numerik (Hitung Cepat)', y: hasilTryout.numerik_hitung_cepat },
        { name: 'Numerik (Deret Angka)', y: hasilTryout.numerik_deret_angka },
        { name: 'Numerik (Perbandingan Kuantitatif)', y: hasilTryout.numerik_perbandingan_kuantitatif },
        { name: 'Numerik (Soal Cerita)', y: hasilTryout.numerik_soal_cerita },
        { name: 'Figural (Analogi)', y: hasilTryout.figural_analogi },
        { name: 'Figural (Ketidaksamaan)', y: hasilTryout.figural_ketidaksamaan },
        { name: 'Figural (Serial)', y: hasilTryout.figural_serial },
    ];
}

// Data TKP untuk Highcharts (hanya jika ada TKP)
if (tryoutInfo.is_tryout_resmi || tryoutInfo.kategori_focus.includes('TKP')) {
    const tkpData = [
        { name: 'Pelayanan Publik', y: hasilTryout.pelayanan_publik },
        { name: 'Jejaring Kerja', y: hasilTryout.jejaring_kerja },
        { name: 'Sosial Budaya', y: hasilTryout.sosial_budaya },
        { name: 'Teknologi Informasi dan Komunikasi (TIK)', y: hasilTryout.teknologi_informasi_dan_komunikasi_tik },
        { name: 'Profesionalisme', y: hasilTryout.profesionalisme },
        { name: 'Anti Radikalisme', y: hasilTryout.anti_radikalisme },
    ];
}

</script>

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            text: '{{ session('success') }}',
            showConfirmButton: true,
            timer: 5000
        });
    @elseif (session('error'))
        Swal.fire({
            icon: 'error',
            text: '{{ session('error') }}',
            showConfirmButton: true,
            timer: 5000
        });
    @endif
</script>
<script>
// Variable untuk menyimpan URL Google Form UAT
const UAT_FORM_URL = '{{ config("app.uat_form_url", "https://forms.gle/9EUnpP3fDQrKyut79") }}';

// Handle UAT Form opening
function openUATForm() {
    Swal.fire({
        icon: 'info',
        title: 'Kuesioner User Acceptance Testing',
        html: `
            <p>Anda akan diarahkan ke kuesioner UAT untuk memberikan penilaian terhadap sistem tryout CPNS ini.</p>
            <p><strong>Kuesioner ini membutuhkan waktu sekitar 3-5 menit.</strong></p>
            <small class="text-muted">Kontribusi Anda sangat membantu penelitian ini.</small>
        `,
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-external-link-alt"></i> Buka Kuesioner',
        cancelButtonText: 'Nanti Saja',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Buka Google Form di tab baru
            window.open(UAT_FORM_URL, '_blank');
            
            // Show thank you message
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Terima Kasih!',
                    text: 'Kuesioner telah dibuka di tab baru. Silakan lengkapi kuesioner untuk membantu penelitian ini.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
            }, 500);
        }
    });
}

// Event listeners untuk tombol UAT
document.addEventListener('DOMContentLoaded', function() {
    // Tombol UAT di card
    const uatButton = document.getElementById('openUATForm');
    if (uatButton) {
        uatButton.addEventListener('click', openUATForm);
    }
    
    // Tombol UAT di modal
    const uatButtonModal = document.getElementById('openUATFormFromModal');
    if (uatButtonModal) {
        uatButtonModal.addEventListener('click', openUATForm);
    }
});


</script>
@endpush