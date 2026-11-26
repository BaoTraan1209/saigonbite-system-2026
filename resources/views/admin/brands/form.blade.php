@extends('admin.layout')
@section('title', 'Brand Details | SaigonBite')
@section('content')
<div class="breadcrumb-line"><a href="{{ route('admin.dashboard') }}">⌂ <span>Home</span></a><i>›</i><a href="{{ route('admin.brands.index') }}">Brand &amp; Menu</a><i>›</i><a href="{{ route('admin.brands.index') }}">Brand Management</a><i>›</i><b id="brand-breadcrumb">{{ request()->routeIs('admin.brands.edit') ? 'Edit Brand' : 'Create Brand' }}</b></div>
<div class="page-title-row form-title-row"><div><h1 id="brand-page-title">{{ request()->routeIs('admin.brands.edit') ? 'Edit Brand' : 'Create Brand' }}</h1><p>{{ request()->routeIs('admin.brands.edit') ? 'Update the brand information and save the changes.' : 'Create a brand and make it available to add menu items.' }}</p></div><a class="button secondary back-button" href="{{ route('admin.brands.index') }}"><span aria-hidden="true">&larr;</span> Back</a></div>
<div class="brand-form-layout" data-screen="brand-form" data-brand-id="{{ request()->route('brand') ?? '' }}">
  <section class="content-card brand-form-card">
    <form id="brand-editor" novalidate>
      <div class="form-field"><label for="brand-name">Brand Name <em>*</em></label><input id="brand-name" class="form-control" name="name" maxlength="150" required placeholder="Enter brand name"><small class="field-feedback" id="brand-name-feedback"></small></div>
      <div class="form-field"><div class="field-label-line"><label for="brand-description">Description</label><span id="brand-description-count">0 / 500</span></div><textarea id="brand-description" class="form-control" name="description" maxlength="500" rows="5" placeholder="Describe the brand..."></textarea></div>
      <div class="form-field"><label for="brand-status">Status <em>*</em></label><select id="brand-status" class="form-select" name="status" required><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
      <div class="form-actions"><button class="button primary" id="brand-save" type="submit">✓ Save Brand</button><a class="button secondary" href="{{ route('admin.brands.index') }}">Cancel</a></div>
      <div class="form-inline-message" id="brand-form-message" role="status" aria-live="polite"></div>
    </form>
  </section>
  <aside class="brand-preview-card"><div class="preview-label">BRAND PREVIEW</div><div class="preview-logo" id="brand-preview-logo">SB</div><h2 id="brand-preview-name">Brand name</h2><span id="brand-preview-status" class="status-pill active">Active</span><div class="preview-items"><span class="fork-icon">♜</span><b id="brand-preview-items">0</b> menu items</div><p class="preview-description" id="brand-preview-description">Your brand description will appear here.</p><div class="preview-info"><span>ⓘ</span><p id="brand-preview-note">This brand is currently active and available for ordering.</p></div></aside>
</div>
@endsection
@push('scripts')<script type="module" src="{{ asset('js/catalog-admin.js') }}"></script>@endpush
