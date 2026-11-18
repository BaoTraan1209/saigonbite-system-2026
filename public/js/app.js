const panel = document.querySelector('#panel');
async function api(path, options={}) {
  const response = await fetch('/api'+path, {
    headers:{'Accept':'application/json','Content-Type':'application/json',...(options.headers||{})},
    ...options
  });
  const data = await response.json();
  if(!response.ok) throw new Error(data.message || JSON.stringify(data.errors || data));
  return data;
}
function table(rows) {
  if(!rows || !rows.length) return '<p>No records found.</p>';
  const cols=Object.keys(rows[0]);
  return `<table><thead><tr>${cols.map(c=>`<th>${c}</th>`).join('')}</tr></thead><tbody>${rows.map(r=>`<tr>${cols.map(c=>`<td>${typeof r[c]==='object'?JSON.stringify(r[c]):(r[c]??'')}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
}
async function load(name) {
  panel.innerHTML='<p>Loading...</p>';
  try {
    if(name==='orders') panel.innerHTML='<h2>Orders</h2>'+table((await api('/orders')).data || []);
    if(name==='hubs') panel.innerHTML='<h2>Hubs</h2>'+table(await api('/hubs'));
    if(name==='brands') panel.innerHTML='<h2>Brands</h2>'+table(await api('/brands'));
    if(name==='kitchen') panel.innerHTML='<h2>Kitchen Orders</h2>'+table(await api('/kitchen/orders'));
    if(name==='deliveries') panel.innerHTML='<h2>Deliveries</h2>'+table(await api('/deliveries'));
    if(name==='reports') panel.innerHTML='<h2>Management Reports</h2><pre>'+JSON.stringify(await api('/reports/summary'),null,2)+'</pre>';
  } catch(e) { panel.innerHTML=`<p class="error">${e.message}</p>`; }
}
document.querySelectorAll('nav button').forEach(btn=>btn.addEventListener('click',()=>load(btn.dataset.panel)));
