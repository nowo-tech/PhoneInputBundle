<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Integration\Architecture;

use Nowo\PhoneInputBundle\Phone\NationalPhoneNumberChecker;

/**
 * A host's own national-number backend, wired with `national_number_checker`.
 */
final class OnlySpainChecker implements NationalPhoneNumberChecker
{
    public function isValid(string $regionIso, string $nationalNumber): bool
    {
        return 'ES' === $regionIso;
    }
}
