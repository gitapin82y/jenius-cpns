<ul class="navbar-nav bg-primary d-block d-md-none sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{url('/')}}">
        <img src="{{asset('assets/img/lulus-bersama-logo.png')}}" alt="logo jenius CPNS" width="60px">
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{Request::is('dashboard') ? 'active' : ''}}">
        <a class="nav-link" href="{{url('dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Charts -->
    <li class="nav-item {{Request::is('pengguna') ? 'active' : ''}}">
        <a class="nav-link" href="{{url('pengguna')}}">
            <i class="fas fa-fw fa-users"></i>
            <span>Pengguna</span></a>
    </li>
    <hr class="sidebar-divider my-0">
    
    <!-- Nav Item - Materi -->
    <li class="nav-item {{Request::is('materi') || Request::is('materi/*') ? 'active' : ''}}">
        <a class="nav-link" href="{{url('materi')}}">
            <i class="fas fa-fw fa-book"></i>
            <span>Materi</span></a>
    </li>
    <hr class="sidebar-divider my-0">
    
    <!-- Nav Item - Tryout -->
    <li class="nav-item {{Request::is('setsoal') || Request::is('soal/*') ? 'active' : ''}}">
        <a class="nav-link" href="{{url('setsoal')}}">
            <i class="fas fa-fw fa-pencil-alt"></i> 
            <span>Tryout SKD</span></a>
    </li>

    <hr class="sidebar-divider my-0">
    
    <!-- Nav Item - System Error -->
    <li class="nav-item {{Request::is('system-error') ? 'active' : ''}}">
        <a class="nav-link" href="{{url('system-error')}}">
            <i class="fas fa-fw fa-exclamation-triangle"></i>
            <span>Laporan Sistem</span></a>
    </li>
    <hr class="sidebar-divider my-0">

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>