<ul class="navbar-nav bg-primary d-block d-md-none sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{url('/')}}">
        {{-- <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Lulus Bersama</div> --}}
        <img src="{{asset('assets/img/lulus-bersama-logo.png')}}" alt="logo lulus bersama" width="60px">
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
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Pengguna</span></a>
    </li>
    <hr class="sidebar-divider my-0">
       <!-- Nav Item - Charts -->
       <li class="nav-item {{Request::is('setsoal') || Request::is('soal/*') ? 'active' : ''}}">
        <a class="nav-link" href="{{url('setsoal')}}">
            <i class="fas fa-pencil-alt"></i> 
            <span>Tryout SKD</span></a>
        </li>

        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Charts -->
        <li class="nav-item {{Request::is('skb/setsoal') || Request::is('skb/soal/*') ? 'active' : ''}}">
         <a class="nav-link" href="{{url('setsoal')}}">
            <i class="fas fa-file-alt"></i>
             <span>Tryout SKB</span></a>
         </li>

    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Pages Collapse Menu -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Charts -->
    <li class="nav-item {{Request::is('transaksi') ? 'active' : ''}}">
        <a class="nav-link" href="{{url('transaksi')}}">
            <i class="fas fa-credit-card"></i>
            <span>Transaksi</span></a>
    </li>
    <hr class="sidebar-divider my-0">

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

  

</ul>