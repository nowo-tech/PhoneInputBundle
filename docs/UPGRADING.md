# Upgrading

## Table of contents

- [From 1.4.0 to 1.4.1](#from-140-to-141)
- [From 1.3.3 to 1.4.0](#from-133-to-140)
- [From 1.3.2 to 1.3.3](#from-132-to-133)
- [1.x](#1x)
  - [1.1.5](#115)
  - [1.1.4](#114)
  - [1.1.0](#110)
  - [1.0.0](#100)
- [Unreleased](#unreleased)
- [To 1.6.1](#to-161)
- [To 1.6.0](#to-160)
- [To 1.5.0](#to-150)
- [To 1.4.4](#to-144)
- [To 1.4.3](#to-143)
- [To 1.4.2](#to-142)
- [To 1.3.2](#to-132)
- [To 1.3.1](#to-131)
- [To 1.3.0](#to-130)
- [To 1.2.1](#to-121)
- [To 1.2.0](#to-120)
  - [Twig Extra Bundle (REQ-TWIG-004)](#twig-extra-bundle-req-twig-004)
  - [Twig-CS-Fixer (maintainers)](#twig-cs-fixer-maintainers)

## From 1.4.0 to 1.4.1

FrankenPHP worker / `FRANKENPHP_RESET_KERNEL=false` hardening. **No required host changes.**

Confirmed compatible with FrankenPHP **worker** mode when the host runs with **`FRANKENPHP_RESET_KERNEL=false`** (sticky Kernel). See [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md).

```bash
composer update nowo-tech/phone-input-bundle
```

## From 1.3.3 to 1.4.0

The widget outer host is now `<nowo-phone-input>` (light DOM). Country picker behaviour is unchanged. If you copied the form theme, wrap the compound field in `<nowo-phone-input>`.

```bash
composer update nowo-tech/phone-input-bundle
php bin/console assets:install
```

## From 1.3.2 to 1.3.3

No breaking changes. **No application upgrade steps.**

```bash
composer update nowo-tech/phone-input-bundle
```

# Upgrading

This document describes how to upgrade between versions of Phone Input Bundle.

## 1.x

### 1.1.5

**Assets:** the bundle registers the named package `nowo_phone_input` and requires `symfony/asset`. Prefer:

```twig
{{ asset('css/phone_input.css', 'nowo_phone_input') }}
```

instead of hard-coded `bundles/nowophoneinput/…` paths. Run `assets:install` and clear cache after upgrade.

**Demos / FrankenPHP:** Symfony 8 demo image uses PHP 8.5; set `FRANKENPHP_MODE=classic|worker` (default `worker`) in the demo `.env`.

No other intentional breaking changes for application installs. Continue requiring `nowo-tech/phone-input-bundle: ^1.1`.

### 1.1.4

No application code changes required.

**Maintainers / local demos:** `demo/symfony8` was removed. Use `demo/symfony8` or `demo/symfony8` (see [demo/README.md](../demo/README.md)).

### 1.1.0

**Breaking:** requires PHP **>= 8.2** (bundle uses `readonly` classes).

If you are on PHP 8.1, upgrade PHP before updating the bundle:

```bash
composer require nowo-tech/phone-input-bundle:^1.1
```

### 1.0.0

First release — no prior versions to upgrade from.

**Requirements** (at first release; use **^1.1** for the supported PHP floor)

- PHP >= 8.2, < 8.6 (1.0.x composer.json listed 8.1; 1.1.0 aligns requirement with `readonly` classes)
- Symfony ^6.0 || ^7.0 || ^8.0
- `symfony/validator`

**Install**

```bash
composer require nowo-tech/phone-input-bundle:^1.0
```

**Integration checklist**

1. Register `NowoPhoneInputBundle` in `config/bundles.php` (or use Flex).
2. Add the form theme `@NowoPhoneInputBundle/Form/phone_input_widget.html.twig` (or set `use_phone_form_theme: true`).
3. Run `php bin/console assets:install` and include CSS via `asset('css/…', 'nowo_phone_input')` (see [INSTALLATION.md](INSTALLATION.md)).
4. (Optional) `composer require symfony/ux-icons symfony/http-client` for `flag_display: UX_ICON`.
5. (Optional) `composer require giggsey/libphonenumber-for-php` for libphonenumber validation.

See [INSTALLATION.md](INSTALLATION.md) and [USAGE.md](USAGE.md) for details.

**Demos / path repository**

If you mount the bundle source in Docker demos, use `dev-main as 1.0.99` in demo `composer.json` with `minimum-stability: dev` and `prefer-stable: true`.

## Unreleased

- **Widget texts in the bundle domain:** the prefix selector label, search placeholder and empty state are translated
  in `NowoPhoneInputBundle` (keys `phone_input.prefix_label`, `phone_input.search_placeholder`,
  `phone_input.no_countries`; en, es, fr, de, it, pt, nl), no longer as English literals in the field domain. If you
  translated those literals in your own domain, move them to `translations/NowoPhoneInputBundle.<locale>.yaml` under
  the new keys. Overrides of `phone_input_widget.html.twig` keep working.

## To 1.6.1

From **1.6.0** — typed international prefixes outside the selector countries are rejected.

```bash
composer update nowo-tech/phone-input-bundle
```

- With the prefix selector and `allowed_countries` / `excluded_countries`, a number typed with the `+` prefix of a country outside the selector list is now rejected (field invalid) instead of being saved with that country.

## To 1.6.0

From **1.5.0** — shared dial codes keep the selected country; Symfony >=6.4.

```bash
composer update nowo-tech/phone-input-bundle
```

- Requires Symfony 6.4+ on the 6.x line (6.0–6.3 are no longer allowed).
- For numbers whose dial code is shared by several countries, the resolved ISO now follows the selected country / `default_country` / preferred countries instead of the first catalog entry. With the prefix selector, a number typed with another country's `+` prefix is saved with that prefix.

## To 1.5.0

From **1.4.4** — fixes - typed international prefix kept, UX Icons renderer and form theme wired.

```bash
composer update nowo-tech/phone-input-bundle
```

- Without the prefix selector, numbers typed with an international prefix (e.g. `+44…` with default `ES`) are no longer rewritten to the default country.
- `use_phone_form_theme: true` (default) now registers the widget theme in `twig.form_themes`; set it to `false` to manage the theme yourself.
- `flag_display: UX_ICON` now really renders through UX Icons when that bundle is installed.

## To 1.4.4

No action required (see [Security — CSP](SECURITY.md#content-security-policy-csp)).

## To 1.4.3

From **1.4.2** — dependency updates and demo Playwright e2e (REQ-DEMO-013).

```bash
composer update nowo-tech/phone-input-bundle
```

No breaking changes. No application upgrade steps.

## To 1.4.2

From **1.4.1** — REQ-CS-008 Igor FrankenPHP worker audit (igor-php require-dev, igor.json, make igor).

```bash
composer update nowo-tech/phone-input-bundle
php bin/console cache:clear
```

- No application upgrade steps for require-dev Igor tooling (REQ-CS-008). Consumers do not pull `igor-php/igor-php` transitively.

## To 1.3.2

From **1.3.1** — No application upgrade steps.

```bash
composer update nowo-tech/phone-input-bundle
php bin/console cache:clear
```

## To 1.3.1

From **1.3.0** — No application upgrade steps. **Demos only:** Hot Reload Bundle `^1.4` (FrankenPHP Mercure/`hot_reload`, `dev`/`test`). Shipped demos are Symfony 8 only (Symfony 6/7 demo apps removed).

```bash
composer update nowo-tech/phone-input-bundle
```

## To 1.3.0

**CSP / JavaScript:** the widget no longer embeds an inline `<script>` (blocked when `script-src` uses nonces). After `assets:install`, the form theme loads:

```twig
<script src="{{ asset('js/nowo-phone-prefix-picker.js', 'nowo_phone_input') }}" defer></script>
```

once per request when possible. Ensure `public/bundles/nowophoneinput/js/` is published and that CSP allows that script URL (or a nonce on `<script src>`).

**Progressive enhancement:** until the picker JS runs, the native country `<select>` stays visible; the custom toggle appears only after enhancement.

**Optional Stimulus:** markup keeps `data-controller="phone-prefix-picker"` and `data-action` hooks. Hosts that prefer Stimulus may omit the IIFE (override the widget or strip the script tag) and register a Stimulus controller named `phone-prefix-picker` instead. Do not run both on the same element.

`PhoneType` options are unchanged.

```bash
composer update nowo-tech/phone-input-bundle
php bin/console assets:install
php bin/console cache:clear
```

## To 1.2.1

No application code changes required. Patch release restores CI coverage gate (100% PHPUnit coverage on `src/`).

```bash
composer update nowo-tech/phone-input-bundle
```

## To 1.2.0

From **1.1.5** — Adds required Twig Extra (REQ-TWIG-004) and Twig-CS-Fixer. Register TwigExtraBundle if Flex did not.

```bash
composer update nowo-tech/phone-input-bundle
php bin/console cache:clear
```

### Twig Extra Bundle (REQ-TWIG-004)

Hosts that render this bundle's Twig templates must install:

```bash
composer require twig/extra-bundle twig/string-extra
```

and enable `Twig\Extra\TwigExtraBundle\TwigExtraBundle`. Flex recipes usually register it automatically.

### Twig-CS-Fixer (maintainers)

Package maintainers: `composer twig:lint` / `composer twig:fix` use `.twig-cs-fixer.php` over `src/` (and `templates/` when present).

