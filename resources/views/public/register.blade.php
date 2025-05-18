@extends('layouts.login-system')

@section('title', 'Register')

@push('after-style')

@endpush

@section('content')
<div class="row" style="min-height:90vh">
    <div class="col-12 align-self-center">
        <div class="p-5">
            <div class="text-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/lulus-bersama-logo.png') }}"
                        alt="logo jenius CPNS" width="80px">
                </a>
                <h1 class="h4 text-gray-900 my-3">Daftar Akun</h1>
            </div>
            <form action="{{ url('register') }}" method="POST" class="user">
                @csrf

                <div class="row">
                    <div class="col-12">
                        <!-- Nama -->
                        <div class="form-group">
                            <input name="name" type="text" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror form-control-user"
                                id="exampleInputName" placeholder="Masukkan Nama Lengkap">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <input name="email" type="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror form-control-user"
                                id="exampleInputEmail" placeholder="Masukkan Email">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- No Telepon -->
                        <div class="form-group">
                            <input name="phone" type="text" value="{{ old('phone') }}"
                                class="form-control @error('phone') is-invalid @enderror form-control-user"
                                id="exampleInputPhone" placeholder="Masukkan Nomor Telepon">
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group position-relative">
                            <input name="password" type="password" value="{{ old('password') }}"
                                class="form-control @error('password') is-invalid @enderror form-control-user"
                                id="exampleInputPassword" placeholder="Masukkan Password">
                            <i class="fa fa-eye styleEyePassword" id="togglePassword"></i>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Ulangi Password -->
                        <div class="form-group position-relative">
                            <input name="password_confirmation" type="password"
                                value="{{ old('password_confirmation') }}"
                                class="form-control @error('password_confirmation') is-invalid @enderror form-control-user"
                                id="password_confirmation" placeholder="Masukkan Konfirmasi Password">
                            <i class="fa fa-eye styleEyePassword" id="togglePasswordConfirmation"></i>
                            @error('password_confirmation')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn mt-3 btn-primary btn-user btn-block">
                    Daftar Akun
                </button>
            </form>

            <div class="text-center mt-3 small">
                Sudah Punya Akun? <a href="{{ url('/login') }}">Masuk</a>
            </div>

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
