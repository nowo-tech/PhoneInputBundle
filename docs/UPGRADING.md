# Upgrading

This document describes how to upgrade between versions of Phone Input Bundle.

## 1.x

### 1.0.0

First release — no prior versions to upgrade from.

**Requirements**

- PHP >= 8.1, < 8.6
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
