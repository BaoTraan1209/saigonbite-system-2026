const API_BASE = '/api';

export async function request(path, options = {}) {
  const response = await fetch(`${API_BASE}${path}`, {
    ...options,
    headers: {
      Accept: 'application/json',
      ...(document.querySelector('meta[name="csrf-token"]')?.content ? { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } : {}),
      ...(options.body ? { 'Content-Type': 'application/json' } : {}),
      ...options.headers,
    },
  });
  const text = await response.text();
  let body = null;
  try { body = text ? JSON.parse(text) : null; } catch { body = text; }
  if (!response.ok) {
    const errors = body?.errors ? Object.values(body.errors).flat().join('\n') : '';
    throw new Error(body?.message || errors || `Request failed (${response.status})`);
  }
  return body?.success === true && Object.hasOwn(body, 'data') ? body.data : body;
}

export const get = (path) => request(path);
export const post = (path, data) => request(path, { method: 'POST', body: JSON.stringify(data) });
export const put = (path, data) => request(path, { method: 'PUT', body: JSON.stringify(data) });
export const patch = (path, data) => request(path, { method: 'PATCH', body: JSON.stringify(data) });
export const del = (path) => request(path, { method: 'DELETE' });
