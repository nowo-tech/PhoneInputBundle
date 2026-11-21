import { test, expect } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { resolve } from 'node:path';

/**
 * REQ-DEMO-013 — the demo platform shell with the first use-case (no Symfony WebProfiler toolbar).
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

/** Demo platform top bar → page header → first field (+ optional dropdown); the Symfony toolbar is hidden. */
async function clipDemoFrame(
  page: import('@playwright/test').Page,
  extra?: Box | null,
): Promise<Box> {
  const field = await boxOf(page, '[data-testid="demo-field"]');
  if (!field) {
    throw new Error('Missing demo field for PhoneInput screenshot clip');
  }
  const boxes = [field, extra].filter(Boolean) as Box[];
  const bottom = Math.max(...boxes.map((b) => b.y + b.height));
  const width = page.viewportSize()?.width ?? 1280;
  const pad = 16;

  return { x: 0, y: 0, width, height: bottom + pad };
}

async function prepareDemoPage(page: import('@playwright/test').Page) {
  await page.setViewportSize({ width: 1280, height: 1600 });
  await page.goto('/');
  await page.addStyleTag({ content: '.sf-toolbar,.sf-minitoolbar,[id^="sfwdt"]{display:none!important}' });
  await expect(page.getByTestId('demo-platform')).toBeVisible();
  await expect(page.locator('[data-testid="demo-field"] nowo-phone-input').first()).toBeVisible();
}

test.beforeAll(() => {
  mkdirSync(outDir, { recursive: true });
});

test.describe('PhoneInput screenshots (full demo context)', () => {
  test('overview — navbar + first field (closed prefix)', async ({ page }) => {
    await prepareDemoPage(page);
    const clip = await clipDemoFrame(page);
    // Always clip to the demo frame (a full-page capture shows all 20 use-cases).
    await page.screenshot({ path: resolve(outDir, 'overview.png'), clip });
  });

  test('interaction — navbar + first field with prefix open', async ({ page }) => {
    await prepareDemoPage(page);
    const field = page.locator('[data-testid="demo-field"]').first();
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
    // Always clip to the demo frame (a full-page capture shows all 20 use-cases).
    await page.screenshot({ path: resolve(outDir, 'interaction.png'), clip });
  });
});
