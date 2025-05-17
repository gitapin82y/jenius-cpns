
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
          <span class="sr-only">Loading...</span>
      </div>
  </div>

        <!-- Topbar Start -->
        <div class="container-fluid topbar px-0 d-none d-lg-block">
          <div class="container px-0">
              <div class="row gx-0 align-items-center" style="height: 45px;">
                  <div class="col-lg-8 text-center text-lg-start mb-lg-0">
                      <div class="d-flex flex-wrap">
                          {{-- <a href="#" class="text-white me-4"><i class="fas fa-map-marker-alt text-primary me-2"></i>Find A Location</a> --}}
                          <a href="#" class="text-white me-4"><i class="fas fa-phone-alt text-primary me-2"></i>081231548925</a>
                          <a href="#" class="text-white me-0"><i class="fas fa-envelope text-primary me-2"></i>admin@jeniuscpns.com</a>
                      </div>
                  </div>
                  <div class="col-lg-4 text-center text-lg-end">
                      <div class="d-flex align-items-center justify-content-end">
                          <a href="https://www.instagram.com/jeniuscpns?igsh=MXcwdGpxazBrdHh6Yg==" target="_blank" class="btn btn-primary btn-square rounded-circle nav-fill me-3"><i class="fab fa-instagram text-white"></i></a>
                          {{-- <a href="#" class="btn btn-primary btn-square rounded-circle nav-fill me-3"><i class="fab fa-facebook-f text-white"></i></a> --}}
                          {{-- <a href="#" class="btn btn-primary btn-square rounded-circle nav-fill me-3"><i class="fab fa-twitter text-white"></i></a> --}}
                          {{-- <a href="#" class="btn btn-primary btn-square rounded-circle nav-fill me-0"><i class="fab fa-linkedin-in text-white"></i></a> --}}
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <!-- Topbar End -->


      <!-- Navbar & Hero Start -->
      <div class="container-fluid sticky-top px-0">
          <div class="position-absolute bg-dark" style="left: 0; top: 0; width: 100%; height: 100%;">
          </div>
          <div class="container px-0">
              <nav class="navbar navbar-expand-lg navbar-dark bg-white py-3 px-4">
                  <a href="{{url('/')}}" class="navbar-brand p-0">
                      {{-- <h3 class="text-primary m-0"><i class="fas fa-donate me-3"></i>Jenius CPNS</h4> --}}
                      <!-- <img src="img/logo.png" alt="Logo"> -->
                      <img src="{{asset('assets/img/lulus-bersama-logo.png')}}" alt="logo jenius CPNS" width="60px">
                  </a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                      <span class="fa fa-bars"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarCollapse">
                      <div class="navbar-nav ms-auto py-0">
                          <a href="{{ url('/') }}" class="nav-item nav-link {{Request::is('/') ? 'active' : ''}}">Beranda</a>
                          <a href="{{ url('/#tentang') }}" class="nav-item nav-link">Tentang Kami</a>
                          <a href="{{ url('/kontak') }}" class="nav-item nav-link {{Request::is('kontak') ? 'active' : ''}}">Kontak</a>
                          @auth
                          <a href="{{ url('/tryout') }}" class="nav-item nav-link {{Request::is('tryout') ? 'active' : ''}}">Tryout SKD</a>
                          @endauth
                      </div>
                      <div class="d-flex align-items-center flex-nowrap pt-xl-0">
                        @auth
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">{{auth()->user()->name}}</a>
                            <div class="dropdown-menu m-0">
                                @if(auth()->user()->is_admin)
                                <a href="{{ url('/dashboard') }}" class="dropdown-item">Dashboard</a>
                            @endif
                                <a href="{{url('/logout')}}" class="dropdown-item">Logout</a>
                            </div>
                        </div>
                        @else
                        <a href="{{ url('/login') }}" class="btn btn-primary rounded-pill text-white py-2 px-4 ms-2 flex-wrap flex-sm-shrink-0">Login</a>
                        @endauth
                      </div>
                  </div>
              </nav>
          </div>
      </div>
      <!-- Navbar & Hero End -->