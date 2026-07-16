# Upgrading

This document describes how to upgrade between versions of Phone Input Bundle.

## 1.x

### 1.1.4

No application code changes required.

**Maintainers / local demos:** `demo/symfony6` was removed. Use `demo/symfony7` or `demo/symfony8` (see [demo/README.md](../demo/README.md)).

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
3. Run `php bin/console assets:install` and include `flag-icons.min.css` + `phone_input.css` in your layout.
4. (Optional) `composer require symfony/ux-icons symfony/http-client` for `flag_display: UX_ICON`.
5. (Optional) `composer require giggsey/libphonenumber-for-php` for libphonenumber validation.

See [INSTALLATION.md](INSTALLATION.md) and [USAGE.md](USAGE.md) for details.

**Demos / path repository**

If you mount the bundle source in Docker demos, use `dev-main as 1.0.99` in demo `composer.json` with `minimum-stability: dev` and `prefer-stable: true`.
