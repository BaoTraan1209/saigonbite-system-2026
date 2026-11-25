import {get,post,patch} from './api.js';

export async function loadAdmin(){
  const hubs=await get('/hubs');
  document.querySelector('#hub-admin-list').innerHTML=hubs.map(h=>`<div class="record">${h.name} – ${h.status} – ${h.current_load}/${h.capacity} orders (${h.load_percent}%) <button data-id="${h.id}" data-status="active">Activate</button> <button data-id="${h.id}" data-status="suspended">Suspend</button></div>`).join('');
  document.querySelectorAll('#hub-admin-list button').forEach(b=>b.onclick=async()=>{
    try{
      await patch(`/hubs/${b.dataset.id}/status`,{status:b.dataset.status});
      await loadAdmin();
    }catch(e){alert(e.message)}
  });
  const [brands,menu]=await Promise.all([get('/brands'),get('/menu-items')]);
  document.querySelector('#catalog-list').innerHTML='<h4>Brands</h4>'+brands.map(b=>`<div class="record">${b.name} (${b.status})</div>`).join('')+'<h4>Menu items</h4>'+menu.map(m=>`<div class="record">${m.name} – ${m.price} VND</div>`).join('');
}

export function initAdmin(){
  document.querySelector('#hub-form').onsubmit=async e=>{
    e.preventDefault();
    const data=Object.fromEntries(new FormData(e.currentTarget));
    data.capacity=Number(data.capacity);
    try{
      const brands=await get('/brands');
      data.brand_ids=brands.filter(brand=>brand.status==='active').map(brand=>brand.id);
      await post('/hubs',data);
      e.currentTarget.reset();
      await loadAdmin();
    }catch(error){alert(error.message)}
  };
}
