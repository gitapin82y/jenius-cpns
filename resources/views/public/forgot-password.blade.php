@extends('layouts.login-system')
 
@section('title', 'Lupa Password')

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
                <h1 class="h4 text-gray-900 mb-4">Selamat Datang!</h1>
            </div>
            <form action="{{ route('login') }}" method="POST" class="user">
                @csrf
                
                <div class="form-group">
                    <input name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror form-control-user"
                        id="exampleInputEmail"
                     placeholder="Masukkan Email">
                       @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    
                </div>
                <div class="form-group">
                    <input name="password" type="password" value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror form-control-user"
                        id="exampleInputPassword" placeholder="Masukkan Password">
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="row justify-content-end">
                    <a class="small" href="{{url('/forgot-password')}}">Forgot Password?</a>
                </div>

                <button type="submit" class="btn mt-3 btn-primary btn-user btn-block">
                    Login
                </button>
            </form>
            <div class="text-center mt-3">
                <a class="small" href="{{url('/register')}}">Create an Account!</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
{{-- include script --}}
@endpush
