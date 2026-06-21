# Phone Input Bundle

[![CI](https://github.com/nowo-tech/PhoneInputBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/PhoneInputBundle/actions/workflows/ci.yml) [![Packagist Version](https://img.shields.io/packagist/v/nowo-tech/phone-input-bundle.svg?style=flat)](https://packagist.org/packages/nowo-tech/phone-input-bundle) [![Packagist Downloads](https://img.shields.io/packagist/dt/nowo-tech/phone-input-bundle.svg)](https://packagist.org/packages/nowo-tech/phone-input-bundle) [![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE) [![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)](https://php.net) [![Symfony](https://img.shields.io/badge/Symfony-6.0%2B%20%7C%207.4%2B%20%7C%208.0%20%7C%208.1%2B-000000?logo=symfony)](https://symfony.com) [![GitHub stars](https://img.shields.io/github/stars/nowo-tech/PhoneInputBundle.svg?style=social&label=Star)](https://github.com/nowo-tech/PhoneInputBundle) [![Coverage](https://img.shields.io/badge/coverage-95%25-brightgreen)](#tests-and-coverage)

Symfony bundle providing a phone form type with optional country prefix selector and flexible value formats (E.164 string, separated array, or `PhoneNumber` value object).

> ⭐ **Found this useful?** Give it a **star** on [GitHub](https://github.com/nowo-tech/PhoneInputBundle) so more developers can find it.

## Features

- Extends Symfony `TelType` with an optional country prefix selector (flags, dial codes, autocomplete search)
- Three model formats: **CONCATENATED** (E.164), **SEPARATED** (array), **OBJECT** (`PhoneNumber` VO)
- Validation by country ISO, dial prefix, or disabled (`phone_validation`)
- Configurable prefix/flag display modes and CSS classes (Bootstrap, Tailwind, Foundation, custom)
- Bundled country catalog and CSS flag icons; optional UX Icons for SVG flags

## Documentation

- [Installation](docs/INSTALLATION.md)
- [Configuration](docs/CONFIGURATION.md)
- [Usage](docs/USAGE.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Changelog](docs/CHANGELOG.md)
- [Upgrading](docs/UPGRADING.md)
- [Release](docs/RELEASE.md)
- [Security](docs/SECURITY.md)
- [Engram](docs/ENGRAM.md)
- [Spec-driven development](docs/SPEC-DRIVEN-DEVELOPMENT.md)

### Additional documentation

- [Demo with FrankenPHP (development and production)](docs/DEMO-FRANKENPHP.md)
- [Overriding bundle templates](docs/USAGE.md#overriding-bundle-templates)
- [Branching](docs/BRANCHING.md)

## Quick start

```bash
composer require nowo-tech/phone-input-bundle
```

```php
use Nowo\PhoneInputBundle\Form\Type\PhoneType;

$builder->add('mobile', PhoneType::class);
```

See [docs/INSTALLATION.md](docs/INSTALLATION.md) for form theme, CSS assets, and optional dependencies.

## Demo

Demos for Symfony 6.4, 7.0 and 8.0 live under `demo/`. From the bundle root:

```bash
make -C demo up-symfony8
# http://localhost:8003 (see demo/symfony8/.env.example)
```

The demo page shows **20 field examples** and a **CSS framework selector** (`?framework=bootstrap5|tailwind2|foundation6|symfony-default`). See [demo/README.md](demo/README.md).

Demos use **FrankenPHP** without worker mode in development (changes visible on refresh). For production worker setup, see [docs/DEMO-FRANKENPHP.md](docs/DEMO-FRANKENPHP.md).

## Requirements

- PHP >= 8.1, < 8.6
- Symfony ^6.0 || ^7.0 || ^8.0

## Development

```bash
make up && make install && make test
make test-coverage   # PHP coverage report
make release-check   # cs-fix, phpstan, coverage, demo healthchecks
```

## Tests and coverage

- Tests: PHPUnit (unit + integration)
- PHP: **95.75%**
- TS/JS: N/A
- Python: N/A

## License

MIT — see [LICENSE](LICENSE).

## Author

Created by [Nowo.tech](https://nowo.tech)
