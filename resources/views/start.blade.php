@extends('layout')
@section('title','Choose a restaurant | SaigonBite')
@section('content')
@include('partials.progress',['activeStep'=>1])
<div class="intro"><div><small class="eyebrow">HỒ CHÍ MINH CITY</small><h1>Choose a restaurant.</h1><p>Restaurant locations and menu prices are loaded from the database.</p></div><div class="tagline"><b>RESTAURANT LOCATIONS</b><span>Choose a branch to see its menu.</span></div></div>
<div class="row g-4"><section class="col-lg-5"><div class="section-head"><h2>01 Select a location</h2><p>Available branches in the system.</p></div><div class="vstack gap-3" id="hub-list"><p class="muted">Loading locations…</p></div></section>
<section class="col-lg-7"><div class="section-head"><h2>02 Choose a brand</h2><p>Brands available at the selected location.</p></div><div class="vstack gap-3" id="brand-list"><p class="muted">Loading brands…</p></div></section></div>
<div class="selection"><div><small>YOUR SELECTION</small><b id="selection-label">Select a location and brand</b></div><div class="selection-right"><span><b>Browse the live database catalog.</b><small>Menu and prices are listed per branch.</small></span><a class="btn-orange" id="continue-menu" href="#" aria-disabled="true">Continue →</a></div></div>
@endsection
