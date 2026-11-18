import {get,post} from './api.js';
let hubs=[],brands=[],menu=[];
const $=s=>document.querySelector(s);
export async function initOrder(){
 hubs=await get('/hubs?available=1');$('#hub-select').innerHTML=hubs.map(h=>`<option value="${h.id}">${h.name} (${h.load_percent}% load)</option>`).join('');
 await loadBrands();$('#hub-select').onchange=loadBrands;$('#brand-select').onchange=loadMenu;
 $('#order-form').onsubmit=submitOrder;
}
async function loadBrands(){brands=await get(`/brands?hub_id=${$('#hub-select').value}`);$('#brand-select').innerHTML=brands.map(b=>`<option value="${b.id}">${b.name}</option>`).join('');await loadMenu();}
async function loadMenu(){menu=await get(`/menu-items?hub_id=${$('#hub-select').value}&brand_id=${$('#brand-select').value}`);$('#menu-list').innerHTML=menu.map(m=>`<label class="item"><span>${m.name} – ${Number(m.price).toLocaleString()} VND</span><input type="number" min="0" value="0" data-menu-id="${m.id}" ${m.is_available?'':'disabled'}></label>`).join('');}
async function submitOrder(e){e.preventDefault();const f=new FormData(e.currentTarget);const items=[...document.querySelectorAll('[data-menu-id]')].map(x=>({menu_item_id:Number(x.dataset.menuId),quantity:Number(x.value)})).filter(x=>x.quantity>0);if(!items.length){alert('Select at least one menu item');return;}const payload=Object.fromEntries(f.entries());payload.hub_id=Number(payload.hub_id);payload.brand_id=Number(payload.brand_id);payload.items=items;try{const result=await post('/orders',payload);$('#order-result').textContent=JSON.stringify(result,null,2);e.currentTarget.reset();await initOrder();}catch(err){alert(err.message);}}
