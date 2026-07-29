<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

use libphonenumber\PhoneNumberUtil;

/**
 * libphonenumber-backed national number checker (injected; no getInstance in request code).
 */
final class LibPhoneNumberChecker implements NationalPhoneNumberChecker
{
    public function __construct(
        private readonly PhoneNumberUtil $phoneNumberUtil,
    ) {
    }

    public function isValid(string $regionIso, string $nationalNumber): bool
    {
        try {
            $parsed = $this->phoneNumberUtil->parse($nationalNumber, $regionIso);

            return $this->phoneNumberUtil->isValidNumber($parsed);
        } catch (\Throwable) {
            return false;
        }
    }
}
