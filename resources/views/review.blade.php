@extends('layout')
@section('title','Review order | SaigonBite')
@section('content')
@include('partials.progress',['activeStep'=>4,'steps'=>['Restaurant','Menu','Checkout','Review']])
<div class="page-heading"><small class="eyebrow orange-text">REVIEW</small><h1>Review your order</h1><p>Check your details before saving the order.</p></div>
<div class="row g-4"><section class="col-lg-8">
<div class="panel mb-3"><div class="panel-title"><i>1</i><h2>Selected branch</h2></div><b id="review-hub"></b><p class="muted" id="review-hub-address"></p></div>
<div class="panel mb-3"><div class="panel-title"><i>2</i><div><h2>Brand & dishes</h2><p id="review-dish-count"></p></div></div><div class="brand-review"><b id="review-brand"></b></div><div id="review-items"></div></div>
<div class="panel mb-3"><div class="panel-title"><i>3</i><h2>Delivery address</h2></div><div id="review-address" class="address-review"></div><p class="muted" id="review-note"></p></div>
<div class="panel"><div class="panel-title"><i>4</i><h2>Payment method</h2><em class="available ms-auto" id="review-payment"></em></div><p class="muted">Payment is not processed by this application.</p></div></section>
<aside class="col-lg-4"><div class="summary sticky"><h2>Order summary</h2><div class="price"><span>Subtotal</span><b id="review-subtotal">0 ₫</b></div><div class="price"><span>Delivery fee</span><b>15,000 ₫</b></div><div class="price total"><span>Total</span><b id="review-total">15,000 ₫</b></div><button class="btn-orange full mt-3" id="confirm-order">Save order</button><a class="btn-outline full mt-2" href="{{ route('order.checkout') }}">Edit details</a><p class="terms">This saves the order to SaigonBite. It does not send it to the restaurant.</p></div></aside></div>
@endsection
