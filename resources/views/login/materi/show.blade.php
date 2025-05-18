@extends('layouts.public')

@section('title', $material->title)

@push('after-style')
    <style>
        .materi-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        
        .materi-heading {
            border-bottom: 2px solid #4e73df;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .materi-content {
            font-size: 16px;
            line-height: 1.8;
        }
        
        .materi-nav {
            position: sticky;
            top: 20px;
            padding: 20px;
            background-color: #f8f9fc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .materi-nav-item {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .materi-nav-item:hover {
            background-color: #eaecf4;
        }
        
        .materi-nav-item.active {
            background-color: #4e73df;
            color: white;
        }
        
        .materi-nav-item.completed {
            background-color: #1cc88a;
            color: white;
        }
        
        .mark-completed-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
        }
        
        .selesai-membaca-btn {
            margin-top: 30px;
            text-align: center;
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
                    <h5 class="mb-3">Materi {{ $material->tipe }}</h5>
                    <ul class="list-unstyled">
                        @foreach($relatedMaterials as $relatedMaterial)
                            <li>
                                <a href="{{ route('materi.show', $relatedMaterial->id) }}" class="d-block text-decoration-none materi-nav-item {{ $material->id == $relatedMaterial->id ? 'active' : (isset($completedMaterials[$relatedMaterial->id]) ? 'completed' : '') }}">
                                    @if(isset($completedMaterials[$relatedMaterial->id]))
                                        <i class="fas fa-check-circle me-2"></i>
                                    @endif
                                    {{ $relatedMaterial->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    
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