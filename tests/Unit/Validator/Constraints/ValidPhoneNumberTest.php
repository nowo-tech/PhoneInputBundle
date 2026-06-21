<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Validator\Constraints;

use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use Nowo\PhoneInputBundle\Validator\Constraints\ValidPhoneNumber;
use Nowo\PhoneInputBundle\Validator\Constraints\ValidPhoneNumberValidator;
use PHPUnit\Framework\TestCase;

final class ValidPhoneNumberTest extends TestCase
{
    public function testDefaultOptions(): void
    {
        $constraint = new ValidPhoneNumber();

        $this->assertSame(PhoneValidationMode::COUNTRY, $constraint->mode);
        $this->assertSame('ES', $constraint->defaultCountry);
        $this->assertSame(ValidPhoneNumberValidator::class, $constraint->validatedBy());
    }

    public function testConstructorOptions(): void
    {
        $constraint = new ValidPhoneNumber(
            mode: PhoneValidationMode::PREFIX,
            message: 'Invalid phone',
            defaultCountry: 'fr',
        );

        $this->assertSame(PhoneValidationMode::PREFIX, $constraint->mode);
        $this->assertSame('Invalid phone', $constraint->message);
        $this->assertSame('FR', $constraint->defaultCountry);
    }

    public function testLegacyOptionsArrayNormalizesMode(): void
    {
        $constraint = new ValidPhoneNumber(options: ['mode' => 'prefix']);

        $this->assertSame(PhoneValidationMode::PREFIX, $constraint->mode);
    }
}
