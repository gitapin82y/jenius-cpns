@extends('layouts.public')

@section('title', 'Pembahasan')

@push('after-style')
 <style>
/* User Evaluation Section */
.user-evaluation-section {
    background: rgba(255, 255, 255, 0.1);
    padding: 15px;
    border-radius: 10px;
    margin-top: 15px;
}

.btn-evaluation {
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.btn-evaluation:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.evaluation-status {
    text-align: center;
}

.d-flex.gap-2 {
    gap: 10px;
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
    background-color: #ffffff;
    color: #1ecc66;
    padding: 10px 20px;
    margin-left: 10px;
    border-radius: 8px;
}

/* ✅ BARU: Styling untuk rekomendasi materi */
.recommendation-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    margin-top: 20px;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}
.recommendation-box span,.recommendation-box strong{
   color: white;
}

.recommendation-box h2{
    font-size: 20px;
 color: white;
}

.recommendation-box h6 {
    color: white;
    font-weight: 700;
    margin-bottom: 15px;
}

.recommendation-box .material-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 10px;
}

.similarity-badge {
    font-size: 1rem;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
}

.keyword-badge {
    display: inline-block;
    padding: 5px 12px;
    margin: 3px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
}

.keyword-soal {
    background-color: #3498db;
    color: white;
}

.keyword-material {
    background-color: #2ecc71;
    color: white;
}

.keyword-match {
    background-color: #f39c12;
    color: white;
    font-weight: 700;
    border: 2px solid #fff;
}

.status-badge {
    font-size: 1.1rem;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 700;
}

.btn-read-material {
    background-color: white;
    color: #667eea;
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 25px;
    border: none;
    transition: all 0.3s ease;
}

.btn-read-material:hover {
    background-color: #f8f9fa;
    color: #764ba2;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

 @media (min-width: 767.98px) {
    #navSoal .nav-soal{
        width: 70px;
    }
    #navSoal {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #ddd;
        box-sizing: border-box;
    }
}

@media (max-width: 768px) {
    .recommendation-box {
        padding: 15px;
    }
    
    .recommendation-box .material-title {
        font-size: 1.1rem;
    }
    
    .keyword-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
    }
}
 </style>
@endpush

@section('content')
<div class="container px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 mb-5 mt-m-5">
            @foreach($soals as $key => $soal)
            @php
                // Ambil jawaban user untuk soal ini
                $jawabanUser = $jawabanUsers->get($soal->id);
                $isWrong = $jawabanUser && $jawabanUser->status === 'salah';
                
                // Ambil rekomendasi dan evaluasi (jika ada)
                $recommendation = isset($recommendations) ? $recommendations->get($soal->id) : null;
                $evaluation = isset($evaluations) ? $evaluations->get($soal->id) : null;
                
                // Extract keywords soal
                $soalKeywords = $soal->kata_kunci ? json_decode($soal->kata_kunci, true) : [];
            @endphp

            <div class="soal-item mb-4" id="soal-{{ $key }}" style="display: {{ $key === 0 ? 'block' : 'none' }};">
                <!-- Kategori Soal -->
                <div class="col-md-6 col-12 text-start">
                    <p class="badge-category d-inline-block">
                        <strong>{{ strtoupper($soal->kategori) }} </strong> - {{ $soal->tipe }}
                    </p>
                </div>
                
                @if($soal->foto)
                    <img src="{{ asset('storage/' . $soal->foto) }}" alt="Question Image" width="50%" class="img-fluid mb-3">
                @endif
        
                <!-- Pertanyaan -->
                <p>{{ $key+1 . '.) ' .$soal->pertanyaan }}</p>
        
                <!-- Opsi Jawaban -->
                @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                    <div class="form-check">
                        @if($soal->{'foto_jawaban_' . $option})
                            <img src="{{ asset('storage/' . $soal->{'foto_jawaban_' . $option}) }}" width="50%" alt="Answer {{ strtoupper($option) }} Image" class="img-fluid mb-2">
                            <br>
                        @endif
        
                        @php
                            // Hitung poin berdasarkan kategori soal
                            if ($soal->kategori == 'TKP') {
                                $poin = $soal->{'score_' . $option} ?? 0;
                                $isCorrect = true;
                            } else {
                                $isCorrect = strtoupper($option) === $soal->jawaban_benar;
                                $poin = $isCorrect ? $soal->poin : 0;
                            }
                            
                            $isChecked = $jawabanUser && strtoupper($option) === $jawabanUser->jawaban_user ? 'checked' : '';
                        @endphp
        
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="jawaban[{{ $soal->id }}]" 
                            value="{{ strtoupper($option) }}" 
                            {{ $isChecked }} 
                            disabled
                        >
        
                        <label class="form-check-label d-flex align-items-center" for="jawaban-{{ $soal->id }}-{{ $option }}">
                            <span class="@if($isChecked && !$isCorrect) text-danger fw-bold @endif 
                                          @if($isChecked && $isCorrect) text-success fw-bold @endif">
                                {{ strtoupper($option) }}. {{ $soal->{'jawaban_' . $option} }}
                                @if($isCorrect)
                                    ({{ $poin }} poin) <i class="fas fa-check-circle text-success ms-1"></i>
                                @elseif($isChecked && !$isCorrect)
                                    <i class="fas fa-times-circle text-danger ms-1"></i>
                                @endif
                            </span>
                        </label>
                    </div>
                @endforeach
        
                <!-- Pembahasan -->
                <p class="pt-3 fw-bold"><i class="fa fa-solid fa-question text-primary"></i> Pembahasan</p>
                <p class="pt-1">{!! nl2br(e($soal->pembahasan)) !!}</p>

                <!-- ✅ BARU: Kata Kunci Soal -->
                @if(!empty($soalKeywords))
                <div class="mt-3">
                    <p class="fw-bold mb-2">
                        <i class="fas fa-tags text-primary"></i> Kata Kunci Soal:
                    </p>
                    <div>
                        @foreach($soalKeywords as $keyword)
                            <span class="keyword-badge keyword-soal">{{ $keyword }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

             <!-- ✅ REKOMENDASI MATERI (Hanya untuk soal yang salah) -->
@if($isWrong && $recommendation && $evaluation)
    @php
        $material = $recommendation->material;
        $materialKeywords = $material->kata_kunci ? json_decode($material->kata_kunci, true) : [];
        $intersectionKeywords = $evaluation->intersection_keywords ?? [];
        $intersectionCount = $evaluation->intersection_count ?? 0;
        $similarityScore = $recommendation->similarity_score ?? 0;
        $isRelevant = $evaluation->is_relevant ?? false;
        $classification = $evaluation->classification ?? 'FP';
        
        // ✅ BARU: User feedback
        $hasUserFeedback = $evaluation->user_feedback !== null;
        $userFeedback = $evaluation->user_feedback;
        $finalClassification = $evaluation->final_classification ?? $classification;
    @endphp

    <div class="recommendation-box" id="recommendation-{{ $soal->id }}">
        <h6>
            <i class="fas fa-book-reader"></i> 
            REKOMENDASI MATERI UNTUK SOAL INI
        </h6>

             <!-- Kata Kunci Materi -->
        @if(!empty($materialKeywords))
        <div class="mb-3">
            <p class="fw-bold mb-2">
                <i class="fas fa-key"></i> Kata Kunci Materi:
            </p>
            <div>
                @foreach($materialKeywords as $keyword)
                    <span class="keyword-badge keyword-material">{{ $keyword }}</span>
                @endforeach
            </div>
        </div>
        @endif

               <!-- Kata Kunci yang Cocok -->
        <div class="mb-3">
            <p class="fw-bold mb-2">
                <i class="fas fa-check-double"></i> 
                Kata Kunci yang Cocok ({{ $intersectionCount }} kata):
            </p>
            <div>
                @if($intersectionCount > 0)
                    @foreach($intersectionKeywords as $keyword)
                        <span class="keyword-badge keyword-match">
                            <i class="fas fa-star"></i> {{ $keyword }}
                        </span>
                    @endforeach
                @else
                    <span class="fst-italic" style="opacity: 0.8;">
                        (Tidak ada kata kunci yang cocok)
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Judul Materi -->
        <div class="material-title">
            <i class="fas fa-file-alt"></i> {{ $material->title }}
        </div>
        <div class="my-4">
            {!! $material->content !!}
        </div>
        <!-- Cosine Similarity -->
        <div class="mb-3">
            <strong>Cosine Similarity:</strong>
            <span class="similarity-badge 
                {{ $similarityScore >= 0.7 ? 'bg-success' : ($similarityScore >= 0.6 ? 'bg-warning text-dark' : 'bg-danger') }}">
                {{ number_format($similarityScore, 4) }} ({{ number_format($similarityScore * 100, 2) }}%)
            </span>
            
            <div class="mt-2">
                <small class="text-white-50">
                    <i class="fas fa-info-circle"></i> 
                    Threshold minimum: 0.60 (60%)
                    @if($similarityScore >= 0.6)
                        <span class="badge badge-success badge-sm ml-1">
                            <i class="fas fa-check"></i> Memenuhi Threshold
                        </span>
                    @else
                        <span class="badge badge-danger badge-sm ml-1">
                            <i class="fas fa-times"></i> Di Bawah Threshold
                        </span>
                    @endif
                </small>
            </div>
        </div>


 

        <!-- Status Evaluasi Otomatis -->
        <div class="mb-3">
            <strong>Status Evaluasi Otomatis:</strong>
            @if($classification === 'TP')
                <span class="status-badge bg-success text-white">
                    <i class="fas fa-check-circle"></i> RELEVAN (TP)
                </span>
            @else
                <span class="status-badge bg-danger text-white">
                    <i class="fas fa-times-circle"></i> TIDAK RELEVAN (FP)
                </span>
            @endif
            <div class="mt-1">
                <small class="text-white-50">
                    Berdasarkan threshold similarity >= 0.6
                </small>
            </div>
        </div>

        <!-- ✅ BARU: User Manual Evaluation -->
        <div class="mb-3 user-evaluation-section">
            <hr style="border-color: rgba(255,255,255,0.2);">
            
            @if(!$hasUserFeedback)
                <!-- Belum dinilai: Tampilkan form -->
                <div class="evaluation-form" id="eval-form-{{ $evaluation->id }}">
                    <p class="fw-bold mb-2">
                        <i class="fas fa-star"></i> Penilaian Anda:
                    </p>
                    <p class="text-white-50 small mb-3">
                        Apakah materi ini membantu Anda memahami soal?
                    </p>
                    
                    <div class="d-flex gap-2">
                        <button type="button" 
                                class="btn btn-success btn-evaluation flex-fill"
                                onclick="submitEvaluation({{ $evaluation->id }}, true)">
                            <i class="fas fa-thumbs-up"></i> Relevan
                        </button>
                        <button type="button" 
                                class="btn btn-danger btn-evaluation flex-fill"
                                onclick="submitEvaluation({{ $evaluation->id }}, false)">
                            <i class="fas fa-thumbs-down"></i> Tidak Relevan
                        </button>
                    </div>
                </div>
            @else
                <!-- Sudah dinilai: Tampilkan status -->
                <div class="evaluation-status">
                    <p class="fw-bold mb-2">
                        <i class="fas fa-check-circle"></i> Penilaian Anda:
                    </p>
                    
                    @if($userFeedback)
                        <span class="status-badge bg-success text-white">
                            <i class="fas fa-thumbs-up"></i> RELEVAN
                        </span>
                        <p class="text-white-50 small mt-2 mb-0">
                            <i class="fas fa-info-circle"></i> 
                            Anda menilai materi ini relevan pada {{ $evaluation->user_evaluated_at->format('d M Y H:i') }}
                        </p>
                    @else
                        <span class="status-badge bg-danger text-white">
                            <i class="fas fa-thumbs-down"></i> TIDAK RELEVAN
                        </span>
                        <p class="text-white-50 small mt-2 mb-0">
                            <i class="fas fa-info-circle"></i> 
                            Anda menilai materi ini tidak relevan pada {{ $evaluation->user_evaluated_at->format('d M Y H:i') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Button Baca Materi -->
        {{-- <div class="mt-4 text-center">
            <a href="{{ route('materi-belajar.show', $material->id) }}" 
               class="btn btn-read-material"
               target="_blank">
                <i class="fas fa-external-link-alt"></i> 
                Baca Materi Lengkap
            </a>
        </div> --}}
    </div>
@endif

            </div>
            @endforeach
        
        
            <div class="d-flex justify-content-start">
                <button type="button" id="prevBtn" class="btn btn-secondary me-2">Sebelumnya</button>
                <button type="button" id="nextBtn" class="btn btn-primary">Selanjutnya</button>
            </div>
        
        </div>
   
        <div class="col-12 col-lg-3">
            <h5>Nomor Soal</h5>
            <div class="d-flex flex-wrap" id="navSoal">
                @foreach($soals as $key => $soal)
                @php
                    $jawabanUser = $jawabanUsers->where('soal_id', $soal->id)->first();
                    $btnClass = 'btn-secondary';
                    if ($jawabanUser) {
                        if ($jawabanUser->status === 'kosong') {
                            $btnClass = 'btn-secondary';
                        } elseif ($jawabanUser->status === 'benar') {
                            $btnClass = 'btn-success';
                        } elseif ($jawabanUser->status === 'salah') {
                            $btnClass = 'btn-danger';
                        }
                    }
                @endphp
                <button class="btn {{ $btnClass }} btn-outline-primary m-1 text-white nav-soal" data-key="{{ $key }}">{{ $key + 1 }}</button>
                @endforeach
            </div>
            <a href="{{url('/tryout')}}" class="btn btn-success mt-3">Lihat Tryout SKD Lain</a>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script>
// Submit user evaluation
function submitEvaluation(evaluationId, isRelevant) {
    // Disable buttons
    const buttons = document.querySelectorAll(`#eval-form-${evaluationId} .btn-evaluation`);
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    });

    // AJAX request
    fetch('{{ route("cbf-evaluation.submit-feedback") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            evaluation_id: evaluationId,
            is_relevant: isRelevant
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Terima Kasih!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });

            // Reload halaman setelah 2 detik untuk update UI
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Show error
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });

            // Re-enable buttons
            buttons.forEach(btn => {
                btn.disabled = false;
            });
            buttons[0].innerHTML = '<i class="fas fa-thumbs-up"></i> Relevan';
            buttons[1].innerHTML = '<i class="fas fa-thumbs-down"></i> Tidak Relevan';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan. Silakan coba lagi.'
        });

        // Re-enable buttons
        buttons.forEach(btn => {
            btn.disabled = false;
        });
        buttons[0].innerHTML = '<i class="fas fa-thumbs-up"></i> Relevan';
        buttons[1].innerHTML = '<i class="fas fa-thumbs-down"></i> Tidak Relevan';
    });
}
</script>
<script>
    let currentSoal = 0;
    const totalSoals = {{ count($soals) }};
    const navSoalButtons = document.querySelectorAll('.nav-soal');

    // Navigation Functions
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentSoal > 0) {
            currentSoal--;
            showSoal(currentSoal);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentSoal < totalSoals - 1) {
            currentSoal++;
            showSoal(currentSoal);
        }
    });

    navSoalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const key = parseInt(button.getAttribute('data-key'));
            currentSoal = key;
            showSoal(currentSoal);
        });
    });

    function showSoal(index) {
        document.querySelectorAll('.soal-item').forEach((el, i) => {
            el.style.display = i === index ? 'block' : 'none';
        });

        navSoalButtons.forEach((btn, i) => {
            if (i === index) {
                btn.classList.add('btn-primary');
                btn.classList.remove('btn-outline-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            }
        });
    }

    function markAnswered(soalId) {
        const soalKey = parseInt(soalId.split('-')[1]);
        navSoalButtons[soalKey].classList.add('btn-success');
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