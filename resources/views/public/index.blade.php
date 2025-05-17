@extends('layouts.public')
 
@section('title', 'Tryout CPNS TES SKD CASN Gratis - Jenius CPNS')

@push('after-style')

@endpush

@section('content')
    <!-- Carousel Start -->
    <div class="header-carousel owl-carousel">
        <div class="header-carousel-item">
            <div class="header-carousel-item-img-1">
                <img src="{{asset('assets/img/carousel-4.jpg')}}" class="img-fluid w-100" alt="Tryout CPNS TES SKD CASN Gratis - Jenius CPNS">
            </div>
            <div class="carousel-caption">
                <div class="carousel-caption-inner text-center p-3">
                    <h1 class="display-4 text-capitalize text-white mb-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.3s" style="animation-delay: 1.3s;">Tryout CPNS TES SKD CASN Gratis - Jenius CPNS</h1>
                    <p class="mb-5 fs-5 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;">Ikuti jenius CPNS tryout CPNS secara gratis atau berbayar untuk mempersiapkan dirimu menghadapi TES SKD CASN dengan percaya diri.
                    </p>
                    @auth
                    <a href="{{ url('/tryout') }}" class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;">Akses Tryout</a>
                    @else
                    <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#paket-tryout">Daftar Sekarang</a>
                    @endauth
                    <a class="btn btn-dark rounded-pill py-3 px-5 mb-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#tentang">Tentang Kami</a>
                </div>
            </div>
        </div>
        <div class="header-carousel-item mx-auto">
            <div class="header-carousel-item-img-2">
                <img src="{{asset('assets/img/carousel-2.jpg')}}" class="img-fluid w-100" alt="Paket tryout lengkap dan pembahasan soal CASN">
            </div>
            <div class="carousel-caption">
                <div class="carousel-caption-inner text-center p-3">
                    <h2 class="display-1 text-capitalize text-white mb-4">Paket Tryout Lengkap</h2>
                    <p class="mb-5 fs-5">Pilih dari berbagai paket tryout dengan akses pembahasan soal, riwayat nilai, dan grafik statistik skor.
                    </p>
                    @auth
                    <a href="{{ url('/tryout') }}" class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;">Akses Tryout</a>
                    @else
                    <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#paket-tryout">Daftar Sekarang</a>
                    @endauth
                    <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#tentang">Tentang Kami</a>
                </div>
            </div>
        </div>
        {{-- <div class="header-carousel-item">
            <div class="header-carousel-item-img-3">
                <img src="{{asset('assets/img/carousel-3.jpg')}}" class="img-fluid w-100" alt="Image">
            </div>
            <div class="carousel-caption">
                <div class="carousel-caption-inner text-center p-3">
                    <h1 class="display-1 text-capitalize text-white mb-4">Bergabung Bersama Kami</h1>
                    <p class="mb-5 fs-5">Daftar sekarang dan raih mimpimu menjadi ASN dengan persiapan terbaik.
                    </p>
                    @auth
                    <a href="{{ url('/tryout') }}" class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;">Akses Tryout</a>
                    @else
                    <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#paket-tryout">Daftar Sekarang</a>
                    @endauth
                    <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#tentang">Tentang Kami</a>
                </div>
            </div>
        </div> --}}
    </div>
    <!-- Carousel End -->


    <!-- About Start -->
    <div class="container-fluid about bg-light py-5" id="tentang">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 col-xl-5 wow fadeInLeft" data-wow-delay="0.1s">
                    <div class="about-img">
                        <img src="{{asset('assets/img/about-2.jpg')}}" class="img-fluid w-100 rounded" alt="Image">
                    </div>
                </div>
                <div class="col-lg-6 col-xl-7 wow fadeInRight" data-wow-delay="0.3s">
                    <strong class="text-primary">About</strong>
                    <h2 class="display-5 mb-4">Tentang Jenius CPNS</h2>
                    <p class="text ps-4 mb-4">Jenius CPNS adalah platform yang menyediakan tryout TES SKD CASN baik gratis maupun berbayar, dirancang untuk membantu kamu mempersiapkan diri secara optimal.
                    </p>
                    <div class="row g-4 justify-content-between mb-5">
                        <div class="col-lg-6 col-xl-5">
                            <p class="text-dark"><i class="fas fa-check-circle text-success me-1"></i> Pembahasan Soal</p>
                            <p class="text-dark mb-0"><i class="fas fa-check-circle text-success me-1"></i> Riwayat Nilai</p>
                        </div>
                        <div class="col-lg-6 col-xl-7">
                            <p class="text-dark"><i class="fas fa-check-circle text-success me-1"></i> 50 set soal SKD CASN</p>
                            <p class="text-dark mb-0"><i class="fas fa-check-circle text-success me-1"></i> Grafik Skor (TWK, TIU, TKP)</p>
                        </div>
                    </div>
                    {{-- <div class="row g-4 justify-content-between mb-5">
                        <div class="col-xl-5"><a href="#" class="btn btn-primary rounded-pill py-3 px-5">Discover More</a></div>
                        <div class="col-xl-7 mb-5">
                            <div class="about-customer d-flex position-relative">
                                <img src="{{asset('assets/img/customer-img-1.jpg')}}" class="img-fluid btn-xl-square position-absolute" style="left: 0; top: 0;"  alt="Image">
                                <img src="{{asset('assets/img/customer-img-2.jpg')}}" class="img-fluid btn-xl-square position-absolute" style="left: 45px; top: 0;" alt="Image">
                                <img src="{{asset('assets/img/customer-img-3.jpg')}}" class="img-fluid btn-xl-square position-absolute" style="left: 90px; top: 0;" alt="Image">
                                <img src="{{asset('assets/img/customer-img-1.jpg')}}" class="img-fluid btn-xl-square position-absolute" style="left: 135px; top: 0;" alt="Image">
                                <div class="position-absolute text-dark" style="left: 220px; top: 10px;">
                                    <p class="mb-0">5m+ Trusted</p>
                                    <p class="mb-0">Global Customers</p>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row g-4 text-center align-items-center justify-content-center">
                        <div class="col-sm-4">
                            <div class="bg-primary rounded p-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="counter-value fs-1 fw-bold text-dark" data-toggle="counter-up">500</span>
                                    <span class="text-dark fs-1 mb-0" style="font-weight: 600; font-size: 25px;">+</span>
                                </div>
                                <div class="w-100 d-flex align-items-center justify-content-center">
                                    <p class="text-white mb-0">Peserta Terdaftar</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-dark rounded p-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="counter-value fs-1 fw-bold text-white" data-toggle="counter-up">5500</span>
                                    <span class="text-white fs-1 mb-0" style="font-weight: 600; font-size: 25px;">+</span>
                                </div>
                                <div class="w-100 d-flex align-items-center justify-content-center">
                                    <p class="mb-0 text-white">Soal Tersedia</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-primary rounded p-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="counter-value fs-1 fw-bold text-dark" data-toggle="counter-up">10</span>
                                    <span class="text-dark fs-1 mb-0" style="font-weight: 600; font-size: 25px;">+</span>
                                </div>
                                <div class="w-100 d-flex align-items-center justify-content-center">
                                    <p class="text-white mb-0">Tahun Pengalaman</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Testimonial Start -->
    <div class="container-fluid testimonial bg-light py-5">
        <div class="container py-5">
            <div class="row g-4 align-items-center">
                <div class="col-xl-4 wow fadeInLeft" data-wow-delay="0.1s">
                    <div class="h-100 rounded">
                        <strong class="text-primary">Testimoni</strong>
                        <h2 class="display-4 mb-4">Apa Kata Mereka?</h2>
                        <p class="mb-4">Lihat apa yang dikatakan pengguna kami tentang pengalaman mereka dengan Jenius CPNS.</p>
                        {{-- <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="#">Read All Reviews <i class="fas fa-arrow-right ms-2"></i></a> --}}
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="testimonial-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
                        <div class="testimonial-item bg-white rounded p-4 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div><i class="fas fa-quote-left fa-3x text-dark me-3"></i></div>
                                <p class="mt-4">Tryoutnya sangat membantu! Saya merasa lebih siap untuk menghadapi SKD.
                                </p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="my-auto text-end">
                                    <h5>Andi</h5>
                                    <p class="mb-0">24 Tahun</p>
                                </div>
                                <div class="bg-white rounded-circle ms-3">
                                    <img src="{{asset('assets/img/testimonial-1.jpg')}}" class="rounded-circle p-2" style="width: 80px; height: 80px; border: 1px solid; border-color: var(--bs-primary);" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-item bg-white rounded p-4 wow fadeInUp" data-wow-delay="0.5s">
                            <div class="d-flex">
                                <div><i class="fas fa-quote-left fa-3x text-dark me-3"></i></div>
                                <p class="mt-4">Paket Saban benar-benar lengkap, saya suka fitur grafik statistiknya.
                                </p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="my-auto text-end">
                                    <h5>Dewi Putri</h5>
                                    <p class="mb-0">30 Tahun</p>
                                </div>
                                <div class="bg-white rounded-circle ms-3">
                                    <img src="{{asset('assets/img/testimonial-2.jpg')}}" class="rounded-circle p-2" style="width: 80px; height: 80px; border: 1px solid; border-color: var(--bs-primary);" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-item bg-white rounded p-4 wow fadeInUp" data-wow-delay="0.7s">
                            <div class="d-flex">
                                <div><i class="fas fa-quote-left fa-3x text-dark me-3"></i></div>
                                <p class="mt-4">Jenius CPNS membuat persiapan SKD jadi lebih mudah dan terstruktur.
                                </p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="my-auto text-end">
                                    <h5>Budi Septiawan</h5>
                                    <p class="mb-0">27 Tahun</p>
                                </div>
                                <div class="bg-white rounded-circle ms-3">
                                    <img src="{{asset('assets/img/testimonial-3.jpg')}}" class="rounded-circle p-2" style="width: 80px; height: 80px; border: 1px solid; border-color: var(--bs-primary);" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->


    <!-- FAQ Start -->
    <div class="container-fluid faq py-5">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                    <div class="pb-5">
                        <strong class="text-primary">FAQs</strong>
                        <h2 class="display-4">Pertanyaan yang Sering Diajukan</h2>
                    </div>
                   <div class="accordion bg-light rounded p-4" id="accordionExample">
                        <div class="accordion-item border-0 mb-4">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button text-dark fs-5 fw-bold rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseTOne">
                                    Apakah harus mendaftar untuk ikut tryout gratis?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body my-2">
                                    <p>Ya, pendaftaran diperlukan untuk mengikuti tryout gratis.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-4">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed text-dark fs-5 fw-bold rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Bagaimana cara membayar untuk paket berbayar?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body my-2">
                                    <p>Setelah memilih paket member di website anda akan diarahkan di whatsapp untuk melanjutkan pembayaran dengan pilihan metode pembayaran yang diberikan admin, setelah membayar akun anda akan diaktivasi oleh admin dan anda bisa akses paket member selama 6 bulan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-4">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed text-dark fs-5 fw-bold rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Apakah saya bisa mendapatkan akses ke pembahasan soal di tryout gratis?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body my-2">
                                    <p>Tidak, akses ke pembahasan soal hanya tersedia di paket berbayar.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-0">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed text-dark fs-5 fw-bold rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Apakah saya bisa melihat hasil tes kembali dilain waktu?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                <div class="accordion-body my-2">
                                    <p>Iya, anda dapat melihat hasil tes anda dilain waktu jika sudah pernah mengerjakan tes</p>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                    <div class="faq-img RotateMoveRight rounded">
                        <img src="{{asset('assets/img/faq-img.jpg')}}" class="img-fluid rounded w-100" alt="Image">
                        {{-- <a class="faq-btn btn btn-primary rounded-pill text-white py-3 px-5" href="#">Read More Q & A <i class="fas fa-arrow-right ms-2"></i></a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FAQ End -->


    {{-- modal formulir --}}
<!-- Modal -->
<div class="modal fade" id="pesanModal" tabindex="-1" aria-labelledby="pesanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pesanModalLabel">Formulir Pemesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-4">
                <form id="paymentForm" action="{{ route('beliPaket', ['paketId' => '__paketId__']) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_paket" class="form-label">Nama Paket</label>
                        <input type="text" class="form-control" id="nama_paket" name="nama_paket" readonly>
                    </div>
                    <input type="hidden" name="type_paket" id="type_paket">
                    <input type="hidden" name="paket_id" id="paket_id">
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="text" class="form-control" id="harga" name="harga" readonly>
                    </div>
                    @auth
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" id="name" name="name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" id="email" name="email" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill py-2 px-4 mt-2 w-100">Lakukan Pembayaran</button>
                    @else
                    <small class="text-danger">* Untuk Melanjutkan Pembayaran Anda Harus Daftar/Login Akun Terlebih Dahulu dan Kembali ke Formulir Pemesanan ini.</small>
                        <a href="{{ url('/register') }}" class="btn btn-primary rounded-pill py-2 px-4 mt-3 w-100">Daftar Sekarang</a>
                    @endauth
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="daftarModal" tabindex="-1" aria-labelledby="daftarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="daftarModalLabel">Formulir Pendaftaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-4">
                <p>Paket FREE membutuhkan persetujuan admin, anda akan diarahkan ke whatsapp untuk konfirmasi pendaftaran akun.</p>
                <form id="formPesan" action="{{ route('formulir') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_paket_free" class="form-label">Nama Paket</label>
                        <input type="text" class="form-control" value="Free" id="nama_paket_free" name="nama_paket" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="harga_free" class="form-label">Harga</label>
                        <input type="text" class="form-control" value="Rp0,00"  id="harga_free" name="harga" readonly>
                    </div>
                    @auth
                    <div class="mb-3">
                        <label for="name_free" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" id="name_free" name="name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="email_free" class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" id="email_free" name="email" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill py-2 px-4 mt-2 w-100">Hubungi Admin</button>
                    @else
                    <small class="text-danger">* Untuk Melanjutkan Pendaftaran Anda Harus Daftar/Login Akun Terlebih Dahulu dan Kembali ke Formulir Pendaftaran ini.</small>
                        <a href="{{ url('/register') }}" class="btn btn-primary rounded-pill py-2 px-4 mt-3 w-100">Daftar Sekarang</a>
                    @endauth
                </form>
            </div>
        </div>
    </div>
</div>

{{-- end modal formulir --}}
@endsection

@push('after-script')
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{env('MIDTRANS_CLIENT_KEY')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const pesanButtons = document.querySelectorAll('.pesan-sekarang');
    
    pesanButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const serviceItem = this.closest('.service-item');
            const namaPaket = serviceItem.querySelector('.nama_paket').textContent.trim();
            const hargaPaket = serviceItem.querySelector('.harga_paket').textContent.trim();
            const typePaket = serviceItem.querySelector('.type_paket').textContent.trim();
            const paketId = serviceItem.querySelector('.paket_id').textContent.trim();

            const modal = document.querySelector('#pesanModal');
            modal.querySelector('#nama_paket').value = namaPaket;
            modal.querySelector('#type_paket').value = typePaket;
            modal.querySelector('#harga').value = hargaPaket;
            modal.querySelector('#paket_id').value = paketId;
            
            const paymentForm = modal.querySelector('#paymentForm');
            paymentForm.action = paymentForm.action.replace('__paketId__', paketId);
        });
    });

    // Handle form submit with AJAX
    const paymentForm = document.querySelector('#paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const formData = new FormData(paymentForm);
            console.log(formData);
            // Send data to controller to get snap token
            fetch(paymentForm.action, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.pembelian) {
                    // Get Snap Token from response
                    const snapToken = data.pembelian.snap_token;
                    
                    // Call Midtrans Snap UI with the token
                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            const redirectUrl = "{{ url('payment-success') }}/" + data.pembelian.id;
                            window.location.href = redirectUrl;

                        },
                        onPending: function(result) {
                            // Handle payment pending
                            console.log('Payment is pending:', result);
                        },
                        onError: function(result) {
                            // Handle payment error
                            console.error('Payment failed:', result);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error during AJAX request:', error);
            });
        });
    }
});

</script>
@endpush
