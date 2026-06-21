# Installation

## Requirements

- **PHP** >= 8.1, < 8.6
- **Symfony** ^6.0 || ^7.0 || ^8.0
- **symfony/form**, **symfony/framework-bundle**, **symfony/twig-bundle**, **symfony/validator**

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

After `assets:install`, include in your layout:

```html
<link rel="stylesheet" href="{{ asset('bundles/nowophoneinput/css/flag-icons.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/nowophoneinput/css/phone_input.css') }}">
```

## Optional UX Icons

For `flag_display: UX_ICON`:

```bash
composer require symfony/ux-icons symfony/http-client
php bin/console ux:icons:lock
```

Without UX Icons, the widget falls back to bundled CSS flag icons (`flag-icons`).
