# Feature Specification: PhoneInputBundle baseline (100% code coverage)

**Feature Branch**: `001-baseline`  
**Created**: 2026-07-07  
**Status**: Active  
**Input**: Backfill GitHub Spec Kit baseline documenting 100% of production code in `src/`.

**Related docs**: [`docs/SPEC-DRIVEN-DEVELOPMENT.md`](../../docs/SPEC-DRIVEN-DEVELOPMENT.md), [`docs/CONFIGURATION.md`](../../docs/CONFIGURATION.md), [`docs/USAGE.md`](../../docs/USAGE.md)  
**Code inventory (traceability)**: [`code-inventory.md`](code-inventory.md)

---

## Summary

**Package**: `nowo-tech/phone-input-bundle`  
**Configuration root**: `nowo_phone_input`

Symfony bundle providing **`PhoneType`**: an extension of Symfony `TelType` with optional country prefix selector (flags, dial codes, search), flexible value formats (E.164 string, separated array, or `PhoneNumber` value object), validation modes, and CSS/Twig assets for Bootstrap/Tailwind/Foundation layouts. Symfony 6|7|8 · PHP 8.2+.

---

## User Scenarios & Testing

### User Story 1 — Phone field with country prefix (Priority: P1)

As an integrator, I add `PhoneType` to a form so users pick a country prefix with flags and enter a national number in one control.

**Independent Test**: Render demo form with default config; prefix dropdown shows dial codes; selecting a country updates hidden ISO field; submitted model matches configured `value_format`.

**Acceptance Scenarios**:

1. **Given** `country_prefix_selector=true`, **When** the field renders, **Then** Twig widget shows prefix selector + national `TelType` input with configured CSS classes.
2. **Given** `preferred_countries` or `allowed_countries`, **When** choices build, **Then** `CountryProvider` filters/sorts the catalog accordingly.
3. **Given** `prefix_search=true`, **When** the dropdown opens, **Then** client-side search filters countries by name, ISO, or dial code.

---

### User Story 2 — Value formats (Priority: P1)

As an integrator, I choose how the form maps view data to the domain: concatenated E.164, separated array, or `PhoneNumber` object.

**Acceptance Scenarios**:

1. **Given** `value_format=CONCATENATED`, **When** form submits valid input, **Then** model data is a single E.164 string (e.g. `+34600111222`).
2. **Given** `value_format=SEPARATED`, **When** form submits, **Then** model is `['country_iso' => 'ES', 'national_number' => '600111222', 'dial_code' => '+34']` (shape documented in USAGE).
3. **Given** `value_format=OBJECT`, **When** form submits, **Then** model is a `PhoneNumber` instance with accessors for ISO, national number, and E.164.

---

### User Story 3 — Validation (Priority: P2)

As an integrator, I enforce valid national numbers using country ISO rules, dial-prefix rules, libphonenumber when installed, or disable validation.

**Acceptance Scenarios**:

1. **Given** `phone_validation=COUNTRY`, **When** national number fails pattern/libphonenumber check for selected ISO, **Then** `ValidPhoneNumber` constraint fails with configured `invalid_message`.
2. **Given** `use_libphonenumber=true` and package installed, **When** validating, **Then** `PhoneValidator` delegates to giggsey/libphonenumber-for-php.
3. **Given** `phone_validation=NONE`, **When** form submits non-empty input, **Then** only basic type/not-blank constraints apply.

---

### User Story 4 — Flag rendering modes (Priority: P2)

As an integrator, I configure how flags appear: CSS sprites, emoji, UX Icons SVG, or hidden.

**Acceptance Scenarios**:

1. **Given** `flag_display=CSS_ICON`, **When** widget renders, **Then** bundled `flag-icons.min.css` and SVG assets under `flags/4x3/` style the prefix selector.
2. **Given** `flag_display=UX_ICON` and UX Icons installed, **When** `IconSupportChecker` detects support, **Then** `CountryFlagRenderer` emits UX icon markup.
3. **Given** `prefix_display=PREFIX_ONLY` or `show_flag=false`, **When** widget renders, **Then** dial code text displays without flag chrome.

---

### Edge Cases

- Missing libphonenumber: validator falls back to bundled `phone_patterns.json` when `use_libphonenumber=false` or package absent.
- `excluded_countries`: removes ISO codes from selector regardless of catalog presence.
- E.164 prefill: `E164Parser` splits stored E.164 into ISO + national on form load when prefix selector enabled.
- Overriding templates: application can override `@NowoPhoneInput/Form/*` via Twig paths registered by `TwigPathsPass`.
- Empty national with required=false: transformer yields null/empty per format rules.

---

## Requirements

### Bundle & DI

- **FR-BUNDLE-001**: `NowoPhoneInputBundle` MUST expose alias `nowo_phone_input` via `NowoPhoneInputExtension`.
- **FR-CFG-001**: `Configuration` MUST define `country_prefix_selector`, `default_country`, `preferred_countries`, `allowed_countries`, `excluded_countries`, `value_format`, `prefix_display`, `show_flag`, `prefix_search`, `flag_display`, CSS class arrays, `use_phone_form_theme`, `trim`, `invalid_message`, `phone_validation`, and `use_libphonenumber`.
- **FR-CFG-002**: Extension MUST load `services.yaml`, inject defaults into `PhoneType`, and set Twig form theme when `use_phone_form_theme=true`.
- **FR-DI-001**: `services.yaml` MUST wire `PhoneType`, transformers, validators, catalog services, and Twig extensions with autowire defaults.

### Country & pattern data

- **FR-DATA-001**: `Country`, `CountryProvider`, and `Resources/data/countries.json` MUST supply ISO code, dial code, and display name for bundled countries.
- **FR-PHONE-001**: `E164Parser` MUST parse and compose E.164 strings consistently with selected country metadata.
- **FR-PHONE-002**: `PhonePattern`, `PhonePatternCatalog`, and `phone_patterns.json` MUST provide national-number patterns used when libphonenumber is unavailable.

### Form layer

- **FR-FORM-001**: `PhoneType` with `ValueFormat`, `PrefixDisplay`, and `FlagDisplay` enums MUST expose documented field options overriding bundle defaults.
- **FR-FORM-002**: `PhoneNumberTransformer` MUST convert between view layer (country_iso + national_number) and model values per `ValueFormat`.
- **FR-FORM-003**: `IconSupportChecker` MUST detect optional UX Icons package to enable `UX_ICON` rendering.
- **FR-MDL-001**: `PhoneNumber` value object MUST expose ISO, national number, dial code, and E.164 accessors for OBJECT format.

### Validation

- **FR-VAL-001**: `PhoneValidator` and `PhoneValidationMode` MUST implement COUNTRY, PREFIX, and NONE strategies using patterns and optional libphonenumber.
- **FR-VAL-002**: `ValidPhoneNumber` constraint and validator MUST integrate with Symfony Validator and respect type-level `invalid_message`.

### Twig & assets

- **FR-TWIG-001**: `TwigPathsPass` MUST register `Resources/views` under namespace `NowoPhoneInput` for overridable templates.
- **FR-TWIG-002**: `CountryFlagExtension` and `CountryFlagRenderer` MUST render flags for Twig and form partials across display modes.
- **FR-TWIG-003**: `phone_input_widget.html.twig` and `_phone_country_flag.html.twig` MUST render prefix selector, national input, and accessibility-friendly markup.
- **FR-ASSET-001**: `phone_input.css` MUST style the composite widget and framework-friendly layout hooks.
- **FR-ASSET-002**: `flag-icons.min.css` MUST map ISO codes to bundled SVG flags.
- **FR-FLAG-001**: SVG assets under `Resources/public/flags/4x3/` MUST provide 4×3 flag icons for catalog countries shipped with the bundle (64 ISO codes).

---

## Key Entities

- **Country**: ISO 3166-1 alpha-2 code, dial prefix, localized name.
- **PhoneNumber**: Immutable VO for OBJECT format with E.164 serialization.
- **PhonePattern**: National-number regex/metadata per country for fallback validation.

---

## Success Criteria

- **SC-001**: 100% of production files in `src/` mapped in [`code-inventory.md`](code-inventory.md) (**93/93**).
- **SC-002**: Configuration keys in `docs/CONFIGURATION.md` match `Configuration.php`.
- **SC-003**: PHPUnit (≥95% coverage) and PHPStan pass in CI.
- **SC-004**: All three value formats round-trip correctly in integration tests.
- **SC-005**: Widget renders with and without prefix selector using documented form theme.

---

## Assumptions

- Integrators publish bundle assets to `public/bundles/nowophoneinput/` per INSTALLATION.
- `giggsey/libphonenumber-for-php` is optional; fallback patterns cover bundled countries only.
- Symfony UX Icons are optional for `UX_ICON` display mode.
- Demos under `demo/` illustrate CSS frameworks but are not Packagist API.

---

## Explicit non-goals

- SMS OTP delivery or phone number verification workflows.
- Real-time carrier lookup or numbering plan administration UI.
- JavaScript-heavy international tel input widgets (widget is server-rendered Twig + CSS).

---

## Validation

| Check | Command |
| --- | --- |
| Full QA | `make release-check` or `composer qa` |
| PHP tests + coverage | `composer test-coverage` (≥95%) |
| Static analysis | `vendor/bin/phpstan analyse` |
| Code inventory | `find src -type f \| wc -l` must match 93 |

When changing behavior, update this spec, [`code-inventory.md`](code-inventory.md), tests, and integrator docs.
