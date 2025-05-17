@extends('layouts.login-system')
 
@section('title', 'Login')

@push('after-style')

@endpush

@section('content')
<div class="row" style="min-height:90vh">
    <div class="col-md-6 col-12 p-4 bg-login-image align-self-center">
        <img src="{{asset('img/login.svg')}}" width="100%" alt="">
    </div>
    <div class="col-md-6 col-12 align-self-center">
        <div class="p-5">
            <div class="text-center">
                <a href="{{url('/')}}">
                    <img src="{{asset('assets/img/lulus-bersama-logo.png')}}" alt="logo lulus bersama" width="80px">
                </a>
                <h4 class="h4 text-gray-900 my-3">Welcome Back, Login</h4>
            </div>
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
    </div>
</div>
@endsection

@push('after-script')
<script>
    $(document).ready(function() {
$('#togglePassword').click(function() {
    const passwordField = $('#exampleInputPassword');
    const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
    passwordField.attr('type', type);
    $(this).toggleClass('fa-eye fa-eye-slash');
});
});
</script>
@endpush
