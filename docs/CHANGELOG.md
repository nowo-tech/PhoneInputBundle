# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Table of contents

- [[Unreleased]](#unreleased)
- [[1.1.3] - 2026-07-09](#113---2026-07-09)
- [[1.1.2] - 2026-06-30](#112---2026-06-30)
- [[1.1.1] - 2026-06-30](#111---2026-06-30)
- [[1.1.0] - 2026-06-20](#110---2026-06-20)
- [[1.0.2] - 2026-06-20](#102---2026-06-20)
- [[1.0.1] - 2026-06-20](#101---2026-06-20)
- [[1.0.0] - 2026-06-20](#100---2026-06-20)

## [Unreleased]

### Removed

- `demo/symfony6` demo application (Symfony 6.4); use `demo/symfony7` or `demo/symfony8`

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
