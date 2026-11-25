const API = '/api';
export async function request(path, options={}) {
  const response = await fetch(`${API}${path}`, {headers:{'Accept':'application/json','Content-Type':'application/json',...options.headers},...options});
  const body = await response.json();
  if (!response.ok) {
    const errors = body.errors ? Object.values(body.errors).flat().join('\n') : '';
    throw new Error(body.message || errors || 'Request failed');
  }
  return body.success === true && Object.hasOwn(body, 'data') ? body.data : body;
}
export const get=(p)=>request(p);
export const post=(p,d)=>request(p,{method:'POST',body:JSON.stringify(d)});
export const put=(p,d)=>request(p,{method:'PUT',body:JSON.stringify(d)});
export const patch=(p,d)=>request(p,{method:'PATCH',body:JSON.stringify(d)});
