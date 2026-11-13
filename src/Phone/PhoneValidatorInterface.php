<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;

/**
 * Phone number check used by `#[ValidPhoneNumber]` and `PhoneType` (since 1.7.0, REQ-ARCH-001 rule 1). The default,
 * {@see PhoneValidator}, uses the configured {@see NationalPhoneNumberChecker} (libphonenumber or your own) or the
 * bundled patterns. Call it from your own adapter to keep the domain free of the bundle, or alias it to replace the
 * check everywhere.
 */
interface PhoneValidatorInterface
{
    /**
     * Whether the value is a valid phone number. An empty value (null, '', an empty national number) is valid: use
     * `NotBlank` for required fields.
     *
     * @param mixed $value an E.164 string, a separated array (`iso`, `prefix`, `national_number`) or a {@see PhoneNumber}
     */
    public function isValid(mixed $value, PhoneValidationMode $mode, string $defaultCountryIso = 'ES'): bool;
}
