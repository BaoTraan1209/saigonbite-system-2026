import {get} from './api.js';
export async function loadReports(){const d=await get('/reports/summary');document.querySelector('#report-result').textContent=JSON.stringify(d,null,2);}
