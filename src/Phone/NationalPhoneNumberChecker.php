<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

/**
 * Optional national-number validation backend (e.g. libphonenumber).
 */
interface NationalPhoneNumberChecker
{
    public function isValid(string $regionIso, string $nationalNumber): bool;
}
