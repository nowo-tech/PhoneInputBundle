import { test, expect } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { resolve } from 'node:path';

/**
 * REQ-DEMO-013 — full demo frame: navbar + demo card (header + first use-case)
 * + Symfony WebProfiler toolbar.
 *
 * Playwright clips to the viewport, so we enlarge the viewport before capture.
 */
const outDir = process.env.SCREENSHOT_DIR
  ? resolve(process.env.SCREENSHOT_DIR)
  : resolve(__dirname, '../../../../docs/images/demo');

type Box = { x: number; y: number; width: number; height: number };

async function boxOf(
  page: import('@playwright/test').Page,
  selector: string,
): Promise<Box | null> {
  const loc = page.locator(selector).first();
  if ((await loc.count()) === 0) {
    return null;
  }
  return loc.boundingBox();
}

/** Navbar → header/framework/first field (+ optional dropdown) → profiler. */
async function clipDemoFrame(
  page: import('@playwright/test').Page,
  extra?: Box | null,
): Promise<Box> {
  const nav = await boxOf(page, 'nav.navbar');
  const header = await boxOf(page, '.demo-header');
  const switcher = await boxOf(page, '.demo-framework-switcher');
  const section = await boxOf(page, '.section-heading');
  const field = await boxOf(page, '.field-card');
  const profiler =
    (await boxOf(page, '.sf-toolbar')) ?? (await boxOf(page, '.sf-minitoolbar'));
  if (!nav || !field) {
    throw new Error('Missing nav.navbar or .field-card for PhoneInput screenshot clip');
  }
  const boxes = [nav, header, switcher, section, field, extra, profiler].filter(Boolean) as Box[];
  const x = Math.min(...boxes.map((b) => b.x));
  const y = Math.min(...boxes.map((b) => b.y));
  const right = Math.max(...boxes.map((b) => b.x + b.width));
  const bottom = Math.max(...boxes.map((b) => b.y + b.height));
  const pad = 8;
  return {
    x: Math.max(0, x - pad),
    y: Math.max(0, y - pad),
    width: right - x + pad * 2,
    height: bottom - y + pad * 2,
  };
}

async function prepareDemoPage(page: import('@playwright/test').Page) {
  await page.setViewportSize({ width: 1280, height: 1600 });
  await page.goto('/');
  await expect(page.locator('nav.navbar .navbar-brand')).toBeVisible();
  await expect(page.locator('.field-card nowo-phone-input').first()).toBeVisible();
  await page
    .locator('.sf-toolbar .sf-toolbar-block, .sf-toolbar-status, .sf-minitoolbar')
    .first()
    .waitFor({ state: 'visible', timeout: 10000 })
    .catch(() => {});
}

test.beforeAll(() => {
  mkdirSync(outDir, { recursive: true });
});

test.describe('PhoneInput screenshots (full demo context)', () => {
  test('overview — navbar + first field (closed prefix)', async ({ page }) => {
    await prepareDemoPage(page);
    const clip = await clipDemoFrame(page);
    if (clip.height >= 700) {
      await page.screenshot({ path: resolve(outDir, 'overview.png'), clip });
    } else {
      await page.screenshot({ path: resolve(outDir, 'overview.png'), fullPage: true });
    }
  });

  test('interaction — navbar + first field with prefix open', async ({ page }) => {
    await prepareDemoPage(page);
    const field = page.locator('.field-card').first();
    const host = field.locator('nowo-phone-input').first();
    const picker = host.locator('[data-nowo-phone-prefix-picker]').first();
    await page.waitForFunction(() => {
      const el = document.querySelector('[data-nowo-phone-prefix-picker]') as HTMLElement | null;
      return el?.dataset.nowoPhonePickerInit === '1';
    });
    await picker.locator('.nowo-phone-input__prefix-toggle').click();
    const dropdown = page.locator('.nowo-phone-input__prefix-dropdown:not([hidden])').first();
    await expect(dropdown).toBeVisible({ timeout: 5000 });
    const dropBox = await dropdown.boundingBox();
    const clip = await clipDemoFrame(page, dropBox);
    if (clip.height >= 700) {
      await page.screenshot({ path: resolve(outDir, 'interaction.png'), clip });
    } else {
      await page.screenshot({ path: resolve(outDir, 'interaction.png'), fullPage: true });
    }
  });
});
