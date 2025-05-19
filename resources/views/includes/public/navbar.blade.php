<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
          <span class="sr-only">Loading...</span>
      </div>
  </div>


      <!-- Navbar & Hero Start -->
      <div class="container-fluid sticky-top px-0">
          <div class="container px-0">
              <nav class="navbar navbar-expand-lg navbar-dark bg-white py-3 px-4">
                  <a href="{{url('/')}}" class="navbar-brand p-0">
                      <img src="{{asset('assets/img/lulus-bersama-logo.png')}}" alt="logo jenius CPNS" width="60px">
                  </a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                      <span class="fa fa-bars"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarCollapse">
                      <div class="navbar-nav ms-auto py-0">
                          <a href="{{ url('/') }}" class="nav-item nav-link {{Request::is('/') ? 'active' : ''}}">Beranda</a>
                          <a href="{{ url('/materi-belajar') }}" class="nav-item nav-link {{Request::is('materi-belajar*') ? 'active' : ''}}">Materi</a>
                          <a href="{{ url('/tryout') }}" class="nav-item nav-link {{Request::is('tryout*') ? 'active' : ''}}">Tryout SKD</a>
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
                        <a href="{{ url('/register') }}" class="btn btn-primary text-white py-2 px-4 ms-2 flex-wrap flex-sm-shrink-0">Daftar Sekarang</a>
                        @endauth
                      </div>
                  </div>
              </nav>
          </div>
      </div>