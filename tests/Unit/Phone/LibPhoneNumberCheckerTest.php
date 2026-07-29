<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Phone;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Nowo\PhoneInputBundle\Phone\LibPhoneNumberChecker;
use PHPUnit\Framework\TestCase;

final class LibPhoneNumberCheckerTest extends TestCase
{
    public function testValidNationalNumber(): void
    {
        $parsed = new PhoneNumber();
        $util = $this->createMock(PhoneNumberUtil::class);
        $util->method('parse')->with('612345678', 'ES')->willReturn($parsed);
        $util->method('isValidNumber')->with($parsed)->willReturn(true);

        $checker = new LibPhoneNumberChecker($util);

        $this->assertTrue($checker->isValid('ES', '612345678'));
    }

    public function testInvalidNationalReturnsFalse(): void
    {
        $parsed = new PhoneNumber();
        $util = $this->createMock(PhoneNumberUtil::class);
        $util->method('parse')->willReturn($parsed);
        $util->method('isValidNumber')->willReturn(false);

        $checker = new LibPhoneNumberChecker($util);

        $this->assertFalse($checker->isValid('ES', '123'));
    }

    public function testParseFailureReturnsFalse(): void
    {
        $util = $this->createMock(PhoneNumberUtil::class);
        $util->method('parse')->willThrowException(new NumberParseException(0, 'bad'));

        $checker = new LibPhoneNumberChecker($util);

        $this->assertFalse($checker->isValid('ZZ', 'not-a-number'));
    }
}
