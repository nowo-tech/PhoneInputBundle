# Configuration

Create or edit `config/packages/nowo_phone_input.yaml`:

```yaml
nowo_phone_input:
    country_prefix_selector: true
    default_country: ES
    preferred_countries: [ES, FR, PT, GB]
    allowed_countries: []
    excluded_countries: []
    value_format: CONCATENATED
    prefix_display: FLAG_AND_PREFIX
    show_flag: true
    prefix_search: true
    flag_display: CSS_ICON
    container_classes: ['input-group', 'nowo-phone-input']
    prefix_selector_classes: ['form-select', 'nowo-phone-input__prefix']
    national_number_classes: ['form-control', 'nowo-phone-input__number']
    use_phone_form_theme: true
    trim: true
    invalid_message: 'The phone number is invalid.'
    phone_validation: COUNTRY
    use_libphonenumber: true
```

See [USAGE.md](USAGE.md) for per-field overrides.

## Assets and CSP

Named package `nowo_phone_input` (`base_path` `/bundles/nowophoneinput`):

| Asset | Purpose |
|-------|---------|
| `css/flag-icons.min.css` | Country flag sprites (`flag_display: CSS_ICON`) |
| `css/phone_input.css` | Widget layout, progressive enhancement, portaled dropdown |
| `js/nowo-phone-prefix-picker.js` | CSP-safe prefix picker (vanilla IIFE; optional if you use Stimulus) |

The form theme emits the JS once per request when `app.request` is available. Hosts may also load it from the layout. Optional Stimulus peer: register controller id `phone-prefix-picker` and skip the IIFE.

## Validation modes

| Value | Behaviour |
|-------|-----------|
| `COUNTRY` | Validate national number against patterns for the selected ISO country |
| `PREFIX` | Validate using dial-prefix rules |
| `NONE` | Disable bundled validation constraint |

When `use_libphonenumber` is true and `giggsey/libphonenumber-for-php` is installed, validation uses libphonenumber instead of bundled JSON patterns.
