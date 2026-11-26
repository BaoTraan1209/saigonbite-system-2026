@extends('layout')
@section('title', 'Welcome | SaigonBite')
@push('styles')
<link href="{{ asset('css/saigonbite-dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="dashboard-hero">
    <div class="hero-shade"></div>
    <div class="hero-content">
        <span class="hero-kicker">WELCOME TO SAIGONBITE</span>
        <h1>Good food.<br><em>Good mood.</em></h1>
        <p>Welcome to SaigonBite. Discover your favorite flavors, freshly prepared by local kitchens and delivered to your door.</p>
        <a href="{{ route('order.start') }}" class="hero-order-btn">Order Now <span>→</span></a>
        <div class="hero-footnote"><span class="hero-live-dot"></span> Freshly prepared · Made for Saigon</div>
    </div>
    <div class="hero-image-caption">
        <span>FRESH FLAVORS, EVERY DAY</span>
        <b>A little Saigon in every bite.</b>
    </div>
</section>

<section class="dashboard-welcome-row">
    <div>
        <span class="dashboard-eyebrow">YOUR NEXT FAVORITE MEAL IS HERE</span>
        <h2>What are you craving today?</h2>
        <p>Choose a nearby hub, explore our partner brands, and make your meal your own.</p>
    </div>
    <a href="{{ route('order.start') }}" class="dashboard-text-link">Explore menu <span>↗</span></a>
</section>

<section class="dashboard-feature-grid">
    <article class="dashboard-feature">
        <div class="feature-icon">⌖</div>
        <div><h3>Nearby kitchens</h3><p>Choose a hub close to your location.</p></div>
    </article>
    <article class="dashboard-feature">
        <div class="feature-icon">✦</div>
        <div><h3>Freshly made</h3><p>Your meal is prepared when you order.</p></div>
    </article>
    <article class="dashboard-feature">
        <div class="feature-icon">♡</div>
        <div><h3>Made for you</h3><p>Discover flavors for every craving.</p></div>
    </article>
</section>

<section class="dashboard-bottom-cta">
    <div><span>READY WHEN YOU ARE</span><h2>Something delicious is just a click away.</h2></div>
    <a href="{{ route('order.start') }}" class="dashboard-bottom-btn">Start your order <span>→</span></a>
</section>
@endsection
