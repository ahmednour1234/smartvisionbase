import { defineConfig } from '@playwright/test';

const baseURL = process.env.BASE_URL || 'http://127.0.0.1:8000';

export default defineConfig({
  testDir: './tests',
  timeout: 60_000,
  use: {
    baseURL,
    trace: 'on-first-retry',
  },
});
