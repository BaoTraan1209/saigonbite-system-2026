@extends('layout')
@section('title','Menu | SaigonBite')
@section('content')
@include('partials.customer-back',['backRoute'=>'order.start'])
@include('partials.progress',['activeStep'=>2])
<div class="intro"><div><small class="eyebrow">RESTAURANT MENU</small><h1>Choose your meal.</h1><p>Prices are read from the catalog in the database.</p></div><div class="hub-mini"><b id="menu-brand">Loading brand…</b><span id="menu-address">Loading address…</span><em class="available" id="menu-status">Catalog</em><a href="{{ route('order.start') }}">Change location</a></div></div>
<div class="row g-4"><section class="col-lg-8"><div class="section-head"><h2>Menu items</h2><p>Select quantities to add items.</p></div><div class="vstack gap-3" id="menu-list"><p class="muted">Loading menu…</p></div></section>
<aside class="col-lg-4"><div class="summary sticky"><div class="summary-head"><h2>Order summary</h2><em id="cart-count">0 items</em></div><p class="summary-hub" id="summary-brand"></p><div id="summary-items"></div><div class="price"><span>Subtotal</span><b id="subtotal">0 ₫</b></div><div class="price"><span>Delivery fee</span><b>15,000 ₫</b></div><div class="price total"><span>Total</span><b id="menu-total">15,000 ₫</b></div><a href="{{ route('order.checkout') }}" class="btn-orange full" id="continue-checkout">Continue to checkout →</a><p class="center-note">Your order will be recorded in this SaigonBite database.</p></div></aside></div>
@endsection
