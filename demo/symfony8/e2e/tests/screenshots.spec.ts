import { test, expect } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { resolve } from 'node:path';

/**
 * REQ-DEMO-013 — demo header + first use-case field (title + phone widget in situ),
 * not a naked control and not the full multi-section page.
 */
const outDir = process.env.SCREENSHOT_DIR
  ? resolve(process.env.SCREENSHOT_DIR)
  : resolve(__dirname, '../../../../docs/images/demo');

type Box = { x: number; y: number; width: number; height: number };

async function boxOf(
  page: import('@playwright/test').Page,
  selector: string,
): Promise<Box | null> {
  return page.locator(selector).first().boundingBox();
}

/** Union of header → first field-card (optionally + open dropdown). */
async function clipUseCase(
  page: import('@playwright/test').Page,
  extra?: Box | null,
): Promise<Box> {
  const header = await boxOf(page, '.demo-header');
  const section = await boxOf(page, '.section-heading');
  const field = await boxOf(page, '.field-card');
  if (!header || !field) {
    throw new Error('Missing .demo-header or .field-card for screenshot clip');
  }
  const boxes = [header, section, field, extra].filter(Boolean) as Box[];
  const x = Math.min(...boxes.map((b) => b.x));
  const y = Math.min(...boxes.map((b) => b.y));
  const right = Math.max(...boxes.map((b) => b.x + b.width));
  const bottom = Math.max(...boxes.map((b) => b.y + b.height));
  return { x, y, width: right - x, height: bottom - y };
}

/** Hide Symfony Web Profiler so crops stay product-only (REQ-DEMO-013). */
async function hideDemoChrome(page: import('@playwright/test').Page) {
  await page.addStyleTag({
    content:
      '.sf-toolbar, .sf-minitoolbar, [id^="sfwdt"] { display: none !important; visibility: hidden !important; }',
  });
}

test.beforeAll(() => {
  mkdirSync(outDir, { recursive: true });
});

test.describe('PhoneInput screenshots (use-case context)', () => {
  test('overview — header + first field (closed prefix)', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('.demo-header')).toBeVisible();
    await expect(page.locator('.field-card nowo-phone-input').first()).toBeVisible();
    await hideDemoChrome(page);
    const clip = await clipUseCase(page);
    await page.screenshot({ path: resolve(outDir, 'overview.png'), clip });
  });

  test('interaction — header + first field with prefix open', async ({ page }) => {
    await page.goto('/');
    const field = page.locator('.field-card').first();
    await expect(field).toBeVisible();
    const host = field.locator('nowo-phone-input').first();
    const picker = host.locator('[data-nowo-phone-prefix-picker]').first();
    await page.waitForFunction(() => {
      const el = document.querySelector('[data-nowo-phone-prefix-picker]') as HTMLElement | null;
      return el?.dataset.nowoPhonePickerInit === '1';
    });
    await picker.locator('.nowo-phone-input__prefix-toggle').click();
    const dropdown = page.locator('.nowo-phone-input__prefix-dropdown:not([hidden])').first();
    await expect(dropdown).toBeVisible({ timeout: 5000 });
    await hideDemoChrome(page);
    const dropBox = await dropdown.boundingBox();
    const clip = await clipUseCase(page, dropBox);
    await page.screenshot({ path: resolve(outDir, 'interaction.png'), clip });
  });
});