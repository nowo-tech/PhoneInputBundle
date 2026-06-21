<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Validation;

/**
 * Phone validation strategies for {@see ValidPhoneNumber}.
 */
enum PhoneValidationMode: string
{
    /** Validate the national number using the selected or resolved ISO country code. */
    case COUNTRY = 'COUNTRY';

    /** Validate using the dial prefix to resolve country rules (useful for shared prefixes). */
    case PREFIX = 'PREFIX';

    /** Disable built-in phone validation. */
    case NONE = 'NONE';
}
