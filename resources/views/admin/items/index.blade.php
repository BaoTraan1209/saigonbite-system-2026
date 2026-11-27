@extends('admin.layout')
@section('title', 'Menu Item Management | SaigonBite')
@section('content')
<div class="breadcrumb-line"><a href="{{ route('admin.dashboard') }}">⌂ <span>Home</span></a><i>›</i><a href="{{ route('admin.brands.index') }}">Brand &amp; Menu</a><i>›</i><b>Menu Item Management</b></div>
<div class="page-title-row"><div><h1>Menu Item Management</h1><p>View, add, update, or delete menu items for each brand.</p></div><div class="page-title-actions"><a class="button secondary back-button" href="{{ route('admin.dashboard') }}"><span aria-hidden="true">&larr;</span> Back</a><button class="button primary" type="button" id="focus-item-form" aria-expanded="false" aria-controls="item-editor-card"><span class="plus">+</span> Add Menu Item</button></div></div>
<div class="items-layout form-closed" data-screen="menu-items">
  <section class="content-card item-table-card">
    <div class="toolbar item-toolbar"><div class="toolbar-filters"><label class="search-box"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg><input id="item-search" type="search" placeholder="Search menu item..." aria-label="Search menu item"></label><select id="item-brand-filter" class="select-control" aria-label="Filter by brand"><option value="">Brand: All Brands</option></select><select id="item-status-filter" class="select-control" aria-label="Filter by status"><option value="">Status: All</option><option value="active">Active</option><option value="inactive">Inactive</option></select></div><span class="result-count" id="item-result-count">Loading…</span></div>
    <div class="table-scroll"><table class="admin-table item-table"><thead><tr><th>#</th><th>Menu Item</th><th>Brand</th><th>Description</th><th class="price-head">Price</th><th>Status</th><th class="actions-head">Actions</th></tr></thead><tbody id="item-table-body"><tr><td colspan="7" class="loading-row">Loading menu items…</td></tr></tbody></table></div>
  </section>
  <aside class="content-card item-editor-card" id="item-editor-card" hidden><div class="item-editor-heading"><div class="form-icon">＋</div><div><h2 id="item-form-title">Add Menu Item</h2><p>Fill in the menu item details.</p></div></div>
    <form id="item-editor" novalidate><input type="hidden" name="id">
      <div class="form-field"><label for="item-name">Menu Item Name <em>*</em></label><input id="item-name" class="form-control" name="name" maxlength="150" required placeholder="Enter menu item name"></div>
      <div class="form-field"><label for="item-brand">Brand <em>*</em></label><select id="item-brand" class="form-select" name="brand_id" required><option value="">Select a brand</option></select></div>
      <div class="form-field"><div class="field-label-line"><label for="item-description">Description</label><span id="item-description-count">0 / 500</span></div><textarea id="item-description" class="form-control" name="description" maxlength="500" rows="3" placeholder="Enter a short description... (optional)"></textarea></div>
      <div class="form-field"><label for="item-price">Price (VND) <em>*</em></label><input id="item-price" class="form-control" type="number" name="price" min="0" step="1000" value="0" required></div>
      <div class="form-field"><label for="item-status">Status <em>*</em></label><select id="item-status" class="form-select" name="is_active"><option value="true">Active</option><option value="false">Inactive</option></select></div>
      <div class="form-actions"><button class="button primary" id="item-save" type="submit">✓ Save Item</button><button class="button secondary" id="item-cancel" type="button">Cancel</button></div><div class="form-inline-message" id="item-form-message" role="status" aria-live="polite"></div>
    </form>
  </aside>
</div>
@endsection
@push('scripts')<script type="module" src="{{ asset('js/catalog-admin.js') }}"></script>@endpush
