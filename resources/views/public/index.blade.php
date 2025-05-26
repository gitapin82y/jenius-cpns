@extends('layouts.public')
 
@section('title', 'Tryout CPNS TES SKD CASN Gratis - Jenius CPNS')

@push('after-style')

@endpush

@section('content')
 <div class="container">
    <div class="row">
        <div class="col-12 col-md-6 align-self-center">
            <h1>
                 Sukses CPNS Bersama! Persiapkan Diri dengan Tryout dan Strategi Belajar yang Efektif!
            </h1>
            <p class="py-3">Gabung bersama kami untuk dapatkan ratusan latihan soal tryout, evaluasi hasil tryout, serta rekomendasi materi dalam bentuk teks dan video yang menyesuaikan dengan hasil tryout. Yuk, hadapi ujian dengan percaya diri dan strategi belajar yang efektif!</p>
            <a href="{{url('/login')}}" class="btn btn-primary text-white py-3 px-4 flex-wrap flex-sm-shrink-0">Masuk Sekarang</a>
        </div>
        <div class="col-12 col-md-6 align-self-center text-end">
            <img src="{{asset('assets/img/customer-img-1.jpg')}}" alt="img belom di generate">
        </div>
    </div>
 </div>
@endsection

@push('after-script')

@endpush
