# Code inventory — 100% traceability

**Baseline spec**: [`spec.md`](spec.md)  
**Package**: `nowo-tech/phone-input-bundle`  
**Last audited**: 2026-07-07

This file proves that **every production source artifact** under `src/` is referenced by the baseline specification. Test-only files under `tests/` and demo trees are out of Packagist scope unless promoted in the spec.

## PHP classes (`src/**/*.php`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `NowoPhoneInputBundle.php` | Bundle entry | FR-BUNDLE-001 |
| `DependencyInjection/Configuration.php` | Config tree | FR-CFG-001 |
| `DependencyInjection/NowoPhoneInputExtension.php` | DI extension | FR-CFG-002 |
| `DependencyInjection/Compiler/TwigPathsPass.php` | Twig namespace path | FR-TWIG-001 |
| `Country/Country.php` | Country value object | FR-DATA-001 |
| `Country/CountryProvider.php` | Country catalog loader | FR-DATA-001 |
| `Phone/E164Parser.php` | E.164 parsing | FR-PHONE-001 |
| `Phone/PhonePattern.php` | National pattern VO | FR-PHONE-002 |
| `Phone/PhonePatternCatalog.php` | Pattern catalog loader | FR-PHONE-002 |
| `Phone/PhoneValidator.php` | Validation orchestration | FR-VAL-001 |
| `Form/Type/PhoneType.php` | Main form type | FR-FORM-001 |
| `Form/DataTransformer/PhoneNumberTransformer.php` | Value format transformer | FR-FORM-002 |
| `Form/Model/PhoneNumber.php` | Phone value object | FR-MDL-001 |
| `Form/ValueFormat.php` | Value format enum | FR-FORM-001 |
| `Form/PrefixDisplay.php` | Prefix display enum | FR-FORM-001 |
| `Form/FlagDisplay.php` | Flag display enum | FR-FORM-001 |
| `Validation/PhoneValidationMode.php` | Validation mode enum | FR-VAL-001 |
| `Validator/Constraints/ValidPhoneNumber.php` | Constraint attribute | FR-VAL-002 |
| `Validator/Constraints/ValidPhoneNumberValidator.php` | Constraint validator | FR-VAL-002 |
| `IconSupport/IconSupportChecker.php` | UX Icons detection | FR-FORM-003 |
| `Twig/CountryFlagExtension.php` | Flag Twig extension | FR-TWIG-002 |
| `Twig/CountryFlagRenderer.php` | Flag render strategies | FR-TWIG-002 |

## JSON data (`src/Resources/data/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/data/countries.json` | Bundled country catalog | FR-DATA-001 |
| `Resources/data/phone_patterns.json` | National number patterns | FR-PHONE-002 |

## CSS assets (`src/Resources/public/css/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/public/css/phone_input.css` | Widget layout styles | FR-ASSET-001 |
| `Resources/public/css/flag-icons.min.css` | CSS flag icons | FR-ASSET-002 |

## Flag SVG icons (`src/Resources/public/flags/4x3/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/public/flags/4x3/ae.svg` | Bundled 4×3 flag (AE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ar.svg` | Bundled 4×3 flag (AR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/at.svg` | Bundled 4×3 flag (AT) | FR-FLAG-001 |
| `Resources/public/flags/4x3/au.svg` | Bundled 4×3 flag (AU) | FR-FLAG-001 |
| `Resources/public/flags/4x3/be.svg` | Bundled 4×3 flag (BE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/bo.svg` | Bundled 4×3 flag (BO) | FR-FLAG-001 |
| `Resources/public/flags/4x3/br.svg` | Bundled 4×3 flag (BR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ca.svg` | Bundled 4×3 flag (CA) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ch.svg` | Bundled 4×3 flag (CH) | FR-FLAG-001 |
| `Resources/public/flags/4x3/cl.svg` | Bundled 4×3 flag (CL) | FR-FLAG-001 |
| `Resources/public/flags/4x3/cn.svg` | Bundled 4×3 flag (CN) | FR-FLAG-001 |
| `Resources/public/flags/4x3/co.svg` | Bundled 4×3 flag (CO) | FR-FLAG-001 |
| `Resources/public/flags/4x3/cr.svg` | Bundled 4×3 flag (CR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/cz.svg` | Bundled 4×3 flag (CZ) | FR-FLAG-001 |
| `Resources/public/flags/4x3/de.svg` | Bundled 4×3 flag (DE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/dk.svg` | Bundled 4×3 flag (DK) | FR-FLAG-001 |
| `Resources/public/flags/4x3/do.svg` | Bundled 4×3 flag (DO) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ec.svg` | Bundled 4×3 flag (EC) | FR-FLAG-001 |
| `Resources/public/flags/4x3/eg.svg` | Bundled 4×3 flag (EG) | FR-FLAG-001 |
| `Resources/public/flags/4x3/es.svg` | Bundled 4×3 flag (ES) | FR-FLAG-001 |
| `Resources/public/flags/4x3/fi.svg` | Bundled 4×3 flag (FI) | FR-FLAG-001 |
| `Resources/public/flags/4x3/fr.svg` | Bundled 4×3 flag (FR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/gb.svg` | Bundled 4×3 flag (GB) | FR-FLAG-001 |
| `Resources/public/flags/4x3/gr.svg` | Bundled 4×3 flag (GR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/gt.svg` | Bundled 4×3 flag (GT) | FR-FLAG-001 |
| `Resources/public/flags/4x3/hk.svg` | Bundled 4×3 flag (HK) | FR-FLAG-001 |
| `Resources/public/flags/4x3/hn.svg` | Bundled 4×3 flag (HN) | FR-FLAG-001 |
| `Resources/public/flags/4x3/id.svg` | Bundled 4×3 flag (ID) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ie.svg` | Bundled 4×3 flag (IE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/il.svg` | Bundled 4×3 flag (IL) | FR-FLAG-001 |
| `Resources/public/flags/4x3/in.svg` | Bundled 4×3 flag (IN) | FR-FLAG-001 |
| `Resources/public/flags/4x3/it.svg` | Bundled 4×3 flag (IT) | FR-FLAG-001 |
| `Resources/public/flags/4x3/jp.svg` | Bundled 4×3 flag (JP) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ke.svg` | Bundled 4×3 flag (KE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/kr.svg` | Bundled 4×3 flag (KR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/mx.svg` | Bundled 4×3 flag (MX) | FR-FLAG-001 |
| `Resources/public/flags/4x3/my.svg` | Bundled 4×3 flag (MY) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ng.svg` | Bundled 4×3 flag (NG) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ni.svg` | Bundled 4×3 flag (NI) | FR-FLAG-001 |
| `Resources/public/flags/4x3/nl.svg` | Bundled 4×3 flag (NL) | FR-FLAG-001 |
| `Resources/public/flags/4x3/no.svg` | Bundled 4×3 flag (NO) | FR-FLAG-001 |
| `Resources/public/flags/4x3/nz.svg` | Bundled 4×3 flag (NZ) | FR-FLAG-001 |
| `Resources/public/flags/4x3/pa.svg` | Bundled 4×3 flag (PA) | FR-FLAG-001 |
| `Resources/public/flags/4x3/pe.svg` | Bundled 4×3 flag (PE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ph.svg` | Bundled 4×3 flag (PH) | FR-FLAG-001 |
| `Resources/public/flags/4x3/pl.svg` | Bundled 4×3 flag (PL) | FR-FLAG-001 |
| `Resources/public/flags/4x3/pr.svg` | Bundled 4×3 flag (PR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/pt.svg` | Bundled 4×3 flag (PT) | FR-FLAG-001 |
| `Resources/public/flags/4x3/py.svg` | Bundled 4×3 flag (PY) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ro.svg` | Bundled 4×3 flag (RO) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ru.svg` | Bundled 4×3 flag (RU) | FR-FLAG-001 |
| `Resources/public/flags/4x3/sa.svg` | Bundled 4×3 flag (SA) | FR-FLAG-001 |
| `Resources/public/flags/4x3/se.svg` | Bundled 4×3 flag (SE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/sg.svg` | Bundled 4×3 flag (SG) | FR-FLAG-001 |
| `Resources/public/flags/4x3/sv.svg` | Bundled 4×3 flag (SV) | FR-FLAG-001 |
| `Resources/public/flags/4x3/th.svg` | Bundled 4×3 flag (TH) | FR-FLAG-001 |
| `Resources/public/flags/4x3/tr.svg` | Bundled 4×3 flag (TR) | FR-FLAG-001 |
| `Resources/public/flags/4x3/tw.svg` | Bundled 4×3 flag (TW) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ua.svg` | Bundled 4×3 flag (UA) | FR-FLAG-001 |
| `Resources/public/flags/4x3/us.svg` | Bundled 4×3 flag (US) | FR-FLAG-001 |
| `Resources/public/flags/4x3/uy.svg` | Bundled 4×3 flag (UY) | FR-FLAG-001 |
| `Resources/public/flags/4x3/ve.svg` | Bundled 4×3 flag (VE) | FR-FLAG-001 |
| `Resources/public/flags/4x3/vn.svg` | Bundled 4×3 flag (VN) | FR-FLAG-001 |
| `Resources/public/flags/4x3/za.svg` | Bundled 4×3 flag (ZA) | FR-FLAG-001 |

## Symfony config (`src/Resources/config/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/config/services.yaml` | Service wiring | FR-DI-001 |

## Twig views (`src/Resources/views/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/views/Form/phone_input_widget.html.twig` | Form widget | FR-TWIG-003 |
| `Resources/views/Form/_phone_country_flag.html.twig` | Flag partial | FR-TWIG-003 |

## Coverage summary

| Category | Files | Mapped |
| --- | ---: | ---: |
| PHP classes | 22 | 22 |
| JSON data | 2 | 2 |
| CSS assets | 2 | 2 |
| Flag SVG icons | 64 | 64 |
| YAML config | 1 | 1 |
| Twig views | 2 | 2 |
| **Total production sources** | **93** | **93** |

Bundled flags cover the ISO codes shipped with the bundle; additional countries may be added in future releases with matching catalog entries in `countries.json`.
