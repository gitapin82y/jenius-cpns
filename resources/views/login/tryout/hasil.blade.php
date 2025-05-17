@extends('layouts.public')

@section('title', 'Hasil Tryout SKD')

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
.form-check span{
    color : black;
 }
.bg-score{
    background-image: url('/assets/img/banner-score.png');
    background-position: top;
    background-repeat: no-repeat;
    background-size: cover;
    width: 100%;
    border-radius: 20px;
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
    <div class="row pb-4">
        <div class="col-12 row ms-0 bg-score">
            <div class="pt-md-0 pt-4 col-12 col-md-6 align-self-center">
                <h2 class="text-white pb-2">Skor Akhir Hasil Tryout SKD</h2>
                <div class="total_score d-flex">
                    <h2 class="text-white my-0">
                        @if($hasilTryout)
                            <h2 class="text-white my-0">
                                {{ ($hasilTryout->twk_score ?? 0) + ($hasilTryout->tiu_score ?? 0) + ($hasilTryout->tkp_score ?? 0) }}
                            </h2>
                        @else
                            <h2 class="text-white my-0">0</h2>
                        @endif 
                    </h2>
                    <small style="font-size: 16px;color:white;margin-left:10px;">Dari {{ $hasilTryout->total_poin }}</small>
                    <p class="badge-lulus my-0 d-inline-block">
                        <strong>Lulus</strong>
                    </p>
                </div>
                <div class="col-12 text-start">
                    <span class="badge rounded-pill mt-2 py-2 px-4 bg-success">Benar: {{ $hasilTryout->total_benar }}</span>
                    <span class="badge rounded-pill mt-2 py-2 px-4 bg-danger">Salah: {{ $hasilTryout->total_salah }}</span>
                    <span class="badge rounded-pill mt-2 py-2 px-4 bg-secondary">Kosong: {{ $hasilTryout->total_kosong }}</span>
                </div>
            </div>
            <div class="pt-md-0 pt-4 col-12 col-md-6 text-md-end text-center">
                <img src="{{asset('assets/img/people-score.png')}}" alt="">
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 mb-5 mt-m-5">
            @foreach($soals as $key => $soal)
            <div class="soal-item mb-4" id="soal-{{ $key }}" style="display: {{ $key === 0 ? 'block' : 'none' }};">
                <!-- Kategori Soal -->
                <div class="col-md-6 col-12 text-start">
                    <p class="badge-category d-inline-block">
                        <strong>Kategori:</strong> {{ strtoupper($soal->kategori) }}
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


        <div class="row justify-content-center mt-5 mb-3">
            <h2 class="mb-4">Grafik Hasil Tryout CPNS SKD</h2>
               <!-- Grafik Skor Total -->
               <div class="col-12 col-md-6">
                <div id="grafik-total-skor" class="mb-5"></div>

               </div>
               <div class="col-12 col-md-6">
            <div id="grafik-twk" class="mb-5"></div>

               </div>
            <!-- Grafik TWK -->

            <div class="col-12">
            <div id="grafik-tiu" class="mb-5"></div>


               </div>
               <div class="col-12">
            <div id="grafik-tkp" class="mb-5"></div>


               </div>
        
            <!-- Grafik TIU -->
        
            <!-- Grafik TKP -->

        </div>

@if(Auth::user()->is_review == 1)
<div class="row justify-content-center mt-5 mb-3">
    <h4 class="col-12 col-md-8 text-center">Berikan kami penilaian atau kritik dan saran agar kami dapat lebih berkembang</h4>
</div>
<form action="{{url('/send-feedback')}}" class="pb-4" method="POST">
    @csrf
    <div class="row g-3">
                <input type="hidden" class="form-control" id="name" value="{{Auth::user()->name}}" name="name">
                <input type="hidden" class="form-control" id="email" name="email" value="{{Auth::user()->email}}">
        <div class="col-12">
            <div class="form-floating">
                <textarea class="form-control" required placeholder="Kirim penilaian berupa kritik atau saran dan pengalaman anda menggunakan lulus bersama" id="message" name="message" style="height: 160px"></textarea>
                <label for="message">Penilaian (Kritik/Saran)</label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100 py-3" onclick="this.disabled=true;this.form.submit();">Send Message</button>
        </div>
    </div>
</form>
@endif

</div>
@endsection

@push('after-script')
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
    // Data dari Controller
    const hasilTryout = @json($hasilTryout);

    // Data TWK
    const twkData = [
        { name: 'Nasionalisme', y: hasilTryout.nasionalisme },
        { name: 'Integritas', y: hasilTryout.integritas },
        { name: 'Bela Negara', y: hasilTryout.bela_negara },
        { name: 'Pilar Negara', y: hasilTryout.pilar_negara },
        { name: 'Bahasa Indonesia', y: hasilTryout.bahasa_indonesia },
    ];

    // Data TIU
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

    // Data TKP
    const tkpData = [
        { name: 'Pelayanan Publik', y: hasilTryout.pelayanan_publik },
        { name: 'Jejaring Kerja', y: hasilTryout.jejaring_kerja },
        { name: 'Sosial Budaya', y: hasilTryout.sosial_budaya },
        { name: 'Teknologi Informasi dan Komunikasi (TIK)', y: hasilTryout.teknologi_informasi_dan_komunikasi_tik },
        { name: 'Profesionalisme', y: hasilTryout.profesionalisme },
        { name: 'Anti Radikalisme', y: hasilTryout.anti_radikalisme },
    ];

    // Data Total Skor
    const totalScoreData = [
        { name: 'TWK', y: hasilTryout.twk_score },
        { name: 'TIU', y: hasilTryout.tiu_score },
        { name: 'TKP', y: hasilTryout.tkp_score },
    ];

    // Grafik TWK
    Highcharts.chart('grafik-twk', {
        chart: { type: 'column' },
        title: { text: 'Grafik TWK' },
        xAxis: { type: 'category' },
        yAxis: { title: { text: 'Poin' } },
        series: [{ name: 'TWK', data: twkData }]
    });

    // Grafik TIU
    Highcharts.chart('grafik-tiu', {
        chart: { type: 'column' },
        title: { text: 'Grafik TIU' },
        xAxis: { type: 'category' },
        yAxis: { title: { text: 'Poin' } },
        series: [{ name: 'TIU', data: tiuData }]
    });

    // Grafik TKP
    Highcharts.chart('grafik-tkp', {
        chart: { type: 'column' },
        title: { text: 'Grafik TKP' },
        xAxis: { type: 'category' },
        yAxis: { title: { text: 'Poin' } },
        series: [{ name: 'TKP', data: tkpData }]
    });

    // Grafik Total Skor
    Highcharts.chart('grafik-total-skor', {
        chart: { type: 'pie' },
        title: { text: 'Grafik Total Skor' },
        series: [{
            name: 'Total Skor',
            colorByPoint: true,
            data: totalScoreData
        }]
    });
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
