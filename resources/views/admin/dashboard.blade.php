@extends('admin.layout')
@section('title', 'Brand & Menu Dashboard | SaigonBite')
@section('content')
<div class="dashboard-heading">
  <div><div class="eyebrow">SAIGONBITE ADMINISTRATION</div><h1>Brand &amp; Menu Management</h1><p>Manage restaurant brands and their menu items across the system.</p></div>
  <div class="sushi-art" aria-hidden="true"><span class="chopstick one"></span><span class="chopstick two"></span><span class="sushi">🍣</span></div>
</div>
<div class="summary-grid">
  <article class="summary-card"><div class="summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10 12 3l9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/><path d="M8 9h8"/></svg></div><div class="summary-copy"><h2>Brand Management</h2><p>Create, view, update, and delete restaurant brands.</p><div class="summary-stats"><span><b id="dashboard-brand-count">—</b> Brands</span><span><b id="dashboard-brand-items">—</b> Menu Items</span></div><a class="outline-orange" href="{{ route('admin.brands.index') }}">Manage Brands <span>→</span></a></div></article>
  <article class="summary-card"><div class="summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3v6m4-6v6M6 6h4m-2 3v12M16 3c-2 3-2 7 0 9v9m0-18v9"/></svg></div><div class="summary-copy"><h2>Menu Item Management</h2><p>Manage menu items for each brand (add, edit, delete).</p><div class="summary-stats"><span><b id="dashboard-item-count">—</b> Menu Items</span><span><b id="dashboard-item-brands">—</b> Brands</span></div><a class="outline-orange" href="{{ route('admin.items.index') }}">Manage Menu Items <span>→</span></a></div></article>
</div>
<div class="recent-grid">
  <section class="content-card"><div class="card-heading"><div><h2>Recent Brands</h2><p>Latest restaurant brands in the system</p></div><a href="{{ route('admin.brands.index') }}">View all <span>→</span></a></div><div class="recent-list" id="recent-brands"><div class="loading-row">Loading brands…</div></div></section>
  <section class="content-card"><div class="card-heading"><div><h2>Recent Menu Items</h2><p>Latest items across all brands</p></div><a href="{{ route('admin.items.index') }}">View all <span>→</span></a></div><div class="recent-list" id="recent-items"><div class="loading-row">Loading menu items…</div></div></section>
</div>
@endsection
@push('scripts')<script type="module" src="{{ asset('js/catalog-admin.js') }}"></script>@endpush
