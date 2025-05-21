@extends('layouts.public')

@section('title', 'Hasil Tryout SKD')

@push('after-style')
  <style>
    .box-shadow {
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .scrollable {
      max-height: 200px;
      overflow-y: auto;
    }
  </style>
@endpush

@section('content')
<div class="container py-4">
  <div class="row g-3">

    <!-- Kiri -->
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="fw-bold">Total Poin Hasil Tryout CPNS</h5>
          <h2 class="fw-bold">400 <small class="text-muted fs-5">/ 550</small></h2>
          <div class="mb-2">
            <strong>Total Soal</strong>
            <div class="d-flex gap-3 mt-1">
              <span class="text-success">Benar 70</span>
              <span class="text-danger">Salah 10</span>
              <span class="text-muted">Kosong 20</span>
            </div>
          </div>
          <button class="btn btn-primary mt-2">Lihat Pembahasan Tryout</button>
        </div>
      </div>

      <div class="card">
        <div class="card-header bg-light">
          <strong>Total Poin Per-kategori</strong>
        </div>
        <div class="card-body">
          <canvas id="chartKategori" height="150"></canvas>
           <div class="row text-center my-3">
            <div class="col-12 fw-bold">
                Hasil Nilai
            </div>
            <div class="col-12 d-flex">
                  <div class="col">TWK 90 Poin</div>
                  <div class="col">TIU 90 Poin</div>
                  <div class="col">TKP 90 Poin</div>
            </div>
             <div class="col-12 fw-bold mt-3">
                Nilai Ambang Batas
            </div>
            <div class="col-12 d-flex">
                  <div class="col">TWK 90 Poin</div>
                  <div class="col">TIU 90 Poin</div>
                  <div class="col">TKP 90 Poin</div>
            </div>
           </div>
        </div>
      </div>

    </div>

    <!-- Kanan -->
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header bg-light">
          <strong>Rekomendasi Materi Belajar</strong>
        </div>
        <div class="card-body">
          <ul class="nav nav-pills mb-2" id="pills-tab">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#">Semua</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#">TWK</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#">TIU</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#">TKP</a></li>
          </ul>
          <ol class="scroll-box">
            <li><a href="#">Nasionalisme</a></li>
            <li><a href="#">Tes Verbal</a></li>
            <li><a href="#">Deret Angka</a></li>
            <li><a href="#">Pilar Negara</a></li>
            <li><a href="#">Pancasila</a></li>
            <li><a href="#">Demokrasi</a></li>
          </ol>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-light">
          <strong>Belajar dengan Mendengarkan (Video)</strong>
        </div>
        <div class="card-body scroll-box">
          <ul class="list-unstyled">
            <li><a href="#"><strong>FOKUS TWK #2 – MATERI DAN SOAL NASIONALISME</strong></a></li>
            <li><a href="#">Mengenal Nasionalisme, Patriotisme, Cinta Tanah Air dan Bela Negara</a></li>
            <li><a href="#"><strong>FOKUS TWK #3 : MATERI DAN SOAL UUD 1945</strong></a></li>
            <li><a href="#"><strong>FOKUS TWK #3 : MATERI DAN SOAL UUD 1945</strong></a></li>
          </ul>
        </div>
      </div>

    </div>

  </div>

  <!-- Penilaian -->
  <div class="card mt-3">
    <div class="card-body">
      <h6 class="fw-bold">Penilaian Efektivitas Sistem Tryout CPNS</h6>
      <p class="mb-2">Terima kasih telah mengikuti tryout CPNS. Untuk meningkatkan kualitas sistem rekomendasi tryout, Anda dapat memberikan penilaian.</p>
      <button class="btn btn-secondary btn-disabled" disabled>Berikan Penilaian Lebih Lanjut</button>
    </div>
  </div>
</div>
@endsection

@push('after-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('chartKategori');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['TWK', 'TIU', 'TKP'],
        datasets: [{
          label: 'Nilai',
          data: [90, 110, 200],
          backgroundColor: ['#6c757d', '#0d6efd', '#198754']
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
@endpush
