@extends('layouts.public')

@section('title', $material->title)

@push('after-style')
<style>
    /* Existing styles... */
    
    /* Styles for the accordion navigation */
    .accordion-button {
        padding: 0.75rem 1rem;
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .accordion-button:not(.collapsed) {
        background-color: #4e73df;
        color: white;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }
    
    .list-group-item.active {
        background-color: #f8f9fa;
        border-color: rgba(0,0,0,.125);
    }
    
    .list-group-item:hover {
        background-color: #f0f0f0;
    }
    
    .materi-nav {
        position: sticky;
        top: 20px;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }
    
    /* Badge positioning on accordion */
    .subcategory-status {
        position: relative;
        z-index: 2;
    }
    
    .accordion-button::after {
        margin-left: 10px;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.35em 0.65em;
    }
    
    /* Ensure badge is visible when accordion is open */
    .accordion-button:not(.collapsed) .badge.bg-success {
        background-color: #fff !important;
        color: #28a745;
    }
    
    .accordion-button:not(.collapsed) .badge.bg-secondary {
        background-color: #fff !important;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="bg-breadcrumb-single"></div>
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">{{ $material->title }}</h4>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('public.materi.index') }}">Materi</a></li>
            <li class="breadcrumb-item active text-primary">{{ $material->title }}</li>
        </ol>
    </div>
</div>
<!-- Header End -->

@php
use App\Models\Material;
@endphp

<div class="container-fluid py-5">
    <div class="container">
        <div class="row">
            <!-- Konten Materi -->
            <div class="col-lg-8">
                <div class="materi-container">
                    <div class="materi-heading">
                        <h2>{{ $material->title }}</h2>
                        <div class="d-flex align-items-center text-muted">
                            <span class="me-3"><i class="fas fa-layer-group me-2"></i>{{ $material->kategori }}</span>
                            <span><i class="fas fa-tag me-2"></i>{{ $material->tipe }}</span>
                        </div>
                    </div>
                    <div class="materi-content">
                        {!! $material->content !!}
                    </div>
                    
                    <!-- Tombol Selesai Membaca -->
                    <div class="selesai-membaca-btn">
                        @if(!isset($userProgress) || !$userProgress->is_completed)
                            <button id="markCompletedBtn" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle me-2"></i> Selesai Membaca
                            </button>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i> Anda telah menyelesaikan materi ini
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Navigasi Materi -->
<div class="col-lg-4">
    <div class="materi-nav">
        <h5 class="mb-3">Daftar Materi {{ $material->kategori }}</h5>
        
        @php
            // Ambil semua tipe (sub-kategori) dalam kategori yang sama
            $subCategories = \App\Models\Material::where('kategori', $material->kategori)
                ->where('status', 'Publish')
                ->select('tipe')
                ->distinct()
                ->get()
                ->pluck('tipe');
                
            // Ambil semua materi untuk setiap sub-kategori
            $allMaterials = [];
            $subCategoryStatus = []; // Status untuk setiap sub-kategori
            
            foreach ($subCategories as $subCategory) {
                $materials = \App\Models\Material::where('kategori', $material->kategori)
                    ->where('tipe', $subCategory)
                    ->where('status', 'Publish')
                    ->orderBy('id')
                    ->get();
                
                $allMaterials[$subCategory] = $materials;
                
                // Hitung status sub-kategori
                $totalMaterials = count($materials);
                $completedCount = 0;
                
                foreach ($materials as $materi) {
                    if (isset($completedMaterials[$materi->id])) {
                        $completedCount++;
                    }
                }
                
                // Jika semua materi selesai, tandai sub-kategori sebagai selesai
                $subCategoryStatus[$subCategory] = ($totalMaterials > 0 && $completedCount == $totalMaterials);
            }
        @endphp
        
        <div class="accordion" id="accordionMateri">
            @foreach ($subCategories as $index => $subCategory)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $material->tipe == $subCategory ? '' : 'collapsed' }} d-flex justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $material->tipe == $subCategory ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                            <span>{{ $subCategory }}</span>
                            
                            <span class="ms-auto me-3 subcategory-status">
                                @if($subCategoryStatus[$subCategory])
                                    <span class="badge bg-success rounded-pill">Selesai</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">Belum Selesai</span>
                                @endif
                            </span>
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $material->tipe == $subCategory ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionMateri">
                        <div class="accordion-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($allMaterials[$subCategory] as $materi)
                                    <li class="list-group-item border-0 {{ $material->id == $materi->id ? 'active bg-light' : '' }}">
                                        <a href="{{ route('materi.show', $materi->id) }}" class="text-decoration-none d-flex align-items-center">
                                            @if(isset($completedMaterials[$materi->id]))
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            @else
                                                <i class="fas fa-circle me-2" style="font-size: 0.5rem; opacity: 0.5;"></i>
                                            @endif
                                            <span class="{{ $material->id == $materi->id ? 'fw-bold' : '' }}">{{ $materi->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            <a href="{{ route('public.materi.index') }}" class="btn btn-primary w-100">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Materi
            </a>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

@endsection

@push('after-script')
<script>
    $(document).ready(function() {
        // Handling mark completed
        $('#markCompletedBtn').click(function() {
            $.ajax({
                url: '{{ route("materi.mark-completed", $material->id) }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hide the completed button
                    $('#markCompletedBtn').fadeOut(function() {
                        $(this).parent().html('<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> Anda telah menyelesaikan materi ini</div>');
                    });
                    
                    // If all materials in category are completed
                    if (response.all_completed) {
                        $('#nextStepContainer').removeClass('d-none');
                        $('#nextStepBtn').removeClass('d-none');
                        
                        if (response.next_step === 'latihan') {
                            $('#nextStepMessage').html('Anda telah menyelesaikan semua materi dalam kategori ini. Anda sekarang bisa melanjutkan ke latihan soal.');
                            $('#nextStepBtn').text('Kerjakan Latihan').attr('href', response.next_url);
                        }
                    }
                    location.reload();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menandai materi selesai. Silakan coba lagi nanti.',
                    });
                }
            });
        });
    });
</script>
@endpush