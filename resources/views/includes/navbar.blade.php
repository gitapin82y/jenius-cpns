<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <a class="navbar-nav d-none d-md-flex" href="{{url('/')}}">
            <img src="{{asset('assets/img/logo-jenius-cpns.png')}}" alt="logo jenius CPNS" width="80px">
    </a>

    <div class="navbar-nav ml-auto d-none d-md-flex nav-dashboard">
        <a href="{{ url('dashboard') }}" class="nav-item nav-link {{ Request::is('dashboard') ? 'active' : '' }}">Dashboard</a>
        <div class="topbar-divider d-none d-sm-block mx-0"></div>
        <a href="{{ url('pengguna') }}" class="nav-item nav-link {{ Request::is('pengguna') ? 'active' : '' }}">Pengguna</a>
        <div class="topbar-divider d-none d-sm-block mx-0"></div>
        <a href="{{ url('materi') }}" class="nav-item nav-link {{ Request::is('materi') || Request::is('materi/*') ? 'active' : '' }}">Materi</a>
        <div class="topbar-divider d-none d-sm-block mx-0"></div>
        <a href="{{ url('setsoal') }}" class="nav-item nav-link {{ Request::is('setsoal') || Request::is('soal/*') ? 'active' : '' }}">Tryout SKD</a>
        <div class="topbar-divider d-none d-sm-block mx-0"></div>
        <a href="{{ url('system-error') }}" class="nav-item nav-link {{ Request::is('system-error') ? 'active' : '' }}">Laporan Sistem</a>
        {{-- <div class="topbar-divider d-none d-sm-block mx-0"></div> --}}
         {{-- <a href="{{ route('admin.keyword-update') }}" class="nav-item nav-link {{ Request::is('admin/keyword-update') ? 'active' : '' }}">Kata Kunci</a> --}}
        <div class="topbar-divider d-none d-sm-block mx-0"></div>        
         <a href="{{ route('admin.cbf-evaluation.dashboard') }}" class="nav-item nav-link {{ Request::is('admin/cbf-evaluation') ? 'active' : '' }}">CBF Evaluation</a>
        <div class="topbar-divider d-none d-sm-block mx-0"></div>  
           <a href="{{ route('admin.automatic-cbf-evaluation.dashboard') }}" class="nav-item nav-link {{ Request::is('admin/automatic-cbf-evaluation') ? 'active' : '' }}">CBF Evaluation</a>
        <div class="topbar-divider d-none d-sm-block mx-0"></div>  
    </div>


    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{auth()->user()->name}}</span>
                <img class="img-profile rounded-circle"
                    src="{{asset('img/undraw_profile.svg')}}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{url('/logout')}}">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>