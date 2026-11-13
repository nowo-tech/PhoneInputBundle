<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Integration\Architecture;

use Nowo\PhoneInputBundle\Phone\PhoneValidatorInterface;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;

/**
 * A host's own phone check, wired by aliasing PhoneValidatorInterface.
 */
final class AlwaysInvalidPhoneValidator implements PhoneValidatorInterface
{
    public function isValid(mixed $value, PhoneValidationMode $mode, string $defaultCountryIso = 'ES'): bool
    {
        return false;
    }
}
