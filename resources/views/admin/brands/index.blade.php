@extends('admin.layout')
@section('title', 'Brand Management | SaigonBite')
@section('content')
<div class="breadcrumb-line"><a href="{{ route('admin.dashboard') }}">⌂ <span>Home</span></a><i>›</i><a href="{{ route('admin.brands.index') }}">Brand &amp; Menu</a><i>›</i><b>Brand Management</b></div>
<div class="page-title-row"><div><h1>Brand Management</h1><p>View, create, update, or delete restaurant brands.</p></div><a class="button primary" href="{{ route('admin.brands.create') }}"><span class="plus">+</span> Create Brand</a></div>
<section class="content-card table-card">
  <div class="toolbar"><div class="toolbar-filters"><label class="search-box"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg><input id="brand-search" type="search" placeholder="Search brand name..." aria-label="Search brand name"></label><select id="brand-status-filter" class="select-control" aria-label="Filter by brand status"><option value="">All Status</option><option value="active">Active</option><option value="inactive">Inactive</option><option value="archived">Archived</option></select></div><span class="result-count" id="brand-result-count">Loading…</span></div>
  <div class="table-scroll"><table class="admin-table"><thead><tr><th>#</th><th>Brand Name</th><th>Description</th><th>Status</th><th>Menu Items</th><th class="actions-head">Actions</th></tr></thead><tbody id="brand-table-body"><tr><td colspan="6" class="loading-row">Loading brands…</td></tr></tbody></table></div>
</section>
@endsection
@push('scripts')<script type="module" src="{{ asset('js/catalog-admin.js') }}"></script>@endpush
