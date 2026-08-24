# Usage

## Basic field

```php
use Nowo\PhoneInputBundle\Form\Type\PhoneType;

$builder->add('mobile', PhoneType::class);
```

## Value formats

```php
use Nowo\PhoneInputBundle\Form\ValueFormat;

// E.164 string (default): '+34612345678'
$builder->add('phone', PhoneType::class, ['value_format' => ValueFormat::CONCATENATED]);

// Array: ['iso' => 'ES', 'prefix' => '+34', 'national_number' => '612345678']
$builder->add('phone', PhoneType::class, ['value_format' => ValueFormat::SEPARATED]);

// PhoneNumber value object
$builder->add('phone', PhoneType::class, ['value_format' => ValueFormat::OBJECT]);
```

## Prefix selector options

```php
use Nowo\PhoneInputBundle\Form\PrefixDisplay;
use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;

$builder->add('phone', PhoneType::class, [
    'country_prefix_selector' => true,
    'prefix_display' => PrefixDisplay::FLAG_AND_PREFIX,
    'show_flag' => true,
    'prefix_search' => true,
    'flag_display' => FlagDisplay::CSS_ICON,
    'allowed_countries' => ['ES', 'FR', 'GB'],
    'excluded_countries' => null,
    'phone_validation' => PhoneValidationMode::COUNTRY,
]);
```

## Overriding bundle templates

Copy templates to your application:

```
templates/bundles/NowoPhoneInputBundle/Form/phone_input_widget.html.twig
templates/bundles/NowoPhoneInputBundle/Form/_phone_country_flag.html.twig
```

Symfony resolves `@NowoPhoneInputBundle/Form/...` to your copies automatically.

## CSP-safe prefix picker

The widget is `<nowo-phone-input>` (light DOM: country picker + national number). It loads an external vanilla IIFE (no Stimulus required):

```twig
<script src="{{ asset('js/nowo-phone-prefix-picker.js', 'nowo_phone_input') }}" defer></script>
```

That script defines the custom element and enhances `[data-controller="phone-prefix-picker"]` and `[data-nowo-phone-prefix-picker]`. Markup also includes Stimulus-style `data-action` attributes so hosts can register a Stimulus controller named `phone-prefix-picker` instead of the IIFE (omit the script in that case). Prefer one approach per page to avoid double-binding.

Under a nonce-based CSP, allow the script URL (or nonce on `<script src>`); do not expect inline scripts to run.

## CSS frameworks

Override CSS classes per field or globally via `container_classes`, `prefix_selector_classes` and `national_number_classes`. See the demo (`?framework=bootstrap5|tailwind2|foundation6|symfony-default`).
