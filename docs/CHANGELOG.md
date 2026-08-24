# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Table of contents

- [[Unreleased]](#unreleased)
- [[1.3.2] - 2026-08-19](#132---2026-08-19)
- [[1.3.1] - 2026-08-18](#131---2026-08-18)
- [[1.3.0] - 2026-08-15](#130---2026-08-15)
- [[1.2.1] - 2026-08-07](#121---2026-08-07)
- [[1.2.0] - 2026-08-04](#120---2026-08-04)
- [[1.1.5] - 2026-07-29](#115---2026-07-29)
- [[1.1.4] - 2026-07-16](#114---2026-07-16)
- [[1.1.3] - 2026-07-09](#113---2026-07-09)
- [[1.1.2] - 2026-06-30](#112---2026-06-30)
- [[1.1.1] - 2026-06-30](#111---2026-06-30)
- [[1.1.0] - 2026-06-20](#110---2026-06-20)
- [[1.0.2] - 2026-06-20](#102---2026-06-20)
- [[1.0.1] - 2026-06-20](#101---2026-06-20)
- [[1.0.0] - 2026-06-20](#100---2026-06-20)

## [Unreleased]

### Changed

- **Web Component:** the widget now renders `<nowo-phone-input>` (light DOM: country picker + national number). `nowo-phone-prefix-picker.js` defines the custom element; the inner prefix picker still initializes via `data-nowo-phone-prefix-picker`.


## [1.3.3] - 2026-08-24

### Changed

- **Docs:** PHP-FIG PSR evaluation (REQ-CS-007).
- **Style:** PHP CS Fixer alignment.

### Notes

- **No API or configuration changes** for integrators unless noted above.

[1.3.3]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.3.3

## [1.3.2] - 2026-08-19

### Security

- **CI:** run `composer audit --locked` after dependency install (REQ-SEC / P3).

[1.3.2]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.3.2

## [1.3.1] - 2026-08-18

### Changed

- **Demos:** pin `nowo-tech/hot-reload-bundle` to `^1.4` with FrankenPHP Mercure/`hot_reload` (`dev`/`test` only).
- **Demos:** Symfony 8 only; Symfony 6/7 demo apps removed.

[1.3.1]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.3.1

## [1.3.0] - 2026-08-15

### Added

- CSP-safe prefix picker: external `js/nowo-phone-prefix-picker.js` (vanilla IIFE) loaded via `asset(..., 'nowo_phone_input')` with `defer`; no inline `<script>` in the Twig widget.
- Stimulus-compatible markup (`data-controller="phone-prefix-picker"` + `data-action` hooks) so hosts may register a Stimulus controller instead of the IIFE.
- Portal dropdown (`--portaled`) so the menu is not clipped by `overflow: auto` dialogs/panels.

### Changed

- Progressive enhancement CSS: native `<select>` remains usable until JS adds `--enhanced`; custom toggle shows only after enhancement.
- Widget CSS: focus styles, portaled z-index, and dark `prefers-color-scheme` dropdown shadow.

### Documentation

- [UPGRADING.md](UPGRADING.md), [USAGE.md](USAGE.md), [CONFIGURATION.md](CONFIGURATION.md), [INSTALLATION.md](INSTALLATION.md), [RELEASE.md](RELEASE.md) for 1.3.0 CSP / Stimulus notes.

[1.3.0]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.3.0

## [1.2.1] - 2026-08-07

### Fixed

- CI coverage gate: raise measured coverage to **100%** (elements/lines) with unit tests for optional DI branches, ambiguous prefix pattern fallback, and `empty_data` value-format coercion.

[1.2.1]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.2.1

## [1.2.0] - 2026-08-04

### Added
- **REQ-TWIG-004:** require `twig/extra-bundle` + `twig/string-extra`; `make check-twig-extra` in `release-check`; demos register `TwigExtraBundle`.
- **Twig-CS-Fixer:** `vincentlanglet/twig-cs-fixer`, `.twig-cs-fixer.php`, `composer twig:lint` / `twig:fix`.

[1.2.0]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.2.0

## [1.1.5] - 2026-07-29

### Added

- Symfony asset package `nowo_phone_input` (`base_path` `/bundles/nowophoneinput`) — use `asset('css/…', 'nowo_phone_input')` (requires `symfony/asset`; now a hard dependency).
- Make targets: `check-open-prs`, `coverage-check` (≥99% lines), `demo-smoke`.
- FrankenPHP Friendly Worker Mode banner in README; Symfony 8 demo on PHP **8.5**; demo entrypoints honor `FRANKENPHP_MODE` (default `worker`).
- Product threat model in `docs/SECURITY.md`.
- **REQ-CS-005:** PHPStan includes `nowo-tech/phpstan-frankenphp` classic + worker rulesets.
- `LibPhoneNumberChecker` / `NationalPhoneNumberChecker` for clearer libphonenumber wiring in DI.

### Changed

- PHPStan `ignoreErrors` emptied (REQ-CS-006); `SYMFONY_DEPRECATIONS_HELPER=max[direct]=0` in PHPUnit.
- Docs/demos no longer hard-code `/bundles/nowophoneinput/…` asset URLs.

### Documentation

- [INSTALLATION.md](INSTALLATION.md), [DEMO-FRANKENPHP.md](DEMO-FRANKENPHP.md), [UPGRADING.md](UPGRADING.md), [RELEASE.md](RELEASE.md) updated for 1.1.5.

## [1.1.4] - 2026-07-16

### Removed

- `demo/symfony6` demo application (Symfony 6.4); use `demo/symfony7` or `demo/symfony8`

### Added

- Contributor Covenant **Code of Conduct** (`CODE_OF_CONDUCT.md`)
- `docs/GITHUB_CI.md` and CI job enforcing **REQ-GIT-001** (no Cursor co-author trailers in git history)
- Git hooks / Make targets: `commit-msg`, `check-no-cursor-coauthor`, `strip-cursor-coauthor-from-history`
- Unit tests covering transformer paths without prefix selector, option normalizers, multi-country dial prefixes, and libphonenumber validation

### Changed

- `make release-check` now includes `check-no-cursor-coauthor`
- Contributing and release docs updated for git hygiene workflow

## [1.1.3] - 2026-07-09

### Added

- GitHub Spec Kit baseline: `specs/001-baseline/` (spec + 100% `src/` code inventory), `.specify/` scaffolding, and Cursor Agent `speckit-*` skills
- `docs/SPEC-KIT.md` — Spec Kit installation, structure, and maintainer usage

### Changed

- Expanded `docs/SPEC-DRIVEN-DEVELOPMENT.md` with Spec Kit layers, user stories, `REQ-*` workflow, and contributor checklist
- Demo Dockerfiles: install `intl` PHP extension alongside `zip`
- Sync `composer.lock` and demo Symfony lock files (dev dependencies)

## [1.1.2] - 2026-06-30

### Fixed

- Demo Makefiles: define `COMPOSE` and `SERVICE_PHP` before including shared `update-deps` script

### Changed

- Sync `composer.lock` and demo Symfony lock files (dev dependencies)

## [1.1.1] - 2026-06-30

### Fixed

- GitHub release workflow: build release body in a shell step to avoid YAML parsing errors when embedding changelog headings inline

## [1.1.0] - 2026-06-20

### Changed

- **Minimum PHP version is now 8.2** (uses `readonly` classes in `Country`, `PhoneNumber`, `PhonePattern`)
- CI matrix no longer tests PHP 8.1

## [1.0.2] - 2026-06-20

### Fixed

- `CountryFlagRenderer::render()` default parameter compatible with PHP 8.1 (no enum in constant expression)

## [1.0.1] - 2026-06-20

### Fixed

- `ValidPhoneNumber` legacy `options['mode']` string now applies correctly on Symfony 8 / PHP 8.4+ (CI test failure)
- Code coverage threshold in CI (`coveredelements` ≥ 95%) met with additional unit tests

## [1.0.0] - 2026-06-20

First stable release.

### Added

- **`PhoneType`** Symfony form type extending `TelType` with optional country prefix selector
- **Value formats**: `CONCATENATED` (E.164 string), `SEPARATED` (array), `OBJECT` (`PhoneNumber` VO)
- **Prefix display modes**: `FULL`, `PREFIX_ONLY`, `FLAG_ONLY`, `FLAG_AND_PREFIX`, `ISO_AND_PREFIX`
- **Flag display**: `EMOJI`, `CSS_ICON`, `UX_ICON`, `NONE` (bundled `flag-icons` CSS + optional UX Icons)
- **Prefix search** autocomplete in the visual country dropdown (`prefix_search`)
- **Country filtering**: global and per-field `allowed_countries`, `excluded_countries`, `preferred_countries`
- **Phone validation**: `ValidPhoneNumber` constraint with modes `COUNTRY`, `PREFIX`, `NONE`
- Optional **libphonenumber** integration when `giggsey/libphonenumber-for-php` is installed
- Bundled **country catalog** (`countries.json`) and **national-number patterns** (`phone_patterns.json`)
- Framework-agnostic widget via configurable CSS classes (Bootstrap, Tailwind, Foundation, custom)
- **Twig** form theme and `nowo_phone_country_flag` helper
- **Symfony Flex recipe** (`1.0.0`) with default `nowo_phone_input.yaml`
- **Demos** for Symfony 6.4, 7.0 and 8.0 (FrankenPHP, Web Profiler, Twig Inspector)
- CI (PHPUnit matrix, PHPStan, PHP-CS-Fixer, coverage ≥95%), release workflows, and full bundle documentation

### Requirements

- PHP >= 8.1, < 8.6
- Symfony ^6.0 || ^7.0 || ^8.0
- `symfony/validator` (required)
