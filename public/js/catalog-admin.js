import { del, get, post, put } from '/assets/js/api.js';

const $ = (selector, root = document) => root.querySelector(selector);
const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]));
const money = (value) => `${new Intl.NumberFormat('vi-VN').format(Number(value) || 0)} ₫`;
const state = { brands: [], items: [] };

const icons = {
  eye: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.6"/></svg>',
  edit: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14 5 5 5M4 20l4.2-.9L20 7.3 16.7 4 5 15.8 4 20Z"/></svg>',
  trash: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5"/></svg>',
};

function toast(message, error = false) {
  const stack = $('#toast-stack');
  if (!stack) return;
  const item = document.createElement('div');
  item.className = `admin-toast${error ? ' error' : ''}`;
  item.setAttribute('role', error ? 'alert' : 'status');
  item.textContent = message;
  stack.append(item);
  window.setTimeout(() => item.remove(), 4800);
}

function statusPill(label, kind) {
  return `<span class="status-pill ${esc(kind)}">${esc(label)}</span>`;
}

function brandInitial(brand) {
  return esc(String(brand?.name || 'SB').trim().slice(0, 2).toUpperCase());
}

function brandThumb(brand, index = 0) {
  return `<span class="brand-thumb" data-tone="${index % 3}" aria-hidden="true">${brandInitial(brand)}</span>`;
}

function foodEmoji(name) {
  const text = String(name || '').toLowerCase();
  if (text.includes('chicken') || text.includes('gà')) return '🍗';
  if (text.includes('burger')) return '🍔';
  if (text.includes('fries') || text.includes('khoai')) return '🍟';
  if (text.includes('spaghetti') || text.includes('mì')) return '🍝';
  if (text.includes('tea') || text.includes('matcha')) return '🍵';
  if (text.includes('pizza')) return '🍕';
  return '🍱';
}

function menuCount(brand) {
  if (Array.isArray(brand.menu_items)) return brand.menu_items.length;
  return state.items.filter((item) => Number(item.brand_id) === Number(brand.id)).length;
}

async function fetchCatalog() {
  const [brands, items] = await Promise.all([get('/brands'), get('/menu-items')]);
  state.brands = Array.isArray(brands) ? brands : [];
  state.items = Array.isArray(items) ? items : [];
  return state;
}

function showEmpty(colspan, title, description) {
  return `<tr><td colspan="${colspan}" class="empty-row"><strong>${esc(title)}</strong>${esc(description)}</td></tr>`;
}

async function loadDashboard() {
  await fetchCatalog();
  const brandCount = state.brands.length;
  const itemCount = state.items.length;
  $('#dashboard-brand-count').textContent = brandCount;
  $('#dashboard-brand-items').textContent = itemCount;
  $('#dashboard-item-count').textContent = itemCount;
  $('#dashboard-item-brands').textContent = new Set(state.items.map((item) => item.brand_id)).size;

  $('#recent-brands').innerHTML = state.brands.slice(0, 3).map((brand, index) => `<div class="recent-row">
    ${brandThumb(brand, index)}<div class="recent-info"><b>${esc(brand.name)}</b><small>${menuCount(brand)} menu items</small></div>
    <div class="recent-meta">${statusPill(brand.status, brand.status)}</div>
  </div>`).join('') || '<div class="recent-empty">No brands in the catalog yet.</div>';

  $('#recent-items').innerHTML = state.items.slice(0, 3).map((item) => `<div class="recent-row">
    <span class="food-thumb" aria-hidden="true">${foodEmoji(item.name)}</span><div class="recent-info"><b>${esc(item.name)}</b><small>${esc(item.brand?.name || 'Unassigned brand')}</small></div>
    <div class="recent-meta"><span class="recent-price">${money(item.price)}</span>${statusPill(item.is_available ? 'Available' : 'Unavailable', item.is_available ? 'available' : 'unavailable')}</div>
  </div>`).join('') || '<div class="recent-empty">No menu items in the catalog yet.</div>';
}

function filteredBrands() {
  const query = ($('#brand-search')?.value || '').trim().toLocaleLowerCase();
  const status = $('#brand-status-filter')?.value || '';
  return state.brands.filter((brand) => (!query || brand.name.toLocaleLowerCase().includes(query)) && (!status || brand.status === status));
}

function renderBrandTable() {
  const body = $('#brand-table-body');
  if (!body) return;
  const visible = filteredBrands();
  $('#brand-result-count').innerHTML = `<b>${visible.length}</b> ${visible.length === 1 ? 'brand' : 'brands'}`;
  body.innerHTML = visible.map((brand, index) => `<tr>
    <td>${index + 1}</td>
    <td><div class="brand-cell">${brandThumb(brand, index)}<span class="brand-cell-name"><b>${esc(brand.name)}</b><small>Brand ID: ${esc(brand.id)}</small></span></div></td>
    <td class="description-cell">${esc(brand.description || '—')}</td>
    <td>${statusPill(brand.status, brand.status)}</td>
    <td class="menu-count">${menuCount(brand)}</td>
    <td><div class="action-group"><button class="action-button" type="button" data-brand-action="view" data-id="${brand.id}">View</button><a class="action-button edit" href="/admin/catalog/brands/${encodeURIComponent(brand.id)}/edit">Edit</a><button class="action-button delete" type="button" data-brand-action="delete" data-id="${brand.id}">Delete</button></div></td>
  </tr>`).join('') || showEmpty(6, 'No brands found', 'Change the search or status filter, or create a new brand.');
}

async function loadBrands() {
  $('#brand-table-body').innerHTML = '<tr><td colspan="6" class="loading-row">Loading brands…</td></tr>';
  await fetchCatalog();
  renderBrandTable();
}

function renderItemTable() {
  const body = $('#item-table-body');
  if (!body) return;
  const query = ($('#item-search').value || '').trim().toLocaleLowerCase();
  const brandId = $('#item-brand-filter').value;
  const status = $('#item-status-filter').value;
  const visible = state.items.filter((item) => {
    const matchesSearch = !query || String(item.name).toLocaleLowerCase().includes(query) || String(item.description || '').toLocaleLowerCase().includes(query);
    const matchesBrand = !brandId || String(item.brand_id) === brandId;
    const available = Boolean(item.is_available);
    const matchesStatus = !status || (status === 'available' ? available : !available);
    return matchesSearch && matchesBrand && matchesStatus;
  });
  $('#item-result-count').innerHTML = `<b>${visible.length}</b> ${visible.length === 1 ? 'item' : 'items'}`;
  body.innerHTML = visible.map((item, index) => `<tr>
    <td>${index + 1}</td>
    <td><div class="item-cell"><span class="food-thumb" aria-hidden="true">${foodEmoji(item.name)}</span><span class="item-cell-name"><b>${esc(item.name)}</b><small>Item ID: ${esc(item.id)}</small></span></div></td>
    <td><span class="brand-badge">${esc(item.brand?.name || state.brands.find((brand) => Number(brand.id) === Number(item.brand_id))?.name || '—')}</span></td>
    <td class="item-desc">${esc(item.description || '—')}</td>
    <td class="price-cell">${money(item.price)}</td>
    <td>${statusPill(item.is_available ? 'Available' : 'Unavailable', item.is_available ? 'available' : 'unavailable')}</td>
    <td><div class="action-group"><button class="action-button icon-only" type="button" aria-label="View ${esc(item.name)}" title="View" data-item-action="view" data-id="${item.id}">${icons.eye}</button><button class="action-button icon-only edit" type="button" aria-label="Edit ${esc(item.name)}" title="Edit" data-item-action="edit" data-id="${item.id}">${icons.edit}</button><button class="action-button icon-only delete" type="button" aria-label="Delete ${esc(item.name)}" title="Delete" data-item-action="delete" data-id="${item.id}">${icons.trash}</button></div></td>
  </tr>`).join('') || showEmpty(7, 'No menu items found', 'Try another search or filter, or add a menu item.');
}

function populateBrandSelects(selectedId = '') {
  const activeSelect = $('#item-brand');
  const filterSelect = $('#item-brand-filter');
  if (activeSelect) {
    activeSelect.innerHTML = '<option value="">Select a brand</option>' + state.brands.filter((brand) => brand.status !== 'archived' || String(brand.id) === String(selectedId)).map((brand) => `<option value="${brand.id}">${esc(brand.name)}${brand.status === 'inactive' ? ' (Inactive)' : brand.status === 'archived' ? ' (Archived)' : ''}</option>`).join('');
    activeSelect.value = selectedId || '';
  }
  if (filterSelect) {
    const current = filterSelect.value;
    filterSelect.innerHTML = '<option value="">Brand: All Brands</option>' + state.brands.map((brand) => `<option value="${brand.id}">${esc(brand.name)}</option>`).join('');
    filterSelect.value = current;
  }
}

async function loadItems() {
  $('#item-table-body').innerHTML = '<tr><td colspan="7" class="loading-row">Loading menu items…</td></tr>';
  await fetchCatalog();
  populateBrandSelects();
  renderItemTable();
}

function setBrandPreview(data, count = 0) {
  const name = String(data.name || '').trim();
  const status = data.status || 'active';
  $('#brand-preview-logo').textContent = name ? name.slice(0, 2).toUpperCase() : 'SB';
  $('#brand-preview-name').textContent = name || 'Brand name';
  $('#brand-preview-description').textContent = data.description || 'Your brand description will appear here.';
  $('#brand-preview-items').textContent = count;
  const badge = $('#brand-preview-status');
  badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
  badge.className = `status-pill ${status}`;
  $('#brand-preview-note').textContent = status === 'active'
    ? 'This brand is currently active and available for ordering.'
    : 'This brand is inactive and is hidden from customer ordering.';
}

function setFormMessage(selector, message = '', error = false) {
  const element = $(selector);
  if (!element) return;
  element.textContent = message;
  element.classList.toggle('error', error);
}

async function loadBrandEditor() {
  const form = $('#brand-editor');
  const page = $('.brand-form-layout');
  const brandId = page.dataset.brandId;
  let brand = { name: '', description: '', status: 'active' };
  let count = 0;
  if (brandId) {
    $('#brand-save').disabled = true;
    setFormMessage('#brand-form-message', 'Loading brand details…');
    const response = await get(`/brands/${encodeURIComponent(brandId)}`);
    brand = response;
    count = Array.isArray(response.menu_items) ? response.menu_items.length : 0;
    form.elements.name.value = response.name || '';
    form.elements.description.value = response.description || '';
    form.elements.status.value = response.status === 'active' ? 'active' : 'inactive';
    $('#brand-page-title').textContent = 'Edit Brand';
    $('#brand-breadcrumb').textContent = 'Edit Brand';
    $('#brand-save').disabled = false;
    setFormMessage('#brand-form-message');
  }
  $('#brand-description-count').textContent = `${form.elements.description.value.length} / 500`;
  setBrandPreview(brand, count);
}

function updateBrandPreview() {
  const form = $('#brand-editor');
  const data = Object.fromEntries(new FormData(form));
  const count = Number($('#brand-preview-items').textContent) || 0;
  setBrandPreview(data, count);
  const feedback = $('#brand-name-feedback');
  if (form.elements.name.value.trim()) {
    feedback.textContent = '✓ Brand name is ready to save.';
    feedback.classList.remove('error');
  } else {
    feedback.textContent = '';
  }
  $('#brand-description-count').textContent = `${form.elements.description.value.length} / 500`;
}

async function saveBrand(event) {
  event.preventDefault();
  const form = event.currentTarget;
  if (!form.reportValidity()) return;
  if (!form.elements.name.value.trim()) {
    $('#brand-name-feedback').textContent = 'Brand name is required.';
    $('#brand-name-feedback').classList.add('error');
    form.elements.name.focus();
    return;
  }
  const button = $('#brand-save');
  const page = $('.brand-form-layout');
  const id = page.dataset.brandId;
  const data = {
    name: form.elements.name.value.trim(),
    description: form.elements.description.value.trim() || null,
    status: form.elements.status.value,
  };
  button.disabled = true;
  button.textContent = 'Saving…';
  setFormMessage('#brand-form-message', 'Saving brand…');
  try {
    const brand = id ? await put(`/brands/${encodeURIComponent(id)}`, data) : await post('/brands', data);
    if (!id && brand?.id) {
      page.dataset.brandId = brand.id;
      $('#brand-page-title').textContent = 'Edit Brand';
      $('#brand-breadcrumb').textContent = 'Edit Brand';
      window.history.replaceState({}, '', `/admin/catalog/brands/${encodeURIComponent(brand.id)}/edit`);
    }
    toast(id ? 'Brand updated successfully.' : 'Brand created successfully.');
    setFormMessage('#brand-form-message', 'Changes saved successfully.');
    setBrandPreview(brand, Number($('#brand-preview-items').textContent) || 0);
  } catch (error) {
    setFormMessage('#brand-form-message', error.message, true);
    $('#brand-name-feedback').textContent = error.message;
    $('#brand-name-feedback').classList.add('error');
    toast(error.message, true);
  } finally {
    button.disabled = false;
    button.textContent = page.dataset.brandId ? '✓ Save Changes' : '✓ Save Brand';
  }
}

function resetItemForm() {
  const form = $('#item-editor');
  form.reset();
  form.elements.id.value = '';
  form.elements.price.value = '0';
  form.elements.is_available.value = 'true';
  $('#item-form-title').textContent = 'Add Menu Item';
  $('#item-save').textContent = '✓ Save Item';
  $('#item-description-count').textContent = '0 / 500';
  setFormMessage('#item-form-message');
}

function editItem(item) {
  const form = $('#item-editor');
  form.elements.id.value = item.id;
  form.elements.name.value = item.name || '';
  populateBrandSelects(item.brand_id);
  form.elements.description.value = item.description || '';
  form.elements.price.value = Number(item.price) || 0;
  form.elements.is_available.value = item.is_available ? 'true' : 'false';
  $('#item-form-title').textContent = 'Edit Menu Item';
  $('#item-save').textContent = '✓ Save Changes';
  $('#item-description-count').textContent = `${form.elements.description.value.length} / 500`;
  setFormMessage('#item-form-message', `Editing ${item.name}`);
  $('#item-editor-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  form.elements.name.focus({ preventScroll: true });
}

async function saveItem(event) {
  event.preventDefault();
  const form = event.currentTarget;
  if (!form.reportValidity()) return;
  if (!form.elements.name.value.trim()) {
    setFormMessage('#item-form-message', 'Menu item name is required.', true);
    form.elements.name.focus();
    return;
  }
  const button = $('#item-save');
  const id = form.elements.id.value;
  const data = {
    name: form.elements.name.value.trim(),
    brand_id: Number(form.elements.brand_id.value),
    description: form.elements.description.value.trim() || null,
    price: Number(form.elements.price.value),
    is_available: form.elements.is_available.value === 'true',
  };
  button.disabled = true;
  button.textContent = 'Saving…';
  setFormMessage('#item-form-message', 'Saving menu item…');
  try {
    if (id) await put(`/menu-items/${encodeURIComponent(id)}`, data);
    else await post('/menu-items', data);
    await loadItems();
    resetItemForm();
    toast(id ? 'Menu item updated successfully.' : 'Menu item created successfully.');
  } catch (error) {
    setFormMessage('#item-form-message', error.message, true);
    toast(error.message, true);
  } finally {
    button.disabled = false;
    button.textContent = form.elements.id.value ? '✓ Save Changes' : '✓ Save Item';
  }
}

function showModal(title, body) {
  $('#detail-modal-title').textContent = title;
  $('#detail-modal-body').innerHTML = body;
  bootstrap.Modal.getOrCreateInstance($('#detail-modal')).show();
}

async function viewBrand(id) {
  const brand = await get(`/brands/${encodeURIComponent(id)}`);
  const items = Array.isArray(brand.menu_items) ? brand.menu_items : [];
  const itemRows = items.map((item) => `<li><span>${esc(item.name)} ${statusPill(item.is_available ? 'Available' : 'Unavailable', item.is_available ? 'available' : 'unavailable')}</span><small>${money(item.price)}</small></li>`).join('');
  showModal(brand.name, `<div class="detail-grid"><span>Description</span><b>${esc(brand.description || 'No description')}</b><span>Status</span><b>${statusPill(brand.status, brand.status)}</b><span>Menu items</span><b>${items.length}</b></div><ul class="detail-menu-list">${itemRows || '<li>No menu items assigned.</li>'}</ul>`);
}

function viewItem(item) {
  const brand = item.brand?.name || state.brands.find((entry) => Number(entry.id) === Number(item.brand_id))?.name || '—';
  showModal(item.name, `<div class="detail-grid"><span>Brand</span><b>${esc(brand)}</b><span>Description</span><b>${esc(item.description || 'No description')}</b><span>Price</span><b>${money(item.price)}</b><span>Status</span><b>${statusPill(item.is_available ? 'Available' : 'Unavailable', item.is_available ? 'available' : 'unavailable')}</b></div>`);
}

async function brandAction(button) {
  const brand = state.brands.find((entry) => String(entry.id) === button.dataset.id);
  if (!brand) return;
  if (button.dataset.brandAction === 'view') {
    try { await viewBrand(brand.id); } catch (error) { toast(error.message, true); }
    return;
  }
  if (button.dataset.brandAction === 'delete') {
    if (!window.confirm(`Delete ${brand.name}? This will archive the brand so order history stays intact.`)) return;
    button.disabled = true;
    try {
      await del(`/brands/${encodeURIComponent(brand.id)}`);
      await loadBrands();
      toast(`${brand.name} archived. Existing order history was preserved.`);
    } catch (error) { button.disabled = false; toast(error.message, true); }
  }
}

async function itemAction(button) {
  const item = state.items.find((entry) => String(entry.id) === button.dataset.id);
  if (!item) return;
  const action = button.dataset.itemAction;
  if (action === 'view') { viewItem(item); return; }
  if (action === 'edit') { editItem(item); return; }
  if (action === 'delete') {
    if (!window.confirm(`Delete ${item.name}? If confirmed, it will be marked unavailable and its order history will be preserved.`)) return;
    button.disabled = true;
    try {
      await del(`/menu-items/${encodeURIComponent(item.id)}`);
      await loadItems();
      toast(`${item.name} is now unavailable.`);
    } catch (error) { button.disabled = false; toast(error.message, true); }
  }
}

const path = window.location.pathname;
if (path === '/admin/catalog') {
  loadDashboard().catch((error) => { toast(error.message, true); $('#recent-brands').innerHTML = '<div class="recent-empty">Could not load catalog data.</div>'; });
} else if (path === '/admin/catalog/brands') {
  loadBrands().catch((error) => { toast(error.message, true); $('#brand-table-body').innerHTML = showEmpty(6, 'Catalog unavailable', error.message); });
  $('#brand-search').addEventListener('input', renderBrandTable);
  $('#brand-status-filter').addEventListener('change', renderBrandTable);
  $('#brand-table-body').addEventListener('click', (event) => {
    const button = event.target.closest('[data-brand-action]');
    if (button) brandAction(button);
  });
} else if (path.startsWith('/admin/catalog/brands/') && $('#brand-editor')) {
  loadBrandEditor().catch((error) => { setFormMessage('#brand-form-message', error.message, true); toast(error.message, true); });
  $('#brand-editor').addEventListener('input', updateBrandPreview);
  $('#brand-editor').addEventListener('change', updateBrandPreview);
  $('#brand-editor').addEventListener('submit', saveBrand);
} else if (path === '/admin/catalog/items') {
  loadItems().catch((error) => { toast(error.message, true); $('#item-table-body').innerHTML = showEmpty(7, 'Catalog unavailable', error.message); });
  ['#item-search', '#item-brand-filter', '#item-status-filter'].forEach((selector) => $(selector).addEventListener(selector === '#item-search' ? 'input' : 'change', renderItemTable));
  $('#item-table-body').addEventListener('click', (event) => {
    const button = event.target.closest('[data-item-action]');
    if (button) itemAction(button);
  });
  $('#item-editor').addEventListener('submit', saveItem);
  $('#item-editor').elements.description.addEventListener('input', (event) => { $('#item-description-count').textContent = `${event.target.value.length} / 500`; });
  $('#item-cancel').addEventListener('click', resetItemForm);
  $('#focus-item-form').addEventListener('click', () => { $('#item-editor-card').scrollIntoView({ behavior: 'smooth', block: 'start' }); $('#item-name').focus({ preventScroll: true }); });
}
