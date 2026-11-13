# Architecture — PhoneInputBundle

PhoneInputBundle is a **presentation** bundle (REQ-ARCH-001, class *Presentation*): a form type with a country prefix
picker, a validation constraint and the services behind them. It has no entities, no Doctrine mapping and no events.
The extension points are the service interfaces below, the `national_number_checker` key and the Twig form theme.

## Table of contents

- [Public surface](#public-surface)
- [Ports and extension points (SPI)](#ports-and-extension-points-spi)
- [Standard Symfony and DDD hosts](#standard-symfony-and-ddd-hosts)
- [Worker mode](#worker-mode)

## Public surface

| Kind | Types |
| --- | --- |
| Service ports (interfaces) | `Phone\PhoneValidatorInterface`, `Phone\E164ParserInterface`, `Country\CountryProviderInterface` (since 1.7.0) |
| SPI (you implement it) | `Phone\NationalPhoneNumberChecker` |
| Default services | `Phone\PhoneValidator`, `Phone\E164Parser`, `Country\CountryProvider`, `Phone\LibPhoneNumberChecker` (when `giggsey/libphonenumber-for-php` is installed) |
| Value objects | `Phone\PhoneNumber` (since 1.7.0), `Country\Country`, `Phone\PhonePattern` |
| Enums / constants | `Validation\PhoneValidationMode`, `Form\ValueFormat`, `Form\PrefixDisplay`, `Form\FlagDisplay` |
| Form and validation | `Form\Type\PhoneType`, `Validator\Constraints\ValidPhoneNumber` |
| Deprecated | `Form\Model\PhoneNumber`: a subclass of `Phone\PhoneNumber` with no member of its own, still returned by `ValueFormat::OBJECT`; removed in 2.0 |

None of the ports or value objects uses a Symfony, Doctrine or HTTP type.

## Ports and extension points (SPI)

| Extension point | Default | Replace it to… | How |
| --- | --- | --- | --- |
| `Phone\NationalPhoneNumberChecker` (`isValid($regionIso, $nationalNumber)`) | `LibPhoneNumberChecker` when libphonenumber is installed and `use_libphonenumber: true`; otherwise the bundled patterns | validate with your own rules or another library | `nowo_phone_input.national_number_checker: <service id>` (since 1.7.0); it is used whether or not libphonenumber is installed |
| `Phone\PhoneValidatorInterface` | `PhoneValidator` | replace the whole check used by `#[ValidPhoneNumber]` and `PhoneType` | alias the interface to your service |
| `Phone\E164ParserInterface` | `E164Parser` | parse values your own way | alias the interface |
| `Country\CountryProviderInterface` | `CountryProvider` (bundled `countries.json`, filtered by the configuration) | serve the catalog from your own source | alias the interface; the selector, the parser and the patterns read it |
| Form theme | `@NowoPhoneInputBundle/Form/phone_input_widget.html.twig` | change the markup | override the template or its blocks (REQ-TWIG-001); see [USAGE.md](USAGE.md) |

```yaml
# config/services.yaml — replace a default
services:
  Nowo\PhoneInputBundle\Country\CountryProviderInterface: '@App\Infrastructure\Phone\DatabaseCountryProvider'

# config/packages/nowo_phone_input.yaml — plug in your own national-number check
nowo_phone_input:
  national_number_checker: App\Infrastructure\Phone\TelecomRegistryChecker
```

## Standard Symfony and DDD hosts

**Standard Symfony host.** Nothing to wire: add `PhoneType` to a form, or `#[ValidPhoneNumber]` to an entity or a
DTO. `value_format` picks the model value: an E.164 string (default), a separated array or a `PhoneNumber` object.

**DDD / hexagonal host.**

- Keep the bundle at the edges: use `PhoneType` on the form DTO / command, not on a domain entity, and put
  `#[ValidPhoneNumber]` there (or in external validator mapping).
- Build your own domain value from the model value in the handler. With `value_format: OBJECT` type the value as
  `Nowo\PhoneInputBundle\Phone\PhoneNumber` (plain PHP); never as the deprecated `Form\Model\PhoneNumber`.
- When your domain owns a "phone number policy" port, implement it in infrastructure with
  `PhoneValidatorInterface`:

```php
namespace App\Infrastructure\Phone;

use App\Domain\Contact\PhoneNumberPolicy;           // your port
use Nowo\PhoneInputBundle\Phone\PhoneValidatorInterface;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;

final readonly class NowoPhoneNumberPolicy implements PhoneNumberPolicy
{
    public function __construct(private PhoneValidatorInterface $validator) {}

    public function isValid(string $e164): bool
    {
        return $this->validator->isValid($e164, PhoneValidationMode::COUNTRY, 'ES');
    }
}
```

- To share the same rule between the domain and the form, implement `NationalPhoneNumberChecker` in infrastructure
  over your domain service and set `national_number_checker` to it.

## Worker mode

The services keep only request-independent caches (the parsed catalog and patterns); they are safe in a FrankenPHP
worker with the kernel not reset. See [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md).
