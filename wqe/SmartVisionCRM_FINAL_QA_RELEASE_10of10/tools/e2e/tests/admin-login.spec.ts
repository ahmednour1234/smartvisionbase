import { test, expect } from '@playwright/test';

test('admin login page loads', async ({ page }) => {
  const res = await page.goto('/admin/login', { waitUntil: 'domcontentloaded' });
  expect(res?.ok()).toBeTruthy();

  // Filament login page fields
  await expect(page.locator('input[name="email"]')).toBeVisible();
  await expect(page.locator('input[name="password"]')).toBeVisible();
});
