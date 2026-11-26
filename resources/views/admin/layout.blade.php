<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SaigonBite Admin')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('css/catalog-admin.css') }}" rel="stylesheet">
  <link href="{{ asset('css/account-corner.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body class="admin-body">
  <header class="admin-header">
    <a class="admin-logo" href="{{ route('admin.dashboard') }}"><span>Saigon</span><b>Bite</b></a>
    <nav class="admin-nav" aria-label="Main navigation">
      <a class="{{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brand &amp; Menu</a>
    </nav>
    @include('partials.account-corner')
  </header>
  <main class="admin-main">
    @yield('content')
  </main>
  <div class="toast-stack" id="toast-stack" aria-live="polite" aria-atomic="true"></div>
  <div class="modal fade" id="detail-modal" tabindex="-1" aria-labelledby="detail-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
      <div class="modal-header"><h2 class="modal-title" id="detail-modal-title">Details</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body" id="detail-modal-body"></div>
      <div class="modal-footer"><button type="button" class="button secondary" data-bs-dismiss="modal">Close</button></div>
    </div></div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
