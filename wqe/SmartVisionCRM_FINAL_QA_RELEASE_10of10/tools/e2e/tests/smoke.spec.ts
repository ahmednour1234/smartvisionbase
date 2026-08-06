import { test, expect } from '@playwright/test';

test('admin login page loads', async ({ page }) => {
  const res = await page.goto('/admin/login', { waitUntil: 'domcontentloaded' });
  expect(res?.ok()).toBeTruthy();
  await expect(page.locator('input[name="email"]')).toBeVisible();
  await expect(page.locator('input[name="password"]')).toBeVisible();
});
