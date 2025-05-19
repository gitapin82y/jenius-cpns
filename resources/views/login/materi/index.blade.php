@extends('layouts.public')

@section('title', 'Materi Pembelajaran CPNS')

@push('after-style')
    <style>
        .blog-item {
            background-image: url('{{ asset('assets/img/bg.png') }}');
            background-size: cover;
            background-position: center;
            position: relative;
            top: 0;
            display: block;
            z-index: 99;
        }
        
        /* Styling untuk card materi sesuai dengan contoh */
        .materi-card {
            transition: all 0.3s ease;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        .materi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .materi-card h4 {
            color: #4e73df;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .materi-card .progress {
            height: 8px;
            margin-bottom: 10px;
            background-color: #e9ecef;
        }
        
        .materi-card .progress-bar {
            background-color: #4e73df;
        }
        
        .materi-card .progress-text {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .locked-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 5px;
            z-index: 10;
        }
        
        .btn-rounded {
            border-radius: 50px;
            padding: 8px 24px;
        }
        
        .btn-dark-rounded {
            border-radius: 50px;
            padding: 8px 24px;
            background-color: #343a40;
            color: white;
        }
        
        .btn-dark-rounded:hover {
            background-color: #23272b;
            color: white;
        }
    </style>
@endpush

@section('content')
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="bg-breadcrumb-single"></div>
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Materi Pembelajaran CPNS</h4>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active text-primary">Materi</li>
        </ol>
    </div>
</div>
<!-- Header End -->

<div class="container-fluid blog">
    <div class="container py-5">
        
        <div class="row g-4 justify-content-center">
            @php
                // Menghitung jumlah materi dan status untuk TWK
                $totalTWKMaterials = 0;
                $completedTWKMaterials = 0;
                
                foreach($twkMaterials as $tipe => $materis) {
                    $totalTWKMaterials += count($materis);
                    foreach($materis as $materi) {
                        if(isset($userProgress[$materi->id])) {
                            $completedTWKMaterials++;
                        }
                    }
                }
                
                // Perhitungan persentase TWK
                $twkPercentage = $totalTWKMaterials > 0 ? ($completedTWKMaterials / $totalTWKMaterials) * 100 : 0;
                
                // Status TWK
                $twkStatus = ($totalTWKMaterials > 0) && ($completedTWKMaterials >= $totalTWKMaterials) ? 'Selesai' : 'Belum Selesai';
                
                // Cek apakah latihan TWK sudah dikerjakan
                $twkLatihanCompleted = false;
                if (count($twkLatihan) > 0) {
                    foreach ($twkLatihan as $latihan) {
                        if (isset($userTryoutProgress[$latihan->id])) {
                            $twkLatihanCompleted = true;
                            break;
                        }
                    }
                } else {
                    $twkLatihanCompleted = true;
                }
                
                // Menghitung jumlah materi dan status untuk TIU
                $totalTIUMaterials = 0;
                $completedTIUMaterials = 0;
                
                foreach($tiuMaterials as $tipe => $materis) {
                    $totalTIUMaterials += count($materis);
                    foreach($materis as $materi) {
                        if(isset($userProgress[$materi->id])) {
                            $completedTIUMaterials++;
                        }
                    }
                }
                
                // Perhitungan persentase TIU
                $tiuPercentage = $totalTIUMaterials > 0 ? ($completedTIUMaterials / $totalTIUMaterials) * 100 : 0;
                
                // Status TIU
                $tiuStatus = ($totalTIUMaterials > 0) && ($completedTIUMaterials >= $totalTIUMaterials) ? 'Selesai' : 'Belum Selesai';
                
                // Cek apakah latihan TIU sudah dikerjakan
                $tiuLatihanCompleted = false;
                if (count($tiuLatihan) > 0) {
                    foreach ($tiuLatihan as $latihan) {
                        if (isset($userTryoutProgress[$latihan->id])) {
                            $tiuLatihanCompleted = true;
                            break;
                        }
                    }
                } else {
                    $tiuLatihanCompleted = true;
                }
                
                // Menghitung jumlah materi dan status untuk TKP
                $totalTKPMaterials = 0;
                $completedTKPMaterials = 0;
                
                foreach($tkpMaterials as $tipe => $materis) {
                    $totalTKPMaterials += count($materis);
                    foreach($materis as $materi) {
                        if(isset($userProgress[$materi->id])) {
                            $completedTKPMaterials++;
                        }
                    }
                }
                
                // Perhitungan persentase TKP
                $tkpPercentage = $totalTKPMaterials > 0 ? ($completedTKPMaterials / $totalTKPMaterials) * 100 : 0;
                
                // Status TKP
                $tkpStatus = ($totalTKPMaterials > 0) && ($completedTKPMaterials >= $totalTKPMaterials) ? 'Selesai' : 'Belum Selesai';
                
                // Cek apakah latihan TKP sudah dikerjakan
                $tkpLatihanCompleted = false;
                if (count($tkpLatihan) > 0) {
                    foreach ($tkpLatihan as $latihan) {
                        if (isset($userTryoutProgress[$latihan->id])) {
                            $tkpLatihanCompleted = true;
                            break;
                        }
                    }
                } else {
                    $tkpLatihanCompleted = true;
                }
                
                // Pengecekan akses antar kategori
                $allTWKCompleted = ($totalTWKMaterials > 0) && ($completedTWKMaterials >= $totalTWKMaterials);
                $tiuUnlocked = $allTWKCompleted && $twkLatihanCompleted;
                
                $allTIUCompleted = ($totalTIUMaterials > 0) && ($completedTIUMaterials >= $totalTIUMaterials);
                $tkpUnlocked = $tiuUnlocked && $allTIUCompleted && $tiuLatihanCompleted;
                
                $allTKPCompleted = ($totalTKPMaterials > 0) && ($completedTKPMaterials >= $totalTKPMaterials);
                
                // Semua kategori selesai?
                $allMaterialsCompleted = $allTWKCompleted && $allTIUCompleted && $allTKPCompleted;
                $allLatihanCompleted = $twkLatihanCompleted && $tiuLatihanCompleted && $tkpLatihanCompleted;
            @endphp

   @if(!isset($isLoggedIn) || !$isLoggedIn)
        <!-- Pesan untuk pengguna yang belum login -->
        <div class="alert alert-info text-center">
            <h5><i class="fas fa-info-circle me-2"></i> Anda belum login</h5>
            <p class="mb-3">Silakan login untuk mengakses detail materi dan mengerjakan latihan.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Login Sekarang</a>
            <span class="mx-2">atau</span>
            <a href="{{ url('/register') }}" class="btn btn-outline-primary">Daftar Akun Baru</a>
        </div>
        @else
               
        <!-- Tryout Access Banner -->
        @if($allMaterialsCompleted && $allLatihanCompleted)
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success p-4 text-center">
                        <h4 class="alert-heading mb-3"><i class="fas fa-check-circle me-2"></i> Selamat!</h4>
                        <p class="mb-3">Anda telah menyelesaikan semua materi dan latihan. Anda sekarang bisa mengakses Tryout CPNS.</p>
                        <a href="{{ url('/tryout') }}" class="btn btn-success px-4 py-2">Akses Tryout CPNS</a>
                        
                        @php
                            // Update user access
                            if(Auth::check() && !Auth::user()->is_akses) {
                                Auth::user()->update(['is_akses' => true]);
                            }
                        @endphp
                    </div>
                </div>
            </div>
        @else
            <div class="row ">
                <div class="col-12">
                    <div class="alert alert-info p-4 text-center">
                        <h4 class="alert-heading mb-3"><i class="fas fa-info-circle me-2"></i> Petunjuk</h4>
                        <p class="mb-0">Untuk mengakses Tryout CPNS, Anda harus menyelesaikan semua materi dan latihan dari ketiga kategori (TWK, TIU, dan TKP).</p>
                    </div>
                </div>
            </div>
        @endif

        @endif
            
            <!-- Materi TWK Card -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="blog-item bg-light rounded p-4">
                    <div class="mb-4">
                        <h4 class="mb-2">Materi TWK</h4>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $twkPercentage }}%" aria-valuenow="{{ $twkPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="progress-text d-flex justify-content-between">
                            <span>Progress: {{ $completedTWKMaterials }}/{{ $totalTWKMaterials }}</span>
                            <span>{{ number_format($twkPercentage, 0) }}%</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ $totalTWKMaterials }} Materi</span></p>
                            <p class="mb-0">Status<span class="text-dark fw-bold"> {{ $twkStatus }}</span></p>
                        </div>
                    </div>
                    <a href="{{ !empty($twkMaterials->first()) ? route('materi-belajar.show', $twkMaterials->first()->first()->id) : '#' }}" class="btn btn-primary btn-rounded">Lihat Materi</a>
                </div>
            </div>
            
            <!-- Latihan TWK Card -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="blog-item bg-light rounded p-4">
                    @if(!$allTWKCompleted)
                        <div class="locked-overlay">
                            <div class="text-center text-white">
                                <i class="fas fa-lock fa-3x mb-2"></i>
                                <p>Selesaikan semua materi TWK terlebih dahulu</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h4 class="mb-2">Latihan TWK</h4>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $twkLatihanCompleted ? '100' : '0' }}%" aria-valuenow="{{ $twkLatihanCompleted ? '100' : '0' }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="progress-text d-flex justify-content-between">
                            <span>Progress: {{ $twkLatihanCompleted ? '1' : '0' }}/1</span>
                            <span>{{ $twkLatihanCompleted ? '100' : '0' }}%</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ count($twkLatihan) }} Soal</span></p>
                            <p class="mb-0">Status<span class="text-dark fw-bold"> {{ $twkLatihanCompleted ? 'Selesai' : 'Belum Selesai' }}</span></p>
                        </div>
                    </div>
                    @if($allTWKCompleted && count($twkLatihan) > 0)
                        <a href="{{ route('tryout.index', $twkLatihan->first()->id) }}" class="btn btn-primary btn-rounded">Kerjakan</a>
                    @else
                        <button disabled class="btn btn-secondary btn-rounded">Kerjakan</button>
                    @endif
                </div>
            </div>
            
            <!-- Materi TIU Card -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                <div class="blog-item bg-light rounded p-4">
                    @if(!$tiuUnlocked)
                        <div class="locked-overlay">
                            <div class="text-center text-white">
                                <i class="fas fa-lock fa-3x mb-2"></i>
                                <p>Selesaikan materi dan latihan TWK terlebih dahulu</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h4 class="mb-2">Materi TIU</h4>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $tiuPercentage }}%" aria-valuenow="{{ $tiuPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="progress-text d-flex justify-content-between">
                            <span>Progress: {{ $completedTIUMaterials }}/{{ $totalTIUMaterials }}</span>
                            <span>{{ number_format($tiuPercentage, 0) }}%</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ $totalTIUMaterials }} Materi</span></p>
                            <p class="mb-0">Status<span class="text-dark fw-bold"> {{ $tiuStatus }}</span></p>
                        </div>
                    </div>
                    @if($tiuUnlocked && !empty($tiuMaterials->first()))
                        <a href="{{ route('materi-belajar.show', $tiuMaterials->first()->first()->id) }}" class="btn btn-primary btn-rounded">Lihat Materi</a>
                    @else
                        <button disabled class="btn btn-secondary btn-rounded">Lihat Materi</button>
                    @endif
                </div>
            </div>
            
            <!-- Latihan TIU Card -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="blog-item bg-light rounded p-4">
                    @if(!$tiuUnlocked || !$allTIUCompleted)
                        <div class="locked-overlay">
                            <div class="text-center text-white">
                                <i class="fas fa-lock fa-3x mb-2"></i>
                                <p>Selesaikan semua materi TIU terlebih dahulu</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h4 class="mb-2">Latihan TIU</h4>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $tiuLatihanCompleted ? '100' : '0' }}%" aria-valuenow="{{ $tiuLatihanCompleted ? '100' : '0' }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="progress-text d-flex justify-content-between">
                            <span>Progress: {{ $tiuLatihanCompleted ? '1' : '0' }}/1</span>
                            <span>{{ $tiuLatihanCompleted ? '100' : '0' }}%</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ count($tiuLatihan) }} Soal</span></p>
                            <p class="mb-0">Status<span class="text-dark fw-bold"> {{ $tiuLatihanCompleted ? 'Selesai' : 'Belum Selesai' }}</span></p>
                        </div>
                    </div>
                    @if($tiuUnlocked && $allTIUCompleted && count($tiuLatihan) > 0)
                        <a href="{{ route('tryout.index', $tiuLatihan->first()->id) }}" class="btn btn-primary btn-rounded">Kerjakan</a>
                    @else
                        <button disabled class="btn btn-secondary btn-rounded">Kerjakan</button>
                    @endif
                </div>
            </div>
            
            <!-- Materi TKP Card -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="blog-item bg-light rounded p-4">
                    @if(!$tkpUnlocked)
                        <div class="locked-overlay">
                            <div class="text-center text-white">
                                <i class="fas fa-lock fa-3x mb-2"></i>
                                <p>Selesaikan materi dan latihan TIU terlebih dahulu</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h4 class="mb-2">Materi TKP</h4>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $tkpPercentage }}%" aria-valuenow="{{ $tkpPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="progress-text d-flex justify-content-between">
                            <span>Progress: {{ $completedTKPMaterials }}/{{ $totalTKPMaterials }}</span>
                            <span>{{ number_format($tkpPercentage, 0) }}%</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ $totalTKPMaterials }} Materi</span></p>
                            <p class="mb-0">Status<span class="text-dark fw-bold"> {{ $tkpStatus }}</span></p>
                        </div>
                    </div>
                    @if($tkpUnlocked && !empty($tkpMaterials->first()))
                        <a href="{{ route('materi-belajar.show', $tkpMaterials->first()->first()->id) }}" class="btn btn-primary btn-rounded">Lihat Materi</a>
                    @else
                        <button disabled class="btn btn-secondary btn-rounded">Lihat Materi</button>
                    @endif
                </div>
            </div>
            
            <!-- Latihan TKP Card -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                <div class="blog-item bg-light rounded p-4">
                    @if(!$tkpUnlocked || !$allTKPCompleted)
                        <div class="locked-overlay">
                            <div class="text-center text-white">
                                <i class="fas fa-lock fa-3x mb-2"></i>
                                <p>Selesaikan semua materi TKP terlebih dahulu</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h4 class="mb-2">Latihan TKP</h4>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $tkpLatihanCompleted ? '100' : '0' }}%" aria-valuenow="{{ $tkpLatihanCompleted ? '100' : '0' }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="progress-text d-flex justify-content-between">
                            <span>Progress: {{ $tkpLatihanCompleted ? '1' : '0' }}/1</span>
                            <span>{{ $tkpLatihanCompleted ? '100' : '0' }}%</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <p class="mb-0">Jumlah<span class="text-dark fw-bold"> {{ count($tkpLatihan) }} Soal</span></p>
                            <p class="mb-0">Status<span class="text-dark fw-bold"> {{ $tkpLatihanCompleted ? 'Selesai' : 'Belum Selesai' }}</span></p>
                        </div>
                    </div>
                    @if($tkpUnlocked && $allTKPCompleted && count($tkpLatihan) > 0)
                        <a href="{{ route('tryout.index', $tkpLatihan->first()->id) }}" class="btn btn-primary btn-rounded">Kerjakan</a>
                    @else
                        <button disabled class="btn btn-secondary btn-rounded">Kerjakan</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('after-script')
<script>
    // Script lainnya...
    
    // SweetAlert untuk pengguna yang mencoba akses tryout tanpa menyelesaikan materi
    @if(session('sweetAlert'))
        Swal.fire({
            title: "{{ session('title') }}",
            text: "{{ session('text') }}",
            icon: "{{ session('type') }}",
            showCancelButton: false,
            confirmButtonText: "Oke",
        }).then((result) => {
            if (result.isConfirmed) {
                // Akan tetap di halaman materi
                // Kita bisa scroll ke bagian materi yang belum selesai jika perlu
                $('.nav-tabs a[href="#twk"]').tab('show'); // Misalnya, selalu tampilkan tab TWK
            }
        });
    @endif
</script>
<script>
    $(document).ready(function() {
    @if(session('swal_msg'))
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    Toast.fire({
        icon: "{{ session('swal_type') ?? 'info' }}",
        title: "{{ session('swal_msg') }}"
    });
@endif
    });
</script>
@endpush