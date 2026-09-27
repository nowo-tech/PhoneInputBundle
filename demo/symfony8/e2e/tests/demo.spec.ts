import { test, expect } from '@playwright/test';

test.describe('PhoneInput demo', () => {
  test('home shows phone widget', async ({ page }) => {
    const response = await page.goto('/');
    expect(response?.ok()).toBeTruthy();
    await expect(page.locator('nowo-phone-input').first()).toBeVisible();
  });

  test('prefix toggle opens dropdown', async ({ page }) => {
    await page.goto('/');
    const host = page.locator('nowo-phone-input').first();
    await expect(host).toBeVisible();
    const picker = host.locator('[data-nowo-phone-prefix-picker]').first();
    await expect(picker).toBeVisible();
    await page.waitForFunction(() => {
      const el = document.querySelector('[data-nowo-phone-prefix-picker]') as HTMLElement | null;
      return el?.dataset.nowoPhonePickerInit === '1';
    });
    await picker.locator('.nowo-phone-input__prefix-toggle').click();
    await expect(
      page.locator('.nowo-phone-input__prefix-dropdown:not([hidden])').first(),
    ).toBeVisible({ timeout: 5000 });
  });
});
