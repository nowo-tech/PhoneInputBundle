# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Table of contents

- [[Unreleased]](#unreleased)
- [[1.6.3] - 2026-10-22](#163---2026-10-22)
- [[1.6.2] - 2026-10-18](#162---2026-10-18)
- [[1.6.1] - 2026-10-13](#161---2026-10-13)
- [[1.6.0] - 2026-10-13](#160---2026-10-13)
- [[1.5.0] - 2026-10-13](#150---2026-10-13)
- [[1.4.4] - 2026-10-09](#144---2026-10-09)
- [[1.4.3] - 2026-10-09](#143---2026-10-09)
- [[1.4.2] - 2026-09-27](#142---2026-09-27)
- [[1.4.1] - 2026-09-24](#141---2026-09-24)
- [[1.4.0] - 2026-09-03](#140---2026-09-03)
- [[1.3.3] - 2026-08-24](#133---2026-08-24)
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

## [1.6.3] - 2026-10-22

Wave-2 REQ compliance: DOM-safe prefix picker flags, translation and raw-form gates, CI static analysis and demo smoke. See [UPGRADING](UPGRADING.md#to-163).

### Changed

- Prefix picker JS builds the toggle flag with DOM APIs (`className` / `textContent`) instead of `innerHTML` with data attributes (REQ-UX-001).
- `make validate-translations` (YAML lint + key parity for `NowoPhoneInputBundle` and `validators`) and `make check-no-raw-html-form` in `release-check`, which now starts with `ensure-up` (REQ-MAKE-002/004).
- CI: the code-style job also runs PHPStan, Igor and translation parity on pushes; new `demo-smoke.yml` workflow (REQ-CS-006, REQ-TEST-011).
- Demo: CSS-framework switcher is a Symfony Form (`FrameworkSwitcherType`); the redundant raw hidden `framework` input is gone; the entrypoint waits for `vendor/` so worker mode boots on a clean checkout (REQ-DEMO-001, REQ-TWIG-005, REQ-TEST-011).
- `PhoneType` is tagged by autoconfiguration (manual `form.type` tag removed) (REQ-SF-003); Rector uses the PHP 8.2 sets with at most 2 workers (REQ-CS-003).
- Docs: PSR evaluation reasons match the package (REQ-CS-007); release checklist gains the AI-audit and client-rendering items (REQ-SEC-002); README coverage updated to the measured value (REQ-TEST-007).
- `.scripts/check-no-cursor-coauthor.sh` accepts git worktrees (REQ-GIT-001).

- Dependencies refreshed (Composer and pnpm/npm lock files of the bundle and its demos, campaign 2026-10-19).
- Documentation: wave-1 REQ compliance — README documentation list order and canonical FrankenPHP claim line, CONTRIBUTING Cursor Agent settings and post-release-commit trailer check, canonical bug-report template, de-duplicated pull request template, demo `.env.example` comments, doc TOCs, Spec Kit inventory refresh (110/110).

## [1.6.2] - 2026-10-18

Widget texts translated in the bundle's own domain (7 locales), validator messages shipped. See [UPGRADING](UPGRADING.md#to-162).

### Fixed

- **Untranslated widget texts / profiler missing messages:** the prefix selector label ("Country prefix"), the search
  placeholder ("Search country or prefix…") and the empty state ("No countries found") were English literals looked
  up in the field's translation domain (missing in every host catalogue). They are now messages of the bundle domain
  `NowoPhoneInputBundle` (`phone_input.prefix_label`, `phone_input.search_placeholder`, `phone_input.no_countries`)
  with translations in en, es, fr, de, it, pt and nl. The default violation messages ("The phone number is invalid
  for the selected country.", "The phone number is invalid.") ship in the `validators` domain for the same locales.
  Test rendering the widget with a `DataCollectorTranslator` asserting no missing message; `symfony/translation` added
  to require-dev.

## [1.6.1] - 2026-10-13

### Fixed

- Prefix selector: a number typed with another country's `+` prefix (accepted since 1.6.0) must belong to a country the selector offers; a prefix of a country removed by `allowed_countries` / `excluded_countries` is now a transformation error (invalid field) instead of being saved.

## [1.6.0] - 2026-10-13

### Fixed

- Shared dial codes (+1 US/CA/PR/DO, +7 …): `E164Parser` always returned the first catalog country, so a CONCATENATED value such as `+14165550123` came back as the wrong country. The parser now keeps a preferred ISO that uses the matched code (new optional `$preferredIsos` argument, then the default country). The form keeps the country selected in the prefix selector, and on display prefers `default_country`, then the selector's preferred/allowed countries.
- Prefix selector: a number typed with a different international prefix (e.g. `+34…` while CA is selected) is no longer glued to the selected country's dial code; the typed prefix wins.

### Changed

- Symfony constraints aligned to the CI floor: `^6.4 || ^7.0 || ^8.0` for every Symfony package (some still allowed `^6.0`).

## [1.5.0] - 2026-10-13

### Fixed

- Without the country prefix selector, an international number typed with a non-default prefix (e.g. `+447911123456` with default country `ES`) is now saved as typed instead of being rewritten with the default prefix (`+347911123456`).
- `flag_display: UX_ICON` now actually receives the Symfony UX Icons renderer when `symfony/ux-icons` is installed (the service was never wired, so CSS flags were always used).
- `use_phone_form_theme: true` (default) now registers the widget form theme in `twig.form_themes` as documented; set it to `false` to manage the theme yourself.
- Server-side validation of separated values with a `+` prefix in the national number uses the constraint default country instead of a hardcoded `ES`.

## [1.4.4] - 2026-10-09

### Changed

- CSP: documented the shared nonce convention and added a template scan test guarding it (inline `<script>`/`<style>` must declare the `csp_nonce` request attribute; no inline event handlers). The widget already renders no inline blocks — only the external `nowo-phone-prefix-picker.js`, which sets positions through the CSSOM (allowed by `style-src`).

## [1.4.3] - 2026-10-09

### Added

- **REQ-DEMO-013:** Playwright e2e under `demo/symfony8/e2e/` (`make test-e2e`), `demo-screenshots` target, and README gallery with full demo context (navbar + first use-case + WebProfiler; `docs/images/demo/overview.png`, `interaction.png`).

### Changed

- **Demo screenshots:** capture navbar + form chrome + profiler instead of a tight field crop; demo template adds a sticky navbar.

### Dependencies

- Dev tooling: `igor-php/igor-php` ^0.10 (Dependabot #28), `phpstan/phpstan` 2.3.1, `phpstan/phpstan-phpunit` 2.1.1, `phpstan/phpstan-symfony` 2.1.0, `rector/rector` 2.7.0, `nowo-tech/phpstan-frankenphp` v1.2.3, `giggsey/libphonenumber-for-php` 9.0.41; Symfony 7.4/8.1 patch releases in the bundle lock.
- Demo (Symfony 8): `twig/twig` v3.30.0, `twig/extra-bundle` v3.29.0, PHPUnit 10.5.66, `nowo-tech/hot-reload-bundle` v1.5.5.

[1.6.3]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.6.3
[1.6.2]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.6.2
[1.6.1]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.6.1
[1.6.0]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.6.0
[1.5.0]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.5.0
[1.4.4]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.4.4
[1.4.3]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.4.3

## [1.4.2] - 2026-09-27

### Added

- **REQ-CS-008:** `igor-php/igor-php` (require-dev only), root `igor.json`, Composer/`Makefile` `igor` target, and `release-check` wiring for FrankenPHP worker-state audit.

### Changed

- **Worker safety (Igor):** justified `// @igor-ignore` annotations and/or `ResetInterface` / request-scoped fixes so `make igor` passes on package `src/`.

[1.4.2]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.4.2

## [1.4.1] - 2026-09-24

### Added

- FrankenPHP worker audit for sticky Kernel (`FRANKENPHP_RESET_KERNEL=false`): [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md)
- Unit regression `PhoneTypeTest::testSharedInstanceDoesNotLeakOptionsAcrossConsecutiveBuilds`

### Changed

- **Worker hardening:** `PhoneType::$defaults` and `CountryProvider` config lists are `readonly`; CSS class defaults are defensively copied in `configureOptions()` so OptionsResolver cannot alias shared service arrays
- PHPStan now uses `nowo-tech/phpstan-frankenphp` `ruleset-worker-strict.neon` (includes worker rules)

### Notes

- No application upgrade steps. Safe under FrankenPHP worker with or without kernel reset.

[1.4.1]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.4.1

## [1.4.0] - 2026-09-03

### Changed

- **Web Component:** the widget now renders `<nowo-phone-input>` (light DOM: country picker + national number). `nowo-phone-prefix-picker.js` defines the custom element; the inner prefix picker still initializes via `data-nowo-phone-prefix-picker`.
- **Deps (dev):** bump `friendsofphp/php-cs-fixer`; refresh Composer lockfiles.

### Notes

- Compound Symfony field submission is unchanged (light DOM). Custom theme overrides should wrap with `<nowo-phone-input>`.

[1.4.0]: https://github.com/nowo-tech/PhoneInputBundle/releases/tag/v1.4.0

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
