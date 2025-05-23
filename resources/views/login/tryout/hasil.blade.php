@extends('layouts.public')

@section('title', 'Hasil Tryout SKD')

@push('after-style')
<style>
.box-shadow {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.scrollable {
    max-height: 200px;
    overflow-y: auto;
}
.badge-category {
    background-color: #0dcaf01d;
    color: #1eafcc;
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
    border-left: 4px solid #007bff;
    padding: 10px;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 5px;
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
    background: linear-gradient(45deg, #007bff, #0056b3);
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
        <div class="col-lg-8 p-0">
           <!-- Header Score Section -->
<div class="card mb-3 p-0" style="border: none">
    <div class="card-body row bg-score mx-0 text-white">
       <div class="pt-md-0 pt-4 col-12 col-md-6 align-self-center">
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
        <a href="{{ route('tryout.pembahasan', $skbSetSoal->id) }}" class="btn btn-primary mt-2">Lihat Pembahasan {{ $tryoutInfo['is_tryout_resmi'] ? 'Tryout' : 'Latihan' }}</a>
    </div>
      <div class="pt-md-0 pt-4 col-12 col-md-6 text-md-end text-center">
        <img src="{{asset('assets/img/people-score.png')}}" alt="Score Image">
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

        <!-- Kanan - Rekomendasi -->
        <div class="col-lg-4">
            <!-- Rekomendasi Materi -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <strong>Rekomendasi Materi Belajar</strong>
        <small class="text-muted d-block">
            Berdasarkan Content Based Filtering - 
            @if(!empty($recommendations['tryout_info']))
                {{ $recommendations['tryout_info']['is_tryout_resmi'] ? 'Tryout Resmi (Semua Kategori)' : 'Latihan (' . implode(', ', $recommendations['tryout_info']['kategori_focus']) . ')' }}
            @endif
        </small>
    </div>
    <div class="card-body">
        @php
            $availableCategories = !empty($recommendations['recommendations']) ? array_keys($recommendations['recommendations']) : [];
            $hasRecommendations = collect($recommendations['recommendations'] ?? [])->flatten(1)->isNotEmpty();
        @endphp
        
        @if($hasRecommendations)
            @if(count($availableCategories) > 1)
                <!-- Tab Navigation untuk Multiple Categories -->
                <ul class="nav nav-pills mb-2" id="pills-tab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="pill" href="#semua-materi">Semua</a>
                    </li>
                    @foreach($availableCategories as $kategori)
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#{{ strtolower($kategori) }}-materi">{{ $kategori }}</a>
                        </li>
                    @endforeach
                </ul>
                
                <div class="tab-content" id="pills-tabContent">
                    <!-- Semua Rekomendasi -->
                    <div class="tab-pane fade show active scrollable" id="semua-materi">
                        @foreach($availableCategories as $kategori)
                            @if(!empty($recommendations['recommendations'][$kategori]))
                                <h6 class="fw-bold text-{{ $kategori == 'TWK' ? 'primary' : ($kategori == 'TIU' ? 'success' : 'info') }} mt-3">{{ $kategori }}</h6>
                                @foreach(array_slice($recommendations['recommendations'][$kategori], 0, 3) as $item)
                                    <div class="recommendation-item">
                                        <h6 class="mb-1">
                                            <a href="{{ route('materi-belajar.show', $item['material']->id) }}" class="text-decoration-none">
                                                {{ $item['material']->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $item['material']->tipe }}</small>
                                        <div class="similarity-score">
                                            Relevansi: {{ number_format($item['similarity'] * 100, 1) }}%
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                    <!-- Individual Category Tabs -->
                    @foreach($availableCategories as $kategori)
                        <div class="tab-pane fade scrollable" id="{{ strtolower($kategori) }}-materi">
                            @if(!empty($recommendations['recommendations'][$kategori]))
                                @foreach($recommendations['recommendations'][$kategori] as $item)
                                    <div class="recommendation-item">
                                        <h6 class="mb-1">
                                            <a href="{{ route('materi-belajar.show', $item['material']->id) }}" class="text-decoration-none">
                                                {{ $item['material']->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $item['material']->tipe }}</small>
                                        <div class="similarity-score">
                                            Relevansi: {{ number_format($item['similarity'] * 100, 1) }}%
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">Tidak ada rekomendasi {{ $kategori }} atau semua jawaban {{ $kategori }} Anda sudah optimal!</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Single Category Display (untuk latihan yang fokus 1 kategori) -->
                @foreach($availableCategories as $kategori)
                    @if(!empty($recommendations['recommendations'][$kategori]))
                        <h6 class="fw-bold text-{{ $kategori == 'TWK' ? 'primary' : ($kategori == 'TIU' ? 'success' : 'info') }} mb-3">
                            Rekomendasi Materi {{ $kategori }}
                        </h6>
                        <div class="scrollable">
                            @foreach($recommendations['recommendations'][$kategori] as $item)
                                <div class="recommendation-item">
                                    <h6 class="mb-1">
                                        <a href="{{ route('materi-belajar.show', $item['material']->id) }}" class="text-decoration-none">
                                            {{ $item['material']->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $item['material']->tipe }}</small>
                                    <div class="similarity-score">
                                        Relevansi: {{ number_format($item['similarity'] * 100, 1) }}%
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @endif
        @else
            <p class="text-muted">Tidak ada rekomendasi tersedia. Semua jawaban Anda benar!</p>
        @endif
    </div>
</div>

<!-- Video Pembelajaran (Update untuk dynamic categories) -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <strong>Belajar dengan Mendengarkan (Video)</strong>
        <small class="text-muted d-block">Video pembelajaran terkait</small>
    </div>
    <div class="card-body scrollable">
        @if(!empty($videoRecommendations))
            @foreach($availableCategories as $kategori)
                @if(!empty($videoRecommendations[$kategori]))
                    <h6 class="fw-bold text-{{ $kategori == 'TWK' ? 'primary' : ($kategori == 'TIU' ? 'success' : 'info') }} mt-3">{{ $kategori }}</h6>
                    @foreach(array_slice($videoRecommendations[$kategori], 0, 2) as $video)
                        <div class="video-item">
                            <div class="d-flex">
                                <img src="{{ $video['thumbnail'] }}" alt="Thumbnail" class="video-thumbnail me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ $video['url'] }}" target="_blank" class="text-decoration-none">
                                            {{ Str::limit($video['title'], 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $video['channel'] }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @else
            <p class="text-muted">Video pembelajaran akan muncul berdasarkan rekomendasi materi.</p>
        @endif
    </div>
</div>
        </div>
    </div>

    <!-- Debug Info CBF (untuk development dan sidang) -->
@if(!empty($recommendations['debug_info']))
    <div class="card mt-4">
        <div class="card-header">
            <strong>Analisis Content Based Filtering - Proses Rekomendasi</strong>
            <small class="text-muted d-block">
                Informasi teknis untuk memahami cara kerja algoritma CBF
                @if(!empty($recommendations['tryout_info']))
                    - {{ $recommendations['tryout_info']['is_tryout_resmi'] ? 'Mode: Tryout Resmi' : 'Mode: Latihan' }}
                @endif
            </small>
        </div>
        <div class="card-body">
            <!-- Tryout Type Info -->
            @if(!empty($recommendations['tryout_info']))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-{{ $recommendations['tryout_info']['is_tryout_resmi'] ? 'primary' : 'warning' }}">
                            <h6><i class="fas fa-info-circle"></i> Jenis Tryout:</h6>
                            <p class="mb-2">
                                <strong>{{ $recommendations['tryout_info']['is_tryout_resmi'] ? 'Tryout Resmi' : 'Latihan' }}</strong>
                                @if(!$recommendations['tryout_info']['is_tryout_resmi'])
                                    - Fokus Kategori: {{ implode(', ', $recommendations['tryout_info']['kategori_focus']) }}
                                @endif
                            </p>
                            <small>
                                {{ $recommendations['tryout_info']['is_tryout_resmi'] 
                                    ? 'Rekomendasi mencakup semua kategori (TWK, TIU, TKP) untuk persiapan komprehensif.' 
                                    : 'Rekomendasi difokuskan pada kategori latihan yang dikerjakan untuk pembelajaran yang lebih terarah.' }}
                            </small>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Step Process -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="fw-bold mb-3">Langkah-langkah Proses CBF:</h6>
                    @if(!empty($recommendations['debug_info']['steps']))
                        @foreach($recommendations['debug_info']['steps'] as $step => $description)
                            <div class="step-indicator">
                                <strong>{{ str_replace('_', ' ', strtoupper($step)) }}:</strong> {{ $description }}
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">Statistik Proses:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-times-circle text-danger"></i> Total soal bermasalah: <strong>{{ $recommendations['debug_info']['total_wrong_answers'] ?? 0 }}</strong></li>
                        <li><i class="fas fa-exclamation-triangle text-warning"></i> TWK/TIU salah: <strong>{{ $recommendations['debug_info']['regular_wrong_answers'] ?? 0 }}</strong></li>
                        <li><i class="fas fa-star text-info"></i> TKP poin 1: <strong>{{ $recommendations['debug_info']['tkp_poin_1_count'] ?? 0 }}</strong></li>
                        <li><i class="fas fa-key text-primary"></i> Kata kunci dari soal: <strong>{{ count($recommendations['debug_info']['soal_keywords'] ?? []) }}</strong></li>
                        <li><i class="fas fa-book text-info"></i> Total kata kunci materi: <strong>{{ $recommendations['debug_info']['total_material_keywords'] ?? 0 }}</strong></li>
                        <li><i class="fas fa-layer-group text-success"></i> Kata kunci unik: <strong>{{ $recommendations['debug_info']['unique_keywords_count'] ?? 0 }}</strong></li>
                        <li><i class="fas fa-calculator text-warning"></i> Similarity dihitung: <strong>{{ $recommendations['debug_info']['total_similarities_calculated'] ?? 0 }}</strong></li>
                        @if(!empty($recommendations['debug_info']['filtered_categories']))
                            <li><i class="fas fa-filter text-primary"></i> Filter kategori: <strong>{{ implode(', ', $recommendations['debug_info']['filtered_categories']) }}</strong></li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Breakdown Soal Bermasalah:</h6>
                    <div class="d-flex flex-wrap mb-3">
                        @if(!empty($recommendations['debug_info']['regular_wrong_answers']) && $recommendations['debug_info']['regular_wrong_answers'] > 0)
                            <span class="badge bg-danger me-2 mb-2">
                                <i class="fas fa-times"></i> {{ $recommendations['debug_info']['regular_wrong_answers'] }} Jawaban Salah
                            </span>
                        @endif
                        @if(!empty($recommendations['debug_info']['tkp_poin_1_count']) && $recommendations['debug_info']['tkp_poin_1_count'] > 0)
                            <span class="badge bg-warning text-dark me-2 mb-2">
                                <i class="fas fa-star"></i> {{ $recommendations['debug_info']['tkp_poin_1_count'] }} TKP Poin 1
                            </span>
                        @endif
                    </div>
                    
                    <h6 class="fw-bold">Kata Kunci dari Soal Bermasalah:</h6>
                    <div class="d-flex flex-wrap">
                        @if(!empty($recommendations['debug_info']['soal_keywords']))
                            @foreach(array_slice($recommendations['debug_info']['soal_keywords'], 0, 15) as $keyword)
                                <span class="badge bg-danger me-1 mb-1">{{ $keyword }}</span>
                            @endforeach
                            @if(count($recommendations['debug_info']['soal_keywords']) > 15)
                                <span class="badge bg-secondary me-1 mb-1">+{{ count($recommendations['debug_info']['soal_keywords']) - 15 }} lainnya</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Penjelasan Metode -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Metodologi CBF:</h6>
                        <p class="mb-2">
                            <strong>Untuk Tryout Resmi:</strong> Sistem memberikan rekomendasi dari semua kategori (TWK, TIU, TKP) berdasarkan analisis soal yang dijawab salah untuk persiapan komprehensif.
                        </p>
                        <p class="mb-2">
                            <strong>Untuk Latihan:</strong> Sistem fokus memberikan rekomendasi materi hanya pada kategori yang sedang dipelajari untuk pembelajaran yang lebih terarah dan efektif.
                        </p>
                        <p class="mb-0">
                            <strong>Penanganan TKP:</strong> Jawaban TKP dengan poin 1 dianggap sebagai "jawaban bermasalah" karena menunjukkan pemahaman yang sangat rendah tentang nilai-nilai ASN.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabel Bobot Vektor (Sample) -->
            @if(!empty($recommendations['debug_info']['soal_vector']) && !empty($recommendations['debug_info']['unique_keywords']))
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold">Sample Tabel Bobot Vektor Biner:</h6>
                        <small class="text-muted">Contoh representasi vektor untuk 10 kata kunci pertama (1 = ada, 0 = tidak ada)</small>
                        <div class="table-responsive mt-2">
                            <table class="table table-sm cbf-table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kata Kunci</th>
                                        <th>Soal Vector</th>
                                        @if(!empty($recommendations['recommendations']))
                                            @foreach(array_slice(collect($recommendations['recommendations'])->flatten(1)->take(3)->toArray(), 0, 3) as $index => $item)
                                                <th>{{ Str::limit($item['material']->title, 15) }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($recommendations['debug_info']['unique_keywords'], 0, 10) as $index => $keyword)
                                        <tr>
                                            <td class="text-start"><strong>{{ $keyword }}</strong></td>
                                            <td class="vector-{{ $recommendations['debug_info']['soal_vector'][$index] ?? 0 }}">
                                                {{ $recommendations['debug_info']['soal_vector'][$index] ?? 0 }}
                                            </td>
                                            @if(!empty($recommendations['recommendations']))
                                                @foreach(array_slice(collect($recommendations['recommendations'])->flatten(1)->take(3)->toArray(), 0, 3) as $item)
                                                    @php
                                                        $vectorValue = $item['vector'][$index] ?? 0;
                                                    @endphp
                                                    <td class="vector-{{ $vectorValue }}">{{ $vectorValue }}</td>
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                                    @if(count($recommendations['debug_info']['unique_keywords']) > 10)
                                        <tr>
                                            <td colspan="100%" class="text-center text-muted">
                                                ... dan {{ count($recommendations['debug_info']['unique_keywords']) - 10 }} kata kunci lainnya
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Cosine Similarity dihitung berdasarkan dot product dan magnitude dari vektor-vektor ini.
                            Semakin banyak kata kunci yang sama (nilai 1), semakin tinggi similarity score.
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

    <!-- Penilaian -->
    @if(Auth::user()->is_review == 1)
    <div class="card mt-4">
        <div class="card-body">
            <h6 class="fw-bold">Penilaian Efektivitas Sistem Tryout CPNS</h6>
            <p class="mb-2">Terima kasih telah mengikuti tryout CPNS. Untuk meningkatkan kualitas sistem rekomendasi tryout, Anda dapat memberikan penilaian.</p>
            
            <form action="{{url('/send-feedback')}}" method="POST">
                @csrf
                <input type="hidden" name="name" value="{{Auth::user()->name}}">
                <input type="hidden" name="email" value="{{Auth::user()->email}}">
                <div class="form-floating mb-3">
                    <textarea class="form-control" required placeholder="Kirim penilaian berupa kritik atau saran dan pengalaman anda menggunakan sistem CPNS" id="message" name="message" style="height: 120px"></textarea>
                    <label for="message">Penilaian (Kritik/Saran)</label>
                </div>
                <button type="submit" class="btn btn-primary" onclick="this.disabled=true;this.form.submit();">Berikan Penilaian</button>
            </form>
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
@endpush