@extends('layouts.public')

@section('title', 'Pembahasan')

@push('after-style')
 <style>

.badge-category {
    background-color: #0dcaf01d;
    color: #1eafcc;
    padding: 10px;
    border-radius: 10px;
}
.total_score {
    display: flex; /* Aktifkan Flexbox */
    align-items: center; /* Rata tengah secara vertikal */
    margin-bottom: 15px;
}
.badge-lulus {
    background-color: #ffffff;
    color: #1ecc66;
    padding: 10px 20px;
    margin-left: 10px;
    border-radius: 8px;
}

 @media (min-width: 767.98px) {
    #navSoal .nav-soal{
        width: 70px;
    }
    #navSoal {
    max-height: 500px; /* Sesuaikan dengan tinggi soal */
    overflow-y: auto; /* Tambahkan scrollbar vertikal */
    border: 1px solid #ddd; /* Opsional: Untuk pembatas */
    box-sizing: border-box; /* Memastikan padding tidak memengaruhi lebar/tinggi */
    }
}
 </style>
@endpush

@section('content')
<div class="container px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 mb-5 mt-m-5">
            @foreach($soals as $key => $soal)
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
                        $poin = $soal->{'score_' . $option} ?? 0; // Poin spesifik untuk kategori TKP
                        $isCorrect = true; // Semua jawaban TKP dianggap valid
                    } else {
                        $isCorrect = strtoupper($option) === $soal->jawaban_benar;
                        $poin = $isCorrect ? $soal->poin : 0; // Poin hanya untuk jawaban benar
                    }
                    
                    $isChecked = strtoupper($option) === $jawabanUsers[$key]->jawaban_user ? 'checked' : '';
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
                <p class="pt-1">{!! nl2br(e($jawabanUsers[$key]->soal->pembahasan)) !!}</p>
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
                $btnClass = 'btn-secondary'; // Default: Tidak menjawab (Abu-abu)
                if ($jawabanUser) {
                    if ($jawabanUser->status === 'kosong') {
                        $btnClass = 'btn-secondary'; // Status 'kosong'
                    } elseif ($jawabanUser->status === 'benar') {
                        $btnClass = 'btn-success'; // Status 'benar'
                    } elseif ($jawabanUser->status === 'salah') {
                        $btnClass = 'btn-danger'; // Status 'salah'
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
