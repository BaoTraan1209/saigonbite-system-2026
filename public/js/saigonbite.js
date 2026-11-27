import { get, post } from '/assets/js/api.js';

(() => {
  const KEY = 'saigonbite_order';
  const defaults = { hubId: null, brandId: null, cart: {}, address: {}, payment: 'COD', orderId: null, idempotencyKey: null };
  let state;
  try { state = { ...defaults, ...JSON.parse(localStorage.getItem(KEY) || '{}') }; }
  catch { state = { ...defaults }; }
  const save = () => localStorage.setItem(KEY, JSON.stringify(state));
  const $ = (id) => document.getElementById(id);
  const money = (value) => `${new Intl.NumberFormat('vi-VN').format(Number(value) || 0)} ₫`;
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const itemInitial = (name) => esc(Array.from(String(name || '').trim())[0]?.toLocaleUpperCase('vi-VN') || '?');
  const showError = (error) => { alert(error.message || 'Không thể tải dữ liệu.'); };
  document.querySelectorAll('.voucher-row, .voucher-applied').forEach((element) => { element.hidden = true; });

  async function loadStart() {
    const hubs = await get('/hubs?available=1');
    const hubList = $('hub-list');
    const brandList = $('brand-list');
    const continueLink = $('continue-menu');
    if (!hubs.length) {
      hubList.innerHTML = '<p class="muted">Chưa có chi nhánh đang hoạt động. Hãy nhập dữ liệu nhà hàng thật vào database.</p>';
      brandList.innerHTML = '<p class="muted">Thương hiệu sẽ hiển thị sau khi chọn chi nhánh.</p>';
      continueLink?.addEventListener('click', (event) => event.preventDefault());
      return;
    }
    if (!hubs.some((hub) => hub.id === state.hubId)) state.hubId = hubs[0].id;
    const availableBrands = await get(`/brands?hub_id=${state.hubId}`);
    if (!availableBrands.some((brand) => brand.id === state.brandId)) state.brandId = availableBrands[0]?.id ?? null;
    hubList.innerHTML = hubs.map((hub) => `<button class="pick hub-card ${hub.id === state.hubId ? 'selected' : ''}" data-hub="${hub.id}"><i class="radio"></i><span class="pick-main"><b>${esc(hub.name)}</b><small>${esc(hub.address)}</small><small>SaigonBite queue: ${hub.current_load}/${hub.capacity}</small></span><em class="available">Listed branch</em></button>`).join('');
    if (brandList) brandList.innerHTML = availableBrands.map((brand) => `<button class="pick brand-card ${brand.id === state.brandId ? 'selected' : ''}" data-brand="${brand.id}"><i class="radio"></i><i class="brand-icon red">${esc(brand.name.slice(0, 1))}</i><span class="pick-main"><b>${esc(brand.name)}</b><small>${esc(brand.description || '')}</small></span></button>`).join('') || '<p>No active brands at this hub.</p>';
    const updateLabel = () => {
      const hub = hubs.find((item) => item.id === state.hubId);
      const brand = availableBrands.find((item) => item.id === state.brandId);
      if ($('selection-label')) $('selection-label').textContent = `${hub?.name || ''} / ${brand?.name || ''}`;
      if (continueLink) {
        continueLink.href = brand ? '/order/menu' : '#';
        continueLink.setAttribute('aria-disabled', brand ? 'false' : 'true');
        continueLink.onclick = brand ? null : (event) => event.preventDefault();
      }
      save();
    };
    hubList.onclick = async (event) => {
      const button = event.target.closest('[data-hub]'); if (!button) return;
      state.hubId = Number(button.dataset.hub);
      const nextBrands = await get(`/brands?hub_id=${state.hubId}`);
      state.brandId = nextBrands[0]?.id ?? null;
      await loadStart();
    };
    if (brandList) brandList.onclick = (event) => {
      const button = event.target.closest('[data-brand]'); if (!button) return;
      state.brandId = Number(button.dataset.brand);
      brandList.querySelectorAll('[data-brand]').forEach((item) => item.classList.toggle('selected', item === button));
      updateLabel();
    };
    updateLabel();
  }

  async function loadMenu() {
    if (!state.hubId || !state.brandId) { location.replace('/order'); return; }
    const [hub, brand, items] = await Promise.all([
      get(`/hubs/${state.hubId}`), get(`/brands/${state.brandId}?hub_id=${state.hubId}`),
      get(`/menu-items?hub_id=${state.hubId}&brand_id=${state.brandId}`),
    ]);
    $('menu-brand').textContent = `${brand.name} / ${hub.name}`;
    $('menu-address').textContent = hub.address;
    if ($('menu-status')) $('menu-status').textContent = hub.status;
    const list = $('menu-list');
    list.innerHTML = items.map((item) => `<article class="menu-item ${item.is_available ? '' : 'unavailable'}" data-id="${item.id}"><div class="food" aria-hidden="true">${itemInitial(item.name)}</div><div class="food-info"><small>${esc(brand.name)}</small><h3>${esc(item.name)}</h3><p>${esc(item.description || '')}</p><b>${money(item.price)}</b>${item.is_available ? '' : '<span class="soldout">Currently unavailable</span>'}</div><div class="qty"><button data-action="minus" ${item.is_available ? '' : 'disabled'}>−</button><b data-qty>${Number(state.cart[item.id] || 0)}</b><button data-action="plus" ${item.is_available ? '' : 'disabled'}>+</button></div></article>`).join('') || '<p>Menu items have not been added yet.</p>';
    const render = () => {
      list.querySelectorAll('.menu-item').forEach((card) => {
        const quantity = Number(state.cart[card.dataset.id] || 0);
        card.querySelector('[data-qty]').textContent = quantity;
        card.querySelector('[data-action="minus"]').disabled = quantity === 0 || card.classList.contains('unavailable');
      });
      const selected = items.filter((item) => Number(state.cart[item.id]) > 0 && item.is_available);
      const count = selected.reduce((sum, item) => sum + Number(state.cart[item.id]), 0);
      const subtotal = selected.reduce((sum, item) => sum + Number(item.price) * Number(state.cart[item.id]), 0);
      if ($('summary-items')) $('summary-items').innerHTML = selected.map((item) => `<div class="summary-line"><span>${esc(item.name)} × ${state.cart[item.id]}</span><b>${money(item.price * state.cart[item.id])}</b></div>`).join('');
      if ($('cart-count')) $('cart-count').textContent = `${count} items`;
      if ($('subtotal')) $('subtotal').textContent = money(subtotal);
      if ($('menu-total')) $('menu-total').textContent = money(subtotal);
      save();
    };
    list.onclick = (event) => {
      const button = event.target.closest('[data-action]'); if (!button) return;
      const card = button.closest('.menu-item'); const id = card.dataset.id;
      state.cart[id] = Math.max(0, Number(state.cart[id] || 0) + (button.dataset.action === 'plus' ? 1 : -1)); render();
    };
    render();
  }

  async function loadCheckout() {
    if (!state.hubId || !state.brandId) { location.replace('/order'); return; }
    const form = $('checkout-form');
    Object.entries(state.address || {}).forEach(([name, value]) => { if (form.elements[name]) form.elements[name].value = value; });
    document.querySelectorAll('input[name="payment"]').forEach((input) => {
      input.checked = input.value.toUpperCase() === state.payment;
      input.onchange = () => { state.payment = input.value.toUpperCase(); save(); };
    });
    const items = await get(`/menu-items?hub_id=${state.hubId}&brand_id=${state.brandId}`);
    const selected = items.filter((item) => Number(state.cart[item.id]) > 0 && item.is_available);
    const subtotal = selected.reduce((sum, item) => sum + Number(item.price) * Number(state.cart[item.id]), 0);
    if (!selected.length) throw new Error('Giỏ hàng trống. Vui lòng chọn món trước.');
    $('checkout-items').innerHTML = selected.map((item) => `<div class="summary-line"><span>${esc(item.name)} × ${state.cart[item.id]}</span><b>${money(item.price * state.cart[item.id])}</b></div>`).join('');
    $('checkout-subtotal').textContent = money(subtotal);
    $('checkout-total').textContent = money(subtotal + 15000);
    form.onsubmit = (event) => {
      event.preventDefault(); if (!form.reportValidity()) return;
      const data = new FormData(form);
      state.address = { customer_name: data.get('recipient'), customer_phone: data.get('phone'), street: data.get('street'), district: data.get('district'), city: data.get('city'), notes: data.get('note') };
      state.payment = String(data.get('payment') || 'COD').toUpperCase(); save(); location.href = '/order/review';
    };
  }

  async function loadReview() {
    if (!state.hubId || !state.brandId || !state.address?.customer_name) { location.replace('/order/checkout'); return; }
    const [hub, brand, items] = await Promise.all([get(`/hubs/${state.hubId}`), get(`/brands/${state.brandId}?hub_id=${state.hubId}`), get(`/menu-items?hub_id=${state.hubId}&brand_id=${state.brandId}`)]);
    const selected = items.filter((item) => Number(state.cart[item.id]) > 0 && item.is_available);
    const subtotal = selected.reduce((sum, item) => sum + Number(item.price) * Number(state.cart[item.id]), 0);
    $('review-hub').textContent = hub.name; $('review-hub-address').textContent = hub.address; $('review-brand').textContent = brand.name;
    $('review-items').innerHTML = selected.map((item) => `<div class="review-line"><div><b>${esc(item.name)}</b><p>${esc(item.description || '')}</p><small>Qty ×${state.cart[item.id]} · ${money(item.price)} each</small></div><b>${money(item.price * state.cart[item.id])}</b></div>`).join('');
    $('review-dish-count').textContent = `1 brand · ${selected.length} dishes · ${selected.reduce((n, item) => n + Number(state.cart[item.id]), 0)} items`;
    $('review-subtotal').textContent = money(subtotal);
    const address = state.address;
    $('review-address').textContent = `${address.customer_name} · ${address.customer_phone}\n${address.street}, ${address.district}, ${address.city}`;
    $('review-note').textContent = address.notes || '';
    $('review-payment').textContent = 'Cash on delivery';
    state.payment = 'COD';
    const orderPayload = {
      hub_id: state.hubId, brand_id: state.brandId,
      customer_name: address.customer_name, customer_phone: address.customer_phone,
      delivery_address: [address.street, address.district, address.city].filter(Boolean).join(', '),
      payment_method: 'COD', notes: address.notes || null,
      items: selected.map((item) => ({ menu_item_id: item.id, quantity: Number(state.cart[item.id]) })),
    };
    const pricing = await post('/orders/preview', orderPayload);
    $('review-subtotal').textContent = money(pricing.subtotal);
    $('review-delivery-fee').textContent = money(pricing.delivery_fee);
    $('review-surcharge').textContent = money(pricing.surcharge);
    $('review-discount').textContent = Number(pricing.discount_amount) > 0 ? `-${money(pricing.discount_amount)}` : money(0);
    $('review-total').textContent = money(pricing.total_amount);
    $('confirm-order').onclick = async () => {
      state.idempotencyKey ||= (crypto.randomUUID?.() || `${Date.now()}-${Math.random().toString(16).slice(2)}`);
      try { const order = await post('/orders', { ...orderPayload, idempotency_key: state.idempotencyKey }); state.orderId = order.id; save(); location.href = '/order/success'; }
      catch (error) { showError(error); }
    };
  }

  async function loadSuccess() {
    if (!state.orderId) { location.replace('/order'); return; }
    const order = await get(`/orders/${state.orderId}`);
    $('success-id').textContent = `#${order.id}`;
    $('success-date').textContent = new Date(order.created_at).toLocaleString('vi-VN');
    $('success-hub').textContent = order.hub?.name || '';
    $('success-hub-address').textContent = order.hub?.address || '';
    $('success-address').textContent = `${order.customer_name} · ${order.customer_phone}\n${order.delivery_address}`;
    $('success-note').textContent = order.notes || '';
    $('success-status').textContent = order.status;
    $('success-payment').textContent = order.payment_method;
    $('success-subtotal').textContent = money(order.subtotal);
    $('success-total').textContent = money(order.total_amount);
  }

  const path = location.pathname;
  const task = path === '/' || path === '/order' ? loadStart() : path === '/order/menu' ? loadMenu() : path === '/order/checkout' ? loadCheckout() : path === '/order/review' ? loadReview() : path === '/order/success' ? loadSuccess() : Promise.resolve();
  task.catch(showError);
})();
