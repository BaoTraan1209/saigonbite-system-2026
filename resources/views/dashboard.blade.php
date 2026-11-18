<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SaigonBite Dashboard</title>
<link rel="stylesheet" href="/css/app.css">
</head>
<body>
<header><h1>SaigonBite</h1><span>Online Food Delivery Hub Platform</span></header>
<main>
<section class="stats">
<div class="stat"><b>{{ $stats['orders'] }}</b><span>Orders</span></div>
<div class="stat"><b>{{ $stats['hubs'] }}</b><span>Hubs</span></div>
<div class="stat"><b>{{ $stats['brands'] }}</b><span>Brands</span></div>
<div class="stat"><b>{{ $stats['deliveries'] }}</b><span>Deliveries</span></div>
</section>
<nav>
<button data-panel="orders">Orders</button><button data-panel="hubs">Hubs</button>
<button data-panel="brands">Brands & Menu</button><button data-panel="kitchen">Kitchen</button>
<button data-panel="deliveries">Deliveries</button><button data-panel="reports">Reports</button>
</nav>
<section id="panel"><h2>Welcome</h2><p>Select a module to load its data.</p></section>
</main>
<script src="/js/app.js"></script>
</body></html>
