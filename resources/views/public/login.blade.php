@extends('layouts.public')
 
@section('title', 'Tryout CPNS TES SKD CASN Gratis - Jenius CPNS')

@push('after-style')
<link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">

<style>
        .styleEyePassword{
    position: absolute;
    right:15px;
    transform: translateY(-33px);
    cursor: pointer;
}
</style>
@endpush

@section('content')
 <div class="container">
    <div class="row mt-5">
    <div class="col-12 col-md-6 align-self-center">
                <h1 class="h4 text-gray-900">Login Platform Tryout & Belajar CPNS</h1>
                <p>Gabung bersama Jenius CPNS untuk merasakan manfaat sistem tryout dilengkapi dengan rekomendasi materi pembelajaran</p>
            <form action="{{ url('login') }}" method="POST" class="user">
                @csrf
                
                <div class="form-group">
                    <input name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror form-control-user"
                        id="exampleInputEmail"
                     placeholder="Masukkan Email">
                       @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    
                </div>
                <div class="form-group position-relative">
                    <input name="password" type="password" value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror form-control-user"
                        id="exampleInputPassword" placeholder="Masukkan Password">
                        <i class="fa fa-eye styleEyePassword" id="togglePassword"></i>
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="row justify-content-end">
                    {{-- <a class="small" href="{{url('/forgot-password')}}">Lupa Password?</a> --}}
                </div>

                <button type="submit" class="btn mt-3 btn-primary btn-user btn-block">
                    Masuk
                </button>
            </form>
            <div class="text-center mt-3 small">
                Belum punya akun? <a href="{{url('/register')}}">Daftar</a>
            </div>
    </div>
   <div class="col-12 col-md-6 align-self-center text-end">
            <img src="{{asset('assets/img/tryout-jenius-cpns.png')}}" alt="tryout jenius cpns" width="100%">
        </div>
    </div>
 </div>
@endsection

@push('after-script')
    <script>
        $(document).ready(function () {

            $('#togglePassword').click(function () {
                const passwordField = $('#exampleInputPassword');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            $('#togglePasswordConfirmation').click(function () {
                const passwordField = $('#password_confirmation');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

        });

    </script>
@endpush