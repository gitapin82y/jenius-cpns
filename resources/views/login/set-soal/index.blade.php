@extends('layouts.public')

@section('title', 'Set Soal Tryout SKD')

@push('after-style')
    <style>
        .blog-item {
            background-image: url('{{ asset('assets/img/bg.png') }}');
            background-size: cover;
            /* Menutupi seluruh area elemen */
            background-position: center;
            /* Posisikan gambar di tengah */
            position: relative;
            /* Gunakan 'relative' jika ingin mengatur 'z-index' */
            top: 0;
            display: block;
            z-index: 99;
        }

    </style>
@endpush
@include('login.set-soal.modal')

@section('content')
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="bg-breadcrumb-single"></div>
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Akses Tryout SKD</h4>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active text-primary">Tryout SKD</li>
        </ol>
    </div>
</div>
<!-- Header End -->




<div class="container-fluid blog">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-4">List Set Soal Tryout</h1>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse($setSoals as $setSoal)
                <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="blog-item bg-light rounded p-4">
                        <div class="mb-4">
                            <h4 class="text-primary mb-2">{{ $setSoal->title }}</h4>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ $setSoal->jumlah_soal }}
                                        Soal</span></p>
                                <p class="mb-0">Waktu<span class="text-dark fw-bold"> 110 Menit</span></p>
                            </div>
                        </div>
                     <a class="btn btn-primary rounded-pill py-2 px-4 btn-kerjakan" href="#" 
   data-paket="{{ $setSoal->paket_id }}" 
   data-soal="{{ $setSoal->id }}" 
   data-title="{{ $setSoal->title }}" 
   data-jumlah="{{ $setSoal->jumlah_soal }}">Kerjakan</a>

<a class="btn btn-dark rounded-pill py-2 px-4 ms-2 btn-riwayat" href="#"
   data-paket="{{ $setSoal->paket_id }}" 
   data-soal="{{ $setSoal->id }}" 
   data-title="{{ $setSoal->title }}">Riwayat Nilai</a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <h4 class="text-muted">Data belum tersedia</h4>
                        <p class="text-muted">Belum ada paket soal yang tersedia saat ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div><!-- Contact End -->

@endsection

@push('after-script')
<script>
    // Hapus localStorage saat halaman dimuat
    localStorage.removeItem('jawaban_users');
    localStorage.removeItem('timeRemaining');
    $(document).ready(function() {
    console.log('Document ready, setting up event listeners');
    
    // Event untuk tombol Kerjakan
    $(document).on('click', '.btn-kerjakan', function(e) {
        e.preventDefault();
        console.log('Tombol Kerjakan diklik');
        
        const paketId = $(this).data('paket');
        const setSoalId = $(this).data('soal');
        const setName = $(this).data('title');
        const jumlahSoal = $(this).data('jumlah');
        
        console.log('Parameters:', {paketId, setSoalId, setName, jumlahSoal});
        
        // Copy fungsi checkSubscription di sini
        try {
            const userAkses = @json($userAkses);
            console.log('userAkses:', userAkses);
            
            if (userAkses == false) {
                Swal.fire({
                    title: 'Tidak dapat akses tryout!',
                    text: 'Anda tidak dapat akses tryout, silahkan login terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Cancel',
                    showCancelButton: true,
                    preConfirm: () => {
                        window.location.href = "{{ url('/login') }}";
                    },
                });
            } else {
                document.getElementById('modalSetName').textContent = setName;
                document.getElementById('modalJumlahSoal').textContent = jumlahSoal;
                document.getElementById('modalKerjakanBtn').href = '/tryout/' + setSoalId;
                new bootstrap.Modal(document.getElementById('kerjakanModal')).show();
            }
        } catch (error) {
            console.error('Error dalam click handler btn-kerjakan:', error);
        }
    });
    
    // Event untuk tombol Riwayat Nilai
    $(document).on('click', '.btn-riwayat', function(e) {
        e.preventDefault();
        console.log('Tombol Riwayat Nilai diklik');
        
        const paketId = $(this).data('paket');
        const setSoalId = $(this).data('soal');
        const setName = $(this).data('title');
        
        // Copy fungsi checkSubscriptionForHistory di sini
        try {
            const userAkses = @json($userAkses);
            
            if (userAkses == false) {
                Swal.fire({
                    title: 'Tidak dapat akses tryout!',
                    text: 'Anda tidak dapat akses tryout, silahkan login terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Cancel',
                    showCancelButton: true,
                    preConfirm: () => {
                        window.location.href = "{{ url('/login') }}";
                    },
                });
            } else {
                window.location.href = `{{ url('/tryout/result/${setSoalId}') }}`;
            }
        } catch (error) {
            console.error('Error dalam click handler btn-riwayat:', error);
        }
    });
    
    // Tambahkan log tombol yang ada
    console.log('Jumlah tombol kerjakan:', $('.btn-kerjakan').length);
    console.log('Jumlah tombol riwayat:', $('.btn-riwayat').length);
});
</script>
@endpush
