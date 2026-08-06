import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: __ENV.VUS ? parseInt(__ENV.VUS, 10) : 10,
  duration: __ENV.DURATION || '30s',
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<1000'],
  },
};

const BASE_URL = (__ENV.BASE_URL || 'http://localhost:8000').replace(/\/$/, '');
const TOKEN = __ENV.TOKEN || '';

function authHeaders() {
  const headers = { Accept: 'application/json' };
  if (TOKEN) {
    headers.Authorization = `Bearer ${TOKEN}`;
  }
  return headers;
}

export default function () {
  const params = { headers: authHeaders(), timeout: '10s' };

  // List leads
  let res = http.get(`${BASE_URL}/api/leads?page=1&per_page=20`, params);
  check(res, {
    'leads list: status 200': (r) => r.status === 200,
  });

  // Search leads
  const queries = ['a', 'moh', 'cairo', 'dubai', 'gmail'];
  const q = queries[Math.floor(Math.random() * queries.length)];
  res = http.get(`${BASE_URL}/api/leads?q=${encodeURIComponent(q)}&search_mode=contains&page=1&per_page=20`, params);
  check(res, {
    'leads search: status 200': (r) => r.status === 200,
  });

  sleep(1);
}
