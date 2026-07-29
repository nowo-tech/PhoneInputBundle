# Installation

## Requirements

- **PHP** >= 8.2, < 8.6
- **Symfony** ^6.0 || ^7.0 || ^8.0
- **symfony/form**, **symfony/framework-bundle**, **symfony/twig-bundle**, **symfony/validator**
- **symfony/asset** — required to use the named package `nowo_phone_input` (`asset('css/…', 'nowo_phone_input')`)

Optional:

- **symfony/ux-icons** + **symfony/http-client** — for `flag_display: UX_ICON`
- **giggsey/libphonenumber-for-php** — optional E.164 validation when `use_libphonenumber: true`

## Install with Composer

```bash
composer require nowo-tech/phone-input-bundle
```

## Register the bundle

```php
Nowo\PhoneInputBundle\NowoPhoneInputBundle::class => ['all' => true],
```

With Symfony Flex, the bundle and `config/packages/nowo_phone_input.yaml` are installed via the recipe when available.

## Form theme

Add the widget theme in `config/packages/twig.yaml`:

```yaml
twig:
    form_themes:
        - '@NowoPhoneInputBundle/Form/phone_input_widget.html.twig'
```

Or enable `use_phone_form_theme: true` in bundle configuration (default).

## Styles

After `assets:install`, files are published under `public/bundles/nowophoneinput/`. The bundle registers a named Symfony asset package (`nowo_phone_input`), so templates should load CSS via that package instead of hard-coding the public path:

```twig
<link rel="stylesheet" href="{{ asset('css/flag-icons.min.css', 'nowo_phone_input') }}">
<link rel="stylesheet" href="{{ asset('css/phone_input.css', 'nowo_phone_input') }}">
```

## Optional UX Icons

For `flag_display: UX_ICON`:

```bash
composer require symfony/ux-icons symfony/http-client
php bin/console ux:icons:lock
```

Without UX Icons, the widget falls back to bundled CSS flag icons (`flag-icons`).
