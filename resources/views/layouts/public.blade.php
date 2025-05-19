<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="google-site-verification" content="4SoLVqo4XwjY5pmJ08v4M4UMYM91pD51HSzq7JQDlCI" />
  <meta charset="utf-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link rel="shortcut icon" sizes="16x16 32x32 48x48" href="{{asset('assets/img/lulus-bersama-logo.png')}}" />
  <meta name="description"
  content="Ikuti jenius CPNS tryout CPNS secara gratis atau berbayar untuk mempersiapkan dirimu menghadapi TES SKD CASN dengan percaya diri." />
<meta name="keywords" 
  content="Jenius CPNS, tryout cpns, TES SKD CASN, Tryout CPNS TES SKD CASN, jenius CPNS tryout cpns" />
<meta name="author" content="Jenius CPNS" />

<meta property="og:title" content="Tryout CPNS TES SKD CASN Gratis - Jenius CPNS">
<meta property="og:description" content="Ikuti jenius CPNS tryout CPNS secara gratis atau berbayar untuk mempersiapkan dirimu menghadapi TES SKD CASN dengan percaya diri.">
<meta property="og:image" content="{{asset('assets/img/lulus-bersama-logo.png')}}">
<meta property="og:url" content="https://jeniuscpns.com">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Tryout CPNS TES SKD CASN Gratis - Jenius CPNS">
<meta name="twitter:description" content="Ikuti jenius CPNS tryout CPNS secara gratis atau berbayar untuk mempersiapkan dirimu menghadapi TES SKD CASN dengan percaya diri.">
<meta name="twitter:image" content="{{asset('assets/img/lulus-bersama-logo.png')}}">
<link rel="canonical" href="https://jeniuscpns.com">


  <title>@yield('title')</title>

  @include('includes.public.style')
  @stack('after-style')
</head>

<body>

  
  <!-- start navbar -->
  @include('includes.public.navbar')
  <!-- end navbar -->

  <!-- start body -->
  @yield('content')
  <!-- end body -->

  @include('includes.public.script')
  @stack('after-script')
</body>

</html>