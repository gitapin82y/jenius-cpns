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
        <div class="col-lg-8 p-0">
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
        <a href="{{ route('tryout.pembahasan', $skbSetSoal->id) }}" class="btn bg-white text-primary mt-2">Lihat Pembahasan {{ $tryoutInfo['is_tryout_resmi'] ? 'Tryout' : 'Latihan' }}</a>
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
                                <h6 class="fw-bold text-secondary mt-3">{{ $kategori }}</h6>
                                @foreach($recommendations['recommendations'][$kategori] as $item)
                                    <div class="recommendation-item">
                                        <h6 class="mb-1">
                                            <a href="{{ route('materi-belajar.show', $item['material']->id) }}" class="text-decoration-none">
                                                {{ $item['material']->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $item['material']->tipe }}</small>
                                        {{-- <div class="similarity-score">
                                            Relevansi: {{ number_format($item['similarity'] * 100, 1) }}%
                                        </div> --}}
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
                                        {{-- <div class="similarity-score">
                                            Relevansi: {{ number_format($item['similarity'] * 100, 1) }}%
                                        </div> --}}
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
                                    {{-- <div class="similarity-score">
                                        Relevansi: {{ number_format($item['similarity'] * 100, 1) }}%
                                    </div> --}}
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
                            <div class="d-flex align-items-center">
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

    <div class="row">
        <!-- Card Evaluasi Sistem Rekomendasi Materi -->
@php
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
@endif
<!-- Modal CBF Evaluation - Complete Version -->
<div class="modal fade" id="cbfEvaluationModal" tabindex="-1" aria-labelledby="cbfEvaluationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cbfEvaluationModalLabel">
                    <i class="fas fa-star"></i> 
                    @if($userHasReviewed)
                        Riwayat Penilaian Rekomendasi Anda
                    @else
                        Evaluasi Rekomendasi Materi
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($userHasReviewed)
                    <!-- Show all user evaluations history -->
                    <div class="alert alert-success">
                        <strong>Status:</strong> Anda telah berkontribusi dalam penelitian ini dengan memberikan evaluasi sistem rekomendasi.
                    </div>
                    
                    @php
                        // Ambil semua evaluasi user dari semua tryout
                        $allUserEvaluations = \App\Models\CBFEvaluation::where('user_id', Auth::id())
                            ->where('evaluation_source', 'user')
                            ->where('is_recommended', true)
                            ->whereNotNull('user_feedback')
                            ->with(['material', 'setSoal'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                        
                        $evaluationsByTryout = $allUserEvaluations->groupBy('set_soal_id');
                    @endphp
                    
                    @forelse($evaluationsByTryout as $setSoalId => $evaluations)
                        @php
                            $tryoutTitle = $evaluations->first()->setSoal->title ?? 'Tryout #' . $setSoalId;
                            $evaluationDate = $evaluations->first()->created_at;
                            $groupedByCategory = $evaluations->groupBy('material.kategori');
                        @endphp
                        
                        <div class="mb-4 border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-primary mb-0">
                                    <i class="fas fa-file-alt"></i> {{ $tryoutTitle }}
                                </h6>
                                <small class="text-muted">{{ $evaluationDate->format('d M Y, H:i') }}</small>
                            </div>
                            
                            @foreach($groupedByCategory as $kategori => $categoryEvaluations)
                                <div class="mb-3">
                                    <h6 class="fw-bold text-{{ $kategori == 'TWK' ? 'primary' : ($kategori == 'TIU' ? 'success' : 'info') }}">
                                        Kategori {{ $kategori }}
                                    </h6>
                                    
                                    <div class="row">
                                        @foreach($categoryEvaluations as $evaluation)
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                    <div>
                                                        <strong>{{ Str::limit($evaluation->material->title, 25) }}</strong>
                                                        <br><small class="text-muted">{{ $evaluation->material->tipe }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <div>
                                                            @if($evaluation->user_feedback === true)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-thumbs-up"></i> Relevan
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-thumbs-down"></i> Tidak Relevan
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($evaluations->first()->user_comment)
                                <div class="mt-2">
                                    <strong>Komentar:</strong>
                                    <div class="bg-light p-2 rounded">
                                        <small>{{ $evaluations->first()->user_comment }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted"></i>
                            <p class="text-muted mt-2">Belum ada riwayat evaluasi</p>
                        </div>
                    @endforelse
                    
                @else
                    <!-- Form untuk evaluasi baru -->
                    <div class="alert alert-info">
                        <strong>Petunjuk:</strong> Berikan centang pada kolom "Relevan" jika materi tersebut sesuai dan membantu Anda memahami soal yang salah.
                        <br><strong>Penting:</strong> Anda hanya dapat memberikan penilaian satu kali untuk penelitian ini.
                    </div>
                    
                    <!-- Form Evaluation -->
                    <form id="cbfEvaluationForm">
                        @csrf
                        <input type="hidden" name="set_soal_id" value="{{ $skbSetSoal->id }}">
                        
                        @php
                            $hasRecommendations = collect($recommendations['recommendations'] ?? [])->flatten(1)->isNotEmpty();
                            $availableCategories = !empty($recommendations['recommendations']) ? array_keys($recommendations['recommendations']) : [];
                        @endphp
                        
                        @if($hasRecommendations)
                            @foreach($availableCategories as $kategori)
                                @if(!empty($recommendations['recommendations'][$kategori]))
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-{{ $kategori == 'TWK' ? 'primary' : ($kategori == 'TIU' ? 'success' : 'info') }}">
                                            Kategori {{ $kategori }}
                                        </h6>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Materi</th>
                                                        <th>Tipe</th>
                                                        {{-- <th>Relevansi</th> --}}
                                                        <th class="text-center">Penilaian</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recommendations['recommendations'][$kategori] as $index => $item)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $item['material']->title }}</strong>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">{{ $item['material']->tipe }}</small>
                                                            </td>
                                                            {{-- <td>
                                                                <small class="text-info">{{ number_format($item['similarity'] * 100, 1) }}%</small>
                                                            </td> --}}
                                                            <td class="text-center">
                                                                <div class="btn-group" role="group" aria-label="Penilaian {{ $item['material']->title }}">
                                                                    <input 
                                                                        type="radio" 
                                                                        class="btn-check" 
                                                                        name="material_{{ $item['material']->id }}" 
                                                                        id="relevan_{{ $item['material']->id }}"
                                                                        value="1"
                                                                        data-material-id="{{ $item['material']->id }}"
                                                                        data-similarity="{{ $item['similarity'] }}"
                                                                    >
                                                                    <label class="btn btn-outline-success btn-sm" for="relevan_{{ $item['material']->id }}">
                                                                        <i class="fas fa-thumbs-up"></i> Relevan
                                                                    </label>
                                                                    
                                                                    <input 
                                                                        type="radio" 
                                                                        class="btn-check" 
                                                                        name="material_{{ $item['material']->id }}" 
                                                                        id="tidak_relevan_{{ $item['material']->id }}"
                                                                        value="0"
                                                                        data-material-id="{{ $item['material']->id }}"
                                                                        data-similarity="{{ $item['similarity'] }}"
                                                                    >
                                                                    <label class="btn btn-outline-danger btn-sm" for="tidak_relevan_{{ $item['material']->id }}">
                                                                        <i class="fas fa-thumbs-down"></i> Tidak Relevan
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            
                            <!-- Komentar Tambahan -->
                            <div class="mb-3">
                                <label for="user_comment" class="form-label">
                                    <strong>Kritik atau Saran (Opsional)</strong>
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="user_comment" 
                                    name="user_comment" 
                                    rows="3" 
                                    placeholder="Berikan komentar berupa kritik, saran atau laporan jika ada bug pada sistem..."
                                ></textarea>
                                <small class="text-muted">Komentar Anda akan membantu penelitian untuk meningkatkan kualitas sistem.</small>
                            </div>
                            
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Tidak ada rekomendasi untuk dievaluasi karena semua jawaban Anda sudah optimal.
                            </div>
                        @endif
                    </form>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ $userHasReviewed ? 'Tutup' : 'Batal' }}
                </button>
                @if(!$userHasReviewed && $hasRecommendations)
                    <button type="button" class="btn btn-primary" id="submitEvaluation">
                        <i class="fas fa-paper-plane"></i> Kirim Penilaian (Hanya 1x)
                    </button>
                @elseif($userHasReviewed)
                    <button type="button" class="btn btn-primary" id="openUATFormFromModal">
                        <i class="fas fa-external-link-alt"></i> Isi Kuesioner UAT
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
    </div>



</div>
@if(isset($gainData) && $gainData)
<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h5><i class="fas fa-chart-line"></i> Perbandingan Pretest vs Posttest</h5>
    </div>
    <div class="card-body">
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="text-muted">Pretest</h6>
                        <h2 class="text-primary">{{ $gainData['pretest_score'] }}</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body">
                        <h6 class="text-muted">Posttest</h6>
                        <h2 class="text-success">{{ $gainData['posttest_score'] }}</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card {{ $gainData['is_improved'] ? 'border-success' : 'border-danger' }}">
                    <div class="card-body">
                        <h6 class="text-muted">Peningkatan</h6>
                        <h2 class="{{ $gainData['is_improved'] ? 'text-success' : 'text-danger' }}">
                            {{ $gainData['gain_score'] > 0 ? '+' : '' }}{{ $gainData['gain_score'] }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="mb-3"><strong>Normalized Gain (N-Gain)</strong></h6>
                
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar 
                        @if($gainData['category'] === 'Tinggi') bg-success
                        @elseif($gainData['category'] === 'Sedang') bg-warning
                        @else bg-danger
                        @endif"
                        style="width: {{ $gainData['normalized_gain_pct'] }}%">
                        <strong>{{ number_format($gainData['normalized_gain_pct'], 2) }}%</strong>
                    </div>
                </div>
                
                <p class="mb-2">
                    <strong>Kategori:</strong> 
                    <span class="badge 
                        @if($gainData['category'] === 'Tinggi') badge-success
                        @elseif($gainData['category'] === 'Sedang') badge-warning
                        @else badge-danger
                        @endif">
                        {{ $gainData['category'] }}
                    </span>
                </p>
                
                <div class="alert 
                    @if($gainData['category'] === 'Tinggi') alert-success
                    @elseif($gainData['category'] === 'Sedang') alert-warning
                    @else alert-info
                    @endif mb-0">
                    @if($gainData['category'] === 'Tinggi')
                        🎉 <strong>Selamat!</strong> Peningkatan sangat signifikan!
                    @elseif($gainData['category'] === 'Sedang')
                        👍 <strong>Bagus!</strong> Ada peningkatan yang baik.
                    @else
                        📚 <strong>Tetap semangat!</strong> Pelajari materi lebih dalam.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if(config('app.debug'))
<div class="alert alert-info mt-3">
    <h6><i class="fas fa-info-circle"></i> Debug Info</h6>
    <ul class="mb-0">
        <li>Soal yang dijawab salah: <strong>{{ $hasilTryout->total_salah }}</strong></li>
        <li>Rekomendasi yang dihasilkan: <strong>{{ isset($recommendationsPerSoal) ? count($recommendationsPerSoal) : 0 }}</strong></li>
        <li>Status: 
            @if($hasilTryout->total_salah == count($recommendationsPerSoal ?? []))
                <span class="badge bg-success">✓ One-to-One BENAR</span>
            @else
                <span class="badge bg-warning">⚠ Jumlah tidak sesuai</span>
            @endif
        </li>
    </ul>
</div>
@endif

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

// Handle submit evaluation - hanya jika belum evaluated
@if(!$userHasReviewed)
document.getElementById('submitEvaluation').addEventListener('click', function() {
    const form = document.getElementById('cbfEvaluationForm');
    const formData = new FormData(form);
    
    // Collect all material evaluations
    const evaluations = [];
    const radioButtons = form.querySelectorAll('input[type="radio"]:checked');
    
    // Validasi: pastikan semua materi sudah dievaluasi
    const allMaterials = form.querySelectorAll('input[type="radio"][data-material-id]');
    const uniqueMaterialIds = [...new Set(Array.from(allMaterials).map(input => input.dataset.materialId))];
    const evaluatedMaterialIds = [...new Set(Array.from(radioButtons).map(input => input.dataset.materialId))];
    
    if (evaluatedMaterialIds.length < uniqueMaterialIds.length) {
        Swal.fire({
            icon: 'warning',
            title: 'Evaluasi Belum Lengkap',
            text: 'Mohon berikan penilaian untuk semua materi yang direkomendasikan.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    // Collect evaluations
    radioButtons.forEach(radio => {
        evaluations.push({
            material_id: radio.dataset.materialId,
            similarity_score: parseFloat(radio.dataset.similarity),
            user_feedback: radio.value === '1',
            is_recommended: true
        });
    });
    
    // Prepare data
    const data = {
        set_soal_id: formData.get('set_soal_id'),
        user_comment: formData.get('user_comment'),
        evaluations: evaluations,
        _token: formData.get('_token')
    };
    
    // Disable submit button
    const submitBtn = document.getElementById('submitEvaluation');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    
    // Send AJAX request
    fetch('{{ route("user.cbf.evaluation.submit") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            // Handle HTTP errors
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Success response dengan prompt UAT
            Swal.fire({
                icon: 'success',
                title: 'Penilaian Berhasil Disimpan!',
                html: `
                    <p>${data.message}</p>
                    <hr>
                    <p><strong>Langkah selanjutnya:</strong> Isi kuesioner User Acceptance Testing untuk melengkapi kontribusi Anda dalam penelitian ini.</p>
                `,
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#28a745',
                confirmButtonText: '<i class="fas fa-external-link-alt"></i> Isi Kuesioner UAT',
                cancelButtonText: '<i class="fas fa-check"></i> Nanti Saja',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buka UAT form
                    window.open(UAT_FORM_URL, '_blank');
                    
                    // Show additional thank you message
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terima Kasih!',
                            text: 'Kuesioner UAT telah dibuka di tab baru. Silakan lengkapi untuk membantu penelitian ini.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        });
                    }, 500);
                }
                
                // Close modal dan refresh halaman
                const modal = bootstrap.Modal.getInstance(document.getElementById('cbfEvaluationModal'));
                modal.hide();
                location.reload();
            });
        } else {
            // Handle error responses
            if (data.error_code === 'ALREADY_REVIEWED') {
                Swal.fire({
                    icon: 'info',
                    title: 'Penilaian Sudah Diberikan',
                    text: data.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#007bff'
                }).then(() => {
                    location.reload(); // Refresh to update UI
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan tidak dikenal');
            }
        }
    })
    .catch(error => {
        console.error('CBF Evaluation Error:', error);
        
        let errorMessage = 'Terjadi kesalahan saat menyimpan penilaian.';
        let errorTitle = 'Gagal Menyimpan';
        
        // Handle specific error messages
        if (error.message) {
            if (error.message.includes('sudah pernah memberikan penilaian') || error.message.includes('ALREADY_REVIEWED')) {
                errorTitle = 'Penilaian Sudah Ada';
                errorMessage = 'Anda sudah pernah memberikan penilaian sebelumnya. Setiap pengguna hanya dapat memberikan penilaian satu kali untuk penelitian ini.';
                
                Swal.fire({
                    icon: 'info',
                    title: errorTitle,
                    text: errorMessage,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#007bff'
                }).then(() => {
                    location.reload();
                });
                return;
            } else if (error.message.includes('validation')) {
                errorTitle = 'Data Tidak Valid';
                errorMessage = 'Data yang dikirim tidak valid. Mohon periksa kembali penilaian Anda.';
            } else if (error.message.includes('network') || error.message.includes('fetch')) {
                errorTitle = 'Masalah Koneksi';
                errorMessage = 'Gagal terhubung ke server. Periksa koneksi internet Anda dan coba lagi.';
            } else {
                errorMessage = error.message;
            }
        }
        
        // Show error alert
        Swal.fire({
            icon: 'error',
            title: errorTitle,
            text: errorMessage,
            confirmButtonText: 'Coba Lagi',
            confirmButtonColor: '#dc3545',
            footer: '<small class="text-muted">Jika masalah berlanjut, hubungi administrator</small>'
        });
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});
@endif

// Handle modal close dengan konfirmasi jika ada perubahan (hanya untuk mode input)
@if(!$userHasReviewed)
document.getElementById('cbfEvaluationModal').addEventListener('hide.bs.modal', function (event) {
    const form = document.getElementById('cbfEvaluationForm');
    const radioButtons = form.querySelectorAll('input[type="radio"]:checked');
    const commentField = form.querySelector('#user_comment');
    
    // Cek apakah ada input yang sudah diisi
    if (radioButtons.length > 0 || (commentField && commentField.value.trim() !== '')) {
        // Prevent modal from closing
        event.preventDefault();
        
        Swal.fire({
            icon: 'warning',
            title: 'Tutup Penilaian?',
            text: 'Anda memiliki penilaian yang belum disimpan. Yakin ingin menutup?',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tutup',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Force close modal
                const modal = bootstrap.Modal.getInstance(event.target);
                modal.hide();
                
                // Reset form
                form.reset();
            }
        });
    }
});
@endif

</script>
@endpush