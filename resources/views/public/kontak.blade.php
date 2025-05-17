@extends('layouts.public')
 
@section('title', 'Kontak Lulus Bersama')

@push('after-style')

@endpush

@section('content')
   <!-- Header Start -->
   <div class="container-fluid bg-breadcrumb">
    <div class="bg-breadcrumb-single"></div>
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Kontak Lulus Bersama</h4>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active text-primary">Kontak</li>
        </ol>    
    </div>
</div>
<!-- Header End -->

<!-- Contact Start -->
<div class="container-fluid contact bg-light py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="contact-item">
                    <div class="pb-5">
                        <h1 class="display-4 mb-4">Hubungi Lulus Bersama</h1>
                        <p class="mb-0">Anda bisa menghubungi informasi kontak dibawah ini atau secara langsung melalui form kontak</p>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary btn-lg-square rounded-circle p-2"><i class="fa fa-phone-alt text-white"></i></div>
                        <div class="ms-4">
                            <h4>Whatsapp</h4>
                            <p class="mb-0">085141210513</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary btn-lg-square rounded-circle p-2"><i class="fa fa-envelope-open text-white"></i></div>
                        <div class="ms-4">
                            <h4>Email</h4>
                            <p class="mb-0">admin@lulusbersama.com</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary btn-lg-square rounded-circle p-2"><i class="fa fa-envelope-open text-white"></i></div>
                        <div class="ms-4">
                            <h4>Instagram</h4>
                            <p class="mb-0">@lulusbersama</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                <form action="send-mail" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" required id="name" name="name" placeholder="Your Name">
                                <label for="name">Your Name</label>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" required id="email" name="email" placeholder="Your Email">
                                <label for="email">Your Email</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" required placeholder="Leave a message here" id="message" name="message" style="height: 160px"></textarea>
                                <label for="message">Message</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 py-3" onclick="this.disabled=true;this.form.submit();">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
            {{-- <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded h-100">
                    <iframe class="rounded-top w-100" 
                    style="height: 500px; margin-bottom: -6px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387191.33750346623!2d-73.97968099999999!3d40.6974881!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sbd!4v1694259649153!5m2!1sen!2sbd" 
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-bottom p-4">
                        <div class="d-flex">
                            <a class="btn btn-dark btn-lg-square rounded-circle me-2" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-dark btn-lg-square rounded-circle mx-2" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-dark btn-lg-square rounded-circle mx-2" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-dark btn-lg-square rounded-circle mx-2" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
<!-- Contact End -->
@endsection

@push('after-script')
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
