@extends('layouts.public')
 
@section('title', 'Soal Tryout')

@push('after-style')
<style>
    .btn-answered {
    background-color: #28a745;
    color: white;
}

.badge-category {
    background-color: #0dcaf01d;
    color: #1eafcc;
    padding: 10px;
    border-radius: 10px;
}

.badge-timer {
    background-color: #fd7d141e;
    color: #fd7e14;
    padding: 10px;
    border-radius: 10px;
    transform: translateY(50px);
}
.mt-m-5{
    transform: translateY(-50px);
}

@media (max-width: 767.98px) {
    #timer {
        margin-bottom: 10px;
    }
    .badge-timer {
    transform: translateY(-10px);
}
.mt-m-5{
    transform: translateY(0px);
}

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
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 mb-5 mt-m-5">
            <div class="col-12 text-md-end text-center">
                <h4 id="timer" class="badge-timer d-inline-block"></h4>
            </div>
            <form id="tryoutForm" action="{{ route('tryout.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="set_soal_id" value="{{ $set_soal_id }}">
                
                @foreach($soals as $key => $soal)
                    <div class="soal-item mb-4" id="soal-{{ $key }}" style="display: {{ $key === 0 ? 'block' : 'none' }};">
                        <div class="row col-12 pb-3 align-items-center">
                            <!-- Category -->
                            <div class="col-md-6 col-12 text-md-start text-center">
                                <p class="badge-category d-inline-block">
                                    <strong>{{ $soal->kategori }}</strong> - {{ $soal->tipe }}
                                    @if($soal->kategori !== 'TKP')
                                        | <strong>Points:</strong> {{ $soal->poin }}
                                    @endif
                                </p>
                            </div>
                        </div>                        
                        @if($soal->foto)
                            <img src="{{ asset('storage/' . $soal->foto) }}" alt="Question Image" width="50%" class="img-fluid mb-3">
                        @endif
                        <p>{{ $key+1 . '.) ' .$soal->pertanyaan }}</p>
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                            <div class="form-check py-1">
                                <input class="form-check-input" type="radio" name="jawaban_users[{{ $soal->id }}]" id="jawaban-{{ $soal->id }}-{{ $option }}" value="{{ strtoupper($option) }}">
                                <label class="form-check-label" for="jawaban-{{ $soal->id }}-{{ $option }}">
                                    {{ strtoupper($option) }}. {{ $soal->{'jawaban_' . $option} }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
        
                <div class="d-flex justify-content-start">
                    <button type="button" id="prevBtn" class="btn btn-secondary me-2">Sebelumnya</button>
                    <button type="button" id="nextBtn" class="btn btn-primary">Selanjutnya</button>
                </div>
            </form>
        
        </div>
   
        <div class="col-12 col-lg-3">
                <h5>Nomor Soal</h5>
                <div class="d-flex flex-wrap" id="navSoal">
                    @foreach($soals as $key => $soal)
                        <button class="btn btn-outline-primary m-1 nav-soal" data-key="{{ $key }}">{{ $key + 1 }}</button>
                    @endforeach
                </div>
                    
            <button type="button" class="btn btn-success mt-3" id="submitTryoutBtn">Submit Tryout</button>
        </div>
    </div>
  


</div>
@endsection

@push('after-script')
<script>
    let currentSoal = 0;
    const totalSoals = {{ count($soals) }};
    const navSoalButtons = document.querySelectorAll('.nav-soal');

    const timeLimit = 110 * 60; // 110 minutes
    let timeRemaining = getTimeRemaining();

    const timerInterval = setInterval(updateTimer, 1000);

    // Timer Functions
    function updateTimer() {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            document.getElementById('tryoutForm').submit();
        }
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('timer').innerText = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        timeRemaining--;
        localStorage.setItem('timeRemaining', timeRemaining);
    }

    function getTimeRemaining() {
        const savedTime = localStorage.getItem('timeRemaining');
        return savedTime !== null ? parseInt(savedTime, 10) : timeLimit;
    }

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

    // Load answers and timer from local storage
    loadAnswersFromLocalStorage();
    function loadAnswersFromLocalStorage() {
        const answers = JSON.parse(localStorage.getItem('jawaban_users') || '{}');
        Object.keys(answers).forEach(soalId => {
            const answer = answers[soalId];
            const input = document.querySelector(`input[name="jawaban_users[${soalId}]"][value="${answer}"]`);
            if (input) {
                input.checked = true;
                markAnswered(input.closest('.soal-item').id);
            }
        });
    }

    document.getElementById('tryoutForm').addEventListener('change', (event) => {
        if (event.target.name && event.target.name.startsWith('jawaban_users')) {
            const answers = JSON.parse(localStorage.getItem('jawaban_users') || '{}');
            answers[event.target.name.match(/\d+/)[0]] = event.target.value;
            localStorage.setItem('jawaban_users', JSON.stringify(answers));
            markAnswered(event.target.closest('.soal-item').id);
        }
    });

    showSoal(currentSoal);

    // document.getElementById('submitTryoutBtn').addEventListener('click', () => {
    //     clearInterval(timerInterval);
    //     localStorage.removeItem('jawaban_users');
    //     localStorage.removeItem('timeRemaining');
    //     document.getElementById('tryoutForm').submit();
    // });

     document.getElementById('submitTryoutBtn').addEventListener('click', () => {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin submit soal tryout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, submit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                isFormSubmitting = true;
                clearInterval(timerInterval);
                localStorage.removeItem('jawaban_users');
                localStorage.removeItem('timeRemaining');
                document.getElementById('tryoutForm').submit();
            }
        });
    });


    window.addEventListener('beforeunload', (event) => {
            if (!isFormSubmitting) { // Check if form is not being submitted
                const confirmationMessage = 'Data soal yang sudah dikerjakan akan hilang jika Anda meninggalkan halaman ini.';
                event.preventDefault(); // Required for Chrome
                event.returnValue = confirmationMessage; // Required for Firefox
                return confirmationMessage; // Required for other browsers
            }
        });

</script>
@endpush


