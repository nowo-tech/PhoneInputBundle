<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Phone;

use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use Nowo\PhoneInputBundle\Phone\E164Parser;
use Nowo\PhoneInputBundle\Phone\PhonePattern;
use Nowo\PhoneInputBundle\Phone\PhonePatternCatalog;
use Nowo\PhoneInputBundle\Phone\PhoneValidator;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use PHPUnit\Framework\TestCase;

final class PhoneValidatorTest extends TestCase
{
    private PhoneValidator $validator;

    protected function setUp(): void
    {
        $provider = TestFixtures::countryProvider();
        $this->validator = new PhoneValidator(
            new E164Parser($provider),
            new PhonePatternCatalog(
                __DIR__.'/../../../src/Resources/data/phone_patterns.json',
                $provider,
            ),
            useLibPhoneNumber: false,
        );
    }

    public function testValidSpanishMobileInCountryMode(): void
    {
        $this->assertTrue($this->validator->isValid('+34612345678', PhoneValidationMode::COUNTRY, 'ES'));
    }

    public function testInvalidSpanishMobileInCountryMode(): void
    {
        $this->assertFalse($this->validator->isValid('+3412345', PhoneValidationMode::COUNTRY, 'ES'));
    }

    public function testValidSpanishMobileInPrefixMode(): void
    {
        $this->assertTrue($this->validator->isValid([
            'iso' => 'ES',
            'prefix' => '+34',
            'national_number' => '612345678',
        ], PhoneValidationMode::PREFIX, 'ES'));
    }

    public function testValidUsNumberUsesPrefixRules(): void
    {
        $this->assertTrue($this->validator->isValid('+12125551234', PhoneValidationMode::PREFIX, 'US'));
    }

    public function testPhoneNumberObjectIsValidated(): void
    {
        $this->assertTrue($this->validator->isValid(
            new PhoneNumber('ES', '+34', '612345678'),
            PhoneValidationMode::COUNTRY,
            'ES',
        ));
    }

    public function testEmptyValueIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('', PhoneValidationMode::COUNTRY, 'ES'));
        $this->assertTrue($this->validator->isValid([
            'iso' => 'ES',
            'prefix' => '+34',
            'national_number' => '',
        ], PhoneValidationMode::COUNTRY, 'ES'));
        $this->assertTrue($this->validator->isValid(new PhoneNumber('ES', '+34', ''), PhoneValidationMode::COUNTRY, 'ES'));
    }

    public function testArrayWithoutPrefixUsesParser(): void
    {
        $this->assertTrue($this->validator->isValid([
            'iso' => 'ES',
            'national_number' => '612345678',
        ], PhoneValidationMode::COUNTRY, 'ES'));
    }

    public function testNoneModeUsesDefaultPattern(): void
    {
        $this->assertTrue($this->validator->isValid('1234567', PhoneValidationMode::NONE, 'ES'));
    }

    public function testNationalNumberWithPlusPrefixIsNormalized(): void
    {
        $this->assertTrue($this->validator->isValid([
            'iso' => 'ES',
            'prefix' => '+34',
            'national_number' => '+34612345678',
        ], PhoneValidationMode::COUNTRY, 'ES'));
    }

    public function testInvalidValueTypeUsesEmptyParts(): void
    {
        $this->assertTrue($this->validator->isValid(12345, PhoneValidationMode::COUNTRY, 'ES'));
    }

    public function testUnreadablePatternsFileUsesFallback(): void
    {
        $provider = TestFixtures::countryProvider();
        $validator = new PhoneValidator(
            new E164Parser($provider),
            new PhonePatternCatalog('/tmp/nowo-phone-patterns-missing.json', $provider),
            useLibPhoneNumber: false,
        );

        $this->assertTrue($validator->isValid('12345678', PhoneValidationMode::COUNTRY, 'ES'));
    }

    public function testUsesLibPhoneNumberWhenAvailable(): void
    {
        if (!class_exists(\libphonenumber\PhoneNumberUtil::class, false)) {
            eval(<<<'PHP'
namespace libphonenumber;
final class PhoneNumberUtil
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function parse(string $number, string $region): object
    {
        if ('999999999' === $number) {
            throw new \RuntimeException('parse failed');
        }

        return (object) ['number' => $number, 'region' => $region];
    }

    public function isValidNumber(object $number): bool
    {
        return is_object($number) && property_exists($number, 'number') && '111111111' !== $number->number;
    }
}
PHP);
        }

        $provider = TestFixtures::countryProvider();
        $validator = new PhoneValidator(
            new E164Parser($provider),
            new PhonePatternCatalog(
                __DIR__.'/../../../src/Resources/data/phone_patterns.json',
                $provider,
            ),
            useLibPhoneNumber: true,
        );

        // Digit-only nationals (letters stripped; leading zeros trimmed by normalizeNationalNumber).
        $this->assertTrue($validator->isValid(['iso' => 'ES', 'prefix' => '+34', 'national_number' => '612345678'], PhoneValidationMode::COUNTRY, 'ES'));
        $this->assertFalse($validator->isValid(['iso' => 'ES', 'prefix' => '+34', 'national_number' => '111111111'], PhoneValidationMode::COUNTRY, 'ES'));
        $this->assertFalse($validator->isValid(['iso' => 'ES', 'prefix' => '+34', 'national_number' => '999999999'], PhoneValidationMode::COUNTRY, 'ES'));
    }
}

final class PhonePatternTest extends TestCase
{
    public function testPatternMatchesWithinLengthBounds(): void
    {
        $pattern = new PhonePattern(9, 9, '^[6789]\d{8}$');

        $this->assertTrue($pattern->matches('612345678'));
        $this->assertFalse($pattern->matches('512345678'));
        $this->assertFalse($pattern->matches('6123456'));
    }
}
