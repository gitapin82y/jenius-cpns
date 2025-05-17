@extends('layouts.public')
 
@section('title', 'Set Soal Tryout SKD')

@push('after-style')
<style>
 .blog-item {
    background-image: url('{{ asset('assets/img/bg.png') }}');
    background-size: cover; /* Menutupi seluruh area elemen */
    background-position: center; /* Posisikan gambar di tengah */
    position: relative; /* Gunakan 'relative' jika ingin mengatur 'z-index' */
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

    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            @if($user->paket_id == 1)
                @if($user->is_akses == 1)
                    <div class="alert alert-info alert-dismissible fade show  mt-5" role="alert">
                        <strong>Hai {{ $user->name }}!</strong> Saat ini paket kamu adalah <strong>{{ $user->paket->nama_paket }}</strong> upgrade paket kamu untuk akses dan benefit lebih lengkap <a href="{{url('/#paket-tryout')}}" class="text-primary"><strong>Upgrade Paket Tryout</strong></a>
                        <br>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @else
                    <div class="alert alert-info alert-dismissible fade show  mt-5" role="alert">
                        <strong>Hai {{ $user->name }}!</strong> Sepertinya anda tidak memiliki akses paket tryout, kamu bisa daftar/pesan paket tryout yang tersedia <a href="{{url('/#paket-tryout')}}" class="text-primary"><strong>Lihat Paket Tryout</strong></a>
                        <br>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @else
            <div class="alert alert-info alert-dismissible fade show  mt-5" role="alert">
                <strong>Hai {{ $user->name }}!</strong> Saat ini paket member kamu adalah <strong>{{ $user->paket->nama_paket }}</strong> dengan durasi masa aktif 6 bulan.
                <br>
                <strong>{{ $statusMessage }}.</strong>
                @if($user->paket_id !== 4)
                ingin akses dan benefit lebih lengkap? <a href="{{url('/#paket-tryout')}}" class="text-primary"><strong>Upgrade Paket Tryout</strong></a>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
    </div>

    

    <div class="container-fluid blog">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h1 class="display-4">List Set Soal Tryout</h1>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($setSoals as $setSoal)
                    <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="blog-item bg-light rounded p-4">
                            <div class="mb-4">
                                <h4 class="text-primary mb-2">{{ $setSoal->paket->nama_paket }}</h4>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ $setSoal->jumlah_soal }} Soal</span></p>
                                    <p class="mb-0">Waktu<span class="text-dark fw-bold"> 110 Menit</span></p>
                                </div>
                            </div>
                            <div class="my-4">
                                <a href="#" class="h4">{{ $setSoal->title }}</a>
                            </div>
                            <a class="btn btn-primary rounded-pill py-2 px-4" href="#" onclick="checkSubscription({{ $setSoal->paket_id}},{{ $setSoal->id}},'{{ $setSoal->paket->nama_paket }}','{{ $setSoal->title }}', {{ $setSoal->jumlah_soal }})">Kerjakan</a>
                            <a class="btn btn-dark rounded-pill py-2 px-4 ms-2" href="#" onclick="checkSubscriptionForHistory({{ $setSoal->paket_id }}, {{ $setSoal->id }},'{{ $setSoal->paket->nama_paket }}','{{ $setSoal->title }}')">Riwayat Nilai</a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div><!-- Contact End -->

@endsection

@push('after-script')
<script>
    localStorage.removeItem('jawaban_users');
    localStorage.removeItem('timeRemaining');
    

    function checkSubscription(paketId,setSoalId,paketName,setName,jumlahSoal) {
        const userPaketId = @json($userPaketId);
        const userAkses = @json($userAkses);

        if(userPaketId < paketId){
            Swal.fire({
                title: 'Belum Berlangganan',
                text: 'Anda belum berlangganan pada paket '+paketName,
                icon: 'warning',
                confirmButtonText: 'Berlangganan Sekarang',
                cancelButtonText: 'Cancel',
                showCancelButton: true, // Menampilkan tombol cancel
                preConfirm: () => {
                    window.location.href = "{{ url('/#paket-tryout') }}"; // Ganti dengan route atau URL yang sesuai
                },
            });
        }else if (userAkses == false) {
            Swal.fire({
                title: 'Tidak memiliki akses tryout!',
                text: 'Anda tidak memiliki akses tryout, upgrade paket anda sekarang.',
                icon: 'warning',
                confirmButtonText: 'Upgrade paket',
                cancelButtonText: 'Cancel',
                showCancelButton: true, // Menampilkan tombol cancel
                preConfirm: () => {
                    window.location.href = "{{ url('/#paket-tryout') }}"; // Ganti dengan route atau URL yang sesuai
                },
            });
        }else {
            // Jika pengguna berlangganan paket yang sesuai
            document.getElementById('modalPaketName').textContent = paketName;
            document.getElementById('modalSetName').textContent = setName;
            document.getElementById('modalJumlahSoal').textContent = jumlahSoal;
            document.getElementById('modalKerjakanBtn').href = '/tryout/' + setSoalId; // Update link if needed
            new bootstrap.Modal(document.getElementById('kerjakanModal')).show();
        }
    }

    function checkSubscriptionForHistory(paketId, setSoalId,paketName,setName ) {
        const userPaketId = @json($userPaketId);
        const userAkses = @json($userAkses);

    if(userPaketId < paketId){
            
            Swal.fire({
                title: 'Belum Berlangganan',
                text: 'Anda belum berlangganan pada paket '+paketName,
                icon: 'warning',
                confirmButtonText: 'Berlangganan Sekarang',
                cancelButtonText: 'Cancel',
                showCancelButton: true, // Menampilkan tombol cancel
                preConfirm: () => {
                    window.location.href = "{{ url('/#paket-tryout') }}"; // Ganti dengan route atau URL yang sesuai
                },
            });
        }else if (userAkses == false) {
            Swal.fire({
                title: 'Tidak memiliki akses tryout!',
                text: 'Anda tidak memiliki akses tryout, upgrade paket anda sekarang.',
                icon: 'warning',
                confirmButtonText: 'Upgrade paket',
                cancelButtonText: 'Cancel',
                showCancelButton: true, // Menampilkan tombol cancel
                preConfirm: () => {
                    window.location.href = "{{ url('/#paket-tryout') }}"; // Ganti dengan route atau URL yang sesuai
                },
            });
    }else {
        // Jika pengguna berlangganan, arahkan ke halaman riwayat nilai
        window.location.href = `{{url('/tryout/result/${setSoalId}')}}`;
    }
    }

</script>
@endpush
