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
        
        .custom-tabs .nav-item .nav-link {
            color: #555;
            background-color: #f8f9fa;
            border-radius: 5px 5px 0 0;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .custom-tabs .nav-item .nav-link.active {
            color: #fff;
            background-color: #4e73df;
        }
        
        .kategori-heading {
            background-color: #e8f4ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid #4e73df;
        }
        
        .materi-card {
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .materi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .completed-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #1cc88a;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
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
        }
        
        .progress-container {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 5px;
            background-color: #4e73df;
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

<div class="container-fluid py-5">
    <div class="container">
        <!-- Tabs untuk kategori -->
        <ul class="nav nav-tabs custom-tabs mb-4" id="kategoriTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="twk-tab" data-bs-toggle="tab" data-bs-target="#twk" type="button" role="tab" aria-controls="twk" aria-selected="true">TWK</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tiu-tab" data-bs-toggle="tab" data-bs-target="#tiu" type="button" role="tab" aria-controls="tiu" aria-selected="false">TIU</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tkp-tab" data-bs-toggle="tab" data-bs-target="#tkp" type="button" role="tab" aria-controls="tkp" aria-selected="false">TKP</button>
            </li>
        </ul>
        
        <!-- Konten tabs -->
        <div class="tab-content" id="kategoriTabContent">
            <!-- TWK Tab -->
            <div class="tab-pane fade show active" id="twk" role="tabpanel" aria-labelledby="twk-tab">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="kategori-heading">
                            <h2>Tes Wawasan Kebangsaan (TWK)</h2>
                            <p class="mb-0">Materi dan latihan untuk mempersiapkan tes TWK</p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress bar -->
                @php
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
                    
                    $twkProgress = $totalTWKMaterials > 0 ? ($completedTWKMaterials / $totalTWKMaterials) * 100 : 0;
                @endphp
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Progress TWK</h5>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: {{ $twkProgress }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small>{{ $completedTWKMaterials }} dari {{ $totalTWKMaterials }} materi selesai</small>
                                    <small>{{ number_format($twkProgress, 0) }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loop through TWK materials -->
                @foreach($twkMaterials as $tipe => $materis)
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4>{{ $tipe }}</h4>
                    </div>
                    
                    @foreach($materis as $materi)
                    <div class="col-md-4">
                        <div class="card shadow-sm materi-card h-100">
                            @if(isset($userProgress[$materi->id]))
                                <div class="completed-badge">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ $materi->title }}</h5>
                                <p class="card-text">{{ Str::limit(strip_tags($materi->content), 100) }}</p>
                                <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-primary">Baca Materi</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
                
                <!-- Latihan TWK -->
                @if(count($twkLatihan) > 0)
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4>Latihan TWK</h4>
                    </div>
                    
                    @php
                        $allTWKMaterialsCompleted = ($totalTWKMaterials > 0) && ($completedTWKMaterials >= $totalTWKMaterials);
                    @endphp
                    
                    @foreach($twkLatihan as $latihan)
                    <div class="col-md-4">
                        <div class="card shadow-sm materi-card h-100 position-relative">
                            @if(!$allTWKMaterialsCompleted)
                                <div class="locked-overlay">
                                    <div class="text-center text-white">
                                        <i class="fas fa-lock fa-3x mb-2"></i>
                                        <p>Selesaikan semua materi TWK terlebih dahulu</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ $latihan->title }}</h5>
                                <p class="card-text">Latihan soal untuk menguji pemahaman materi TWK</p>
                                @if($allTWKMaterialsCompleted)
                                    <a href="{{ route('tryout.index', $latihan->id) }}" class="btn btn-success">Kerjakan Latihan</a>
                                @else
                                    <button class="btn btn-secondary" disabled>Kerjakan Latihan</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            <!-- TIU Tab -->
            <div class="tab-pane fade" id="tiu" role="tabpanel" aria-labelledby="tiu-tab">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="kategori-heading">
                            <h2>Tes Intelegensi Umum (TIU)</h2>
                            <p class="mb-0">Materi dan latihan untuk mempersiapkan tes TIU</p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress bar -->
                @php
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
                    
                    $tiuProgress = $totalTIUMaterials > 0 ? ($completedTIUMaterials / $totalTIUMaterials) * 100 : 0;
                    
                    // Cek apakah semua materi TWK sudah selesai
                    $allTWKCompleted = ($totalTWKMaterials > 0) && ($completedTWKMaterials >= $totalTWKMaterials);
                    
                    // Cek apakah latihan TWK sudah dikerjakan
                    $twkLatihanCompleted = count($twkLatihan) > 0 && count(array_intersect_key(array_flip($userTryoutProgress ?? []), $twkLatihan->pluck('id')->flip()->toArray())) > 0;
                    
                    // TIU di-unlock jika TWK selesai dan latihan TWK sudah dikerjakan
                    $tiuUnlocked = $allTWKCompleted && $twkLatihanCompleted;
                @endphp
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">

                                <h5 class="card-title">Progress TIU</h5>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: {{ $tiuProgress }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small>{{ $completedTIUMaterials }} dari {{ $totalTIUMaterials }} materi selesai</small>
                                    <small>{{ number_format($tiuProgress, 0) }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(!$tiuUnlocked)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-lock me-2"></i> Anda harus menyelesaikan semua materi TWK dan latihan TWK terlebih dahulu.
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Loop through TIU materials -->
                @foreach($tiuMaterials as $tipe => $materis)
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4>{{ $tipe }}</h4>
                    </div>
                    
                    @foreach($materis as $materi)
                    <div class="col-md-4">
                        <div class="card shadow-sm materi-card h-100 position-relative">
                            @if(!$tiuUnlocked)
                                <div class="locked-overlay">
                                    <div class="text-center text-white">
                                        <i class="fas fa-lock fa-3x mb-2"></i>
                                        <p>Selesaikan semua materi dan latihan TWK terlebih dahulu</p>
                                    </div>
                                </div>
                            @elseif(isset($userProgress[$materi->id]))
                                <div class="completed-badge">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ $materi->title }}</h5>
                                <p class="card-text">{{ Str::limit(strip_tags($materi->content), 100) }}</p>
                                @if($tiuUnlocked)
                                    <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-primary">Baca Materi</a>
                                @else
                                    <button class="btn btn-secondary" disabled>Baca Materi</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
                
                <!-- Latihan TIU -->
                @if(count($tiuLatihan) > 0)
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4>Latihan TIU</h4>
                    </div>
                    
                    @php
                        $allTIUMaterialsCompleted = $tiuUnlocked && ($totalTIUMaterials > 0) && ($completedTIUMaterials >= $totalTIUMaterials);
                    @endphp
                    
                    @foreach($tiuLatihan as $latihan)
                    <div class="col-md-4">
                        <div class="card shadow-sm materi-card h-100 position-relative">
                            @if(!$allTIUMaterialsCompleted)
                                <div class="locked-overlay">
                                    <div class="text-center text-white">
                                        <i class="fas fa-lock fa-3x mb-2"></i>
                                        <p>Selesaikan semua materi TIU terlebih dahulu</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ $latihan->title }}</h5>
                                <p class="card-text">Latihan soal untuk menguji pemahaman materi TIU</p>
                                @if($allTIUMaterialsCompleted)
                                    <a href="{{ route('tryout.index', $latihan->id) }}" class="btn btn-success">Kerjakan Latihan</a>
                                @else
                                    <button class="btn btn-secondary" disabled>Kerjakan Latihan</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            <!-- TKP Tab -->
            <div class="tab-pane fade" id="tkp" role="tabpanel" aria-labelledby="tkp-tab">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="kategori-heading">
                            <h2>Tes Karakteristik Pribadi (TKP)</h2>
                            <p class="mb-0">Materi dan latihan untuk mempersiapkan tes TKP</p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress bar -->
                @php
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
                    
                    $tkpProgress = $totalTKPMaterials > 0 ? ($completedTKPMaterials / $totalTKPMaterials) * 100 : 0;
                    
                    // Cek apakah semua materi TIU sudah selesai
                    $allTIUCompleted = ($totalTIUMaterials > 0) && ($completedTIUMaterials >= $totalTIUMaterials);
                    
                    // Cek apakah latihan TIU sudah dikerjakan
                    $tiuLatihanCompleted = count($tiuLatihan) > 0 && count(array_intersect_key(array_flip($userTryoutProgress ?? []), $tiuLatihan->pluck('id')->flip()->toArray())) > 0;
                    
                    // TKP di-unlock jika TIU selesai dan latihan TIU sudah dikerjakan
                    $tkpUnlocked = $tiuUnlocked && $allTIUCompleted && $tiuLatihanCompleted;
                @endphp
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Progress TKP</h5>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: {{ $tkpProgress }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small>{{ $completedTKPMaterials }} dari {{ $totalTKPMaterials }} materi selesai</small>
                                    <small>{{ number_format($tkpProgress, 0) }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(!$tkpUnlocked)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-lock me-2"></i> Anda harus menyelesaikan semua materi TIU dan latihan TIU terlebih dahulu.
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Loop through TKP materials -->
                @foreach($tkpMaterials as $tipe => $materis)
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4>{{ $tipe }}</h4>
                    </div>
                    
                    @foreach($materis as $materi)
                    <div class="col-md-4">
                        <div class="card shadow-sm materi-card h-100 position-relative">
                            @if(!$tkpUnlocked)
                                <div class="locked-overlay">
                                    <div class="text-center text-white">
                                        <i class="fas fa-lock fa-3x mb-2"></i>
                                        <p>Selesaikan semua materi dan latihan TIU terlebih dahulu</p>
                                    </div>
                                </div>
                            @elseif(isset($userProgress[$materi->id]))
                                <div class="completed-badge">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ $materi->title }}</h5>
                                <p class="card-text">{{ Str::limit(strip_tags($materi->content), 100) }}</p>
                                @if($tkpUnlocked)
                                    <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-primary">Baca Materi</a>
                                @else
                                    <button class="btn btn-secondary" disabled>Baca Materi</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
                
                <!-- Latihan TKP -->
                @if(count($tkpLatihan) > 0)
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4>Latihan TKP</h4>
                    </div>
                    
                    @php
                        $allTKPMaterialsCompleted = $tkpUnlocked && ($totalTKPMaterials > 0) && ($completedTKPMaterials >= $totalTKPMaterials);
                    @endphp
                    
                    @foreach($tkpLatihan as $latihan)
                    <div class="col-md-4">
                        <div class="card shadow-sm materi-card h-100 position-relative">
                            @if(!$allTKPMaterialsCompleted)
                                <div class="locked-overlay">
                                    <div class="text-center text-white">
                                        <i class="fas fa-lock fa-3x mb-2"></i>
                                        <p>Selesaikan semua materi TKP terlebih dahulu</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ $latihan->title }}</h5>
                                <p class="card-text">Latihan soal untuk menguji pemahaman materi TKP</p>
                                @if($allTKPMaterialsCompleted)
                                    <a href="{{ route('tryout.index', $latihan->id) }}" class="btn btn-success">Kerjakan Latihan</a>
                                @else
                                    <button class="btn btn-secondary" disabled>Kerjakan Latihan</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                
                <!-- Tryout -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <div class="alert alert-primary">
                            <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Tryout CPNS</h4>
                            <p class="mb-0">
                                @php
                                    $allMaterialsCompleted = 
                                        ($totalTWKMaterials > 0) && ($completedTWKMaterials >= $totalTWKMaterials) &&
                                        ($totalTIUMaterials > 0) && ($completedTIUMaterials >= $totalTIUMaterials) &&
                                        ($totalTKPMaterials > 0) && ($completedTKPMaterials >= $totalTKPMaterials);
                                        
                                    $allLatihanCompleted = 
                                        count($twkLatihan) > 0 && count(array_intersect_key(array_flip($userTryoutProgress ?? []), $twkLatihan->pluck('id')->flip()->toArray())) > 0 &&
                                        count($tiuLatihan) > 0 && count(array_intersect_key(array_flip($userTryoutProgress ?? []), $tiuLatihan->pluck('id')->flip()->toArray())) > 0 &&
                                        count($tkpLatihan) > 0 && count(array_intersect_key(array_flip($userTryoutProgress ?? []), $tkpLatihan->pluck('id')->flip()->toArray())) > 0;
                                @endphp
                                
                                @if($allMaterialsCompleted && $allLatihanCompleted)
                                    Selamat! Anda telah menyelesaikan semua materi dan latihan. Anda sekarang bisa mengakses Tryout CPNS.
                                    <div class="mt-3">
                                        <a href="{{ url('/tryout') }}" class="btn btn-success">Akses Tryout CPNS</a>
                                    </div>
                                    
                                    @php
                                        // Update user access
                                        if(Auth::check() && !Auth::user()->is_akses) {
                                            Auth::user()->update(['is_akses' => true]);
                                        }
                                    @endphp
                                @else
                                    Untuk mengakses Tryout CPNS, Anda harus menyelesaikan semua materi dan latihan dari ketiga kategori (TWK, TIU, dan TKP).
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script>
    // Start with the correct tab based on progress
    $(document).ready(function() {
        // Function to find next incomplete section
        const findNextIncompleteSection = () => {
            const twkCompleted = {{ $twkProgress }} >= 100 && {{ count($twkLatihan) > 0 ? (count(array_intersect_key(array_flip($userTryoutProgress ?? []), $twkLatihan->pluck('id')->flip()->toArray())) > 0 ? 'true' : 'false') : 'true' }};
            
            if (!twkCompleted) {
                return 'twk';
            }
            
            const tiuCompleted = {{ $tiuProgress }} >= 100 && {{ count($tiuLatihan) > 0 ? (count(array_intersect_key(array_flip($userTryoutProgress ?? []), $tiuLatihan->pluck('id')->flip()->toArray())) > 0 ? 'true' : 'false') : 'true' }};
            
            if (!tiuCompleted) {
                return 'tiu';
            }
            
            return 'tkp';
        };
        
        // If user has started but not completed all sections
        const activeSection = findNextIncompleteSection();
        
        // Activate the appropriate tab
        $('#kategoriTab button[data-bs-target="#' + activeSection + '"]').tab('show');
    });
</script>
@endpush