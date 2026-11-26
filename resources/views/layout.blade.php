<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','SaigonBite')</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/saigonbite.css') }}" rel="stylesheet">@stack('styles')</head>
<body>
<header class="site-header"><div class="container header-inner">
<a class="logo" href="{{ route('order.start') }}">Saigon<span>Bite</span></a>
<nav><a href="{{ route('order.start') }}">Home</a><a class="active" href="{{ route('order.start') }}">Order</a><a href="#" onclick="alert('My Orders will be connected to the account API.');return false">My Orders</a><a href="#" onclick="alert('Profile will be connected to the account API.');return false">Profile</a></nav>
<div class="city">● &nbsp;HO CHI MINH CITY</div><button class="btn mobile-toggle" data-bs-toggle="collapse" data-bs-target="#mobileNav">☰</button>
</div><div class="collapse mobile-nav" id="mobileNav"><a href="{{ route('order.start') }}">Home</a><a href="{{ route('order.start') }}">Order</a><a href="#">My Orders</a><a href="#">Profile</a></div></header>
<main class="container page">@yield('content')</main>
<footer class="container footer"><span>A little Saigon in every bite.</span><span>Need a hand? <a href="mailto:support@saigonbite.example">Contact support</a></span></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script type="module" src="{{ asset('js/saigonbite.js') }}"></script>@stack('scripts')
</body></html>
