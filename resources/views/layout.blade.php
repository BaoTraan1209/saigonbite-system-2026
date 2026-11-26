<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','SaigonBite')</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/saigonbite.css') }}" rel="stylesheet"><link href="{{ asset('css/account-corner.css') }}" rel="stylesheet">@stack('styles')</head>
<body>
<header class="site-header"><div class="container header-inner">
<a class="logo" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('order.start') }}">Saigon<span>Bite</span></a>
<nav><a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('order.start') }}">Home</a>@if(auth()->user()->role === 'customer')<a class="active" href="{{ route('order.start') }}">Order</a>@endif @if(auth()->user()->role === 'admin')<a href="{{ route('admin.dashboard') }}">Brand &amp; Menu</a>@endif</nav>@include('partials.account-corner')
<div class="city">● &nbsp;HO CHI MINH CITY</div><button class="btn mobile-toggle" data-bs-toggle="collapse" data-bs-target="#mobileNav">☰</button>
</div><div class="collapse mobile-nav" id="mobileNav"><a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('order.start') }}">Home</a>@if(auth()->user()->role === 'customer')<a href="{{ route('order.start') }}">Order</a>@endif @if(auth()->user()->role === 'admin')<a href="{{ route('admin.dashboard') }}">Brand &amp; Menu</a>@endif</div></header>
<main class="container page">@yield('content')</main>
<footer class="container footer"><span>A little Saigon in every bite.</span><span>Need a hand? <a href="mailto:support@saigonbite.example">Contact support</a></span></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script type="module" src="{{ asset('js/saigonbite.js') }}"></script>@stack('scripts')
</body></html>
