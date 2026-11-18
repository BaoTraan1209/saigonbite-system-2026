const API = '/api';
export async function request(path, options={}) {
  const response = await fetch(`${API}${path}`, {headers:{'Content-Type':'application/json','X-User-Role':sessionStorage.getItem('role')||'Admin',...options.headers},...options});
  const body = await response.json();
  if (!response.ok || !body.success) throw new Error(body.message || 'Request failed');
  return body.data;
}
export const get=(p)=>request(p);
export const post=(p,d)=>request(p,{method:'POST',body:JSON.stringify(d)});
export const put=(p,d)=>request(p,{method:'PUT',body:JSON.stringify(d)});
