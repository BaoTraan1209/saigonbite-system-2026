import {initOrder} from './order.js';
import {loadKitchen} from './kitchen.js';
import {loadDelivery} from './delivery.js';
import {initAdmin,loadAdmin} from './admin.js';
import {loadReports} from './reports.js';
const views=[...document.querySelectorAll('.panel')];
document.querySelectorAll('[data-view]').forEach(b=>b.addEventListener('click',async()=>{views.forEach(v=>v.classList.toggle('hidden',v.id!==b.dataset.view));try{if(b.dataset.view==='order')await initOrder();if(b.dataset.view==='kitchen')await loadKitchen();if(b.dataset.view==='delivery')await loadDelivery();if(b.dataset.view==='admin'){initAdmin();await loadAdmin();}if(b.dataset.view==='reports')await loadReports();}catch(e){alert(e.message)}}));
initOrder().catch(console.error);
