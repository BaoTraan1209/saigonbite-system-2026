@extends('layout')
@section('title','Order saved | SaigonBite')
@section('content')
@include('partials.progress',['activeStep'=>5,'steps'=>['Restaurant','Menu','Checkout','Review','Saved']])
<div class="success-intro"><div class="success-check">✓</div><small class="eyebrow">ORDER SAVED</small><h1>Your order is recorded.</h1><p>This order is saved in SaigonBite. It has not been sent to the restaurant.</p></div>
<div class="order-meta row g-0"><div class="col-md-6"><small>ORDER ID</small><b id="success-id"></b></div><div class="col-md-6"><small>ORDER DATE & TIME</small><b id="success-date"></b><span>Ho Chi Minh City time</span></div></div>
<div class="row g-4"><section class="col-lg-8"><div class="panel mb-3"><h2>Order status</h2><em class="received" id="success-status"></em></div>
<div class="panel mb-3"><h2>Selected branch</h2><b id="success-hub"></b><p class="muted" id="success-hub-address"></p></div>
<div class="panel"><h2>Delivery details</h2><div id="success-address" class="address-review"></div><p class="muted" id="success-note"></p></div></section>
<aside class="col-lg-4"><div class="summary"><h2>Payment method</h2><em class="available mt-2" id="success-payment"></em><hr><div class="price"><span>Subtotal</span><b id="success-subtotal">0 ₫</b></div><div class="price"><span>Delivery fee</span><b>15,000 ₫</b></div><div class="price total"><span>Total amount</span><b id="success-total">15,000 ₫</b></div><a class="btn-orange full mt-2" href="{{ route('order.start') }}" id="back-home">Back to restaurants</a></div></aside></div>
@endsection
