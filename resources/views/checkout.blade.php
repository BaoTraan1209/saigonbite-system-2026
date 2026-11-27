@extends('layout')
@section('title','Checkout | SaigonBite')
@section('content')
@include('partials.customer-back',['backRoute'=>'order.menu'])
@include('partials.progress',['activeStep'=>3])
<div class="page-heading"><small class="eyebrow">CHECKOUT</small><h1>Delivery details</h1><p>Enter the details to record your order.</p></div>
<div class="row g-4"><section class="col-lg-8"><form id="checkout-form" novalidate>
<div class="panel mb-3"><div class="panel-title"><i>1</i><div><h2>Delivery address</h2><p>Where should the order be delivered?</p></div></div><div class="row g-3">
<div class="col-md-6"><label>Recipient name *</label><input class="form-control" name="recipient" required></div><div class="col-md-6"><label>Phone number *</label><input class="form-control" name="phone" required></div>
<div class="col-12"><label>Street *</label><input class="form-control" name="street" required></div><div class="col-md-6"><label>District *</label><input class="form-control" name="district" required></div><div class="col-md-6"><label>City *</label><input class="form-control" name="city" value="Ho Chi Minh City" required></div><div class="col-12"><label>Delivery note</label><input class="form-control" name="note"></div></div><small class="hint">* Required fields</small></div>
<div class="panel"><div class="panel-title"><i>2</i><div><h2>Payment method</h2></div></div><div class="row g-3"><div class="col-md-6"><label class="payment selected"><input type="radio" name="payment" value="COD" checked> <span><b>Cash on delivery</b></span></label></div></div></div>
<button class="btn-orange full mt-3 d-lg-none" type="submit">Review order →</button></form></section>
<aside class="col-lg-4"><div class="summary sticky"><div class="summary-head"><h2>Order summary</h2></div><div id="checkout-items"></div><div class="price"><span>Subtotal</span><b id="checkout-subtotal">0 ₫</b></div><div class="price"><span>Delivery fee</span><b>15,000 ₫</b></div><div class="price total"><span>Total</span><b id="checkout-total">15,000 ₫</b></div><button class="btn-orange full mt-3 d-none d-lg-block" type="submit" form="checkout-form">Review order →</button></div></aside></div>
@endsection
