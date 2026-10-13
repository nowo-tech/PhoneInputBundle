<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Form\DataTransformer;

use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Form\DataTransformer\PhoneNumberTransformer;
use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use Nowo\PhoneInputBundle\Form\ValueFormat;
use Nowo\PhoneInputBundle\Phone\E164Parser;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class PhoneNumberTransformerTest extends TestCase
{
    public function testTransformAndReverseConcatenatedFormat(): void
    {
        $transformer = $this->createTransformer(ValueFormat::CONCATENATED);

        $view = $transformer->transform('+34612345678');
        $this->assertSame('ES', $view['country_iso']);
        $this->assertSame('612345678', $view['national_number']);

        $model = $transformer->reverseTransform([
            'country_iso' => 'ES',
            'national_number' => '612345678',
        ]);

        $this->assertSame('+34612345678', $model);
    }

    public function testTransformAndReverseSeparatedFormat(): void
    {
        $transformer = $this->createTransformer(ValueFormat::SEPARATED);

        $model = $transformer->reverseTransform([
            'country_iso' => 'FR',
            'national_number' => '612345678',
        ]);

        $this->assertSame([
            'iso' => 'FR',
            'prefix' => '+33',
            'national_number' => '612345678',
        ], $model);
    }

    public function testTransformAndReverseObjectFormat(): void
    {
        $transformer = $this->createTransformer(ValueFormat::OBJECT);

        $phone = new PhoneNumber('GB', '+44', '7911123456');
        $view = $transformer->transform($phone);

        $this->assertSame('GB', $view['country_iso']);
        $this->assertSame('7911123456', $view['national_number']);

        $model = $transformer->reverseTransform([
            'country_iso' => 'GB',
            'national_number' => '7911123456',
        ]);

        $this->assertInstanceOf(PhoneNumber::class, $model);
        $this->assertSame('+447911123456', $model->getE164());
    }

    public function testReverseTransformThrowsForUnknownCountry(): void
    {
        $transformer = $this->createTransformer(ValueFormat::CONCATENATED);

        $this->expectException(TransformationFailedException::class);

        $transformer->reverseTransform([
            'country_iso' => 'ZZ',
            'national_number' => '612345678',
        ]);
    }

    public function testReverseTransformThrowsForNonArrayValue(): void
    {
        $transformer = $this->createTransformer(ValueFormat::CONCATENATED);

        $this->expectException(TransformationFailedException::class);

        $transformer->reverseTransform('invalid');
    }

    public function testEmptyObjectFormatReturnsNull(): void
    {
        $transformer = $this->createTransformer(ValueFormat::OBJECT);

        $this->assertNull($transformer->reverseTransform([
            'country_iso' => 'ES',
            'national_number' => '',
        ]));
    }

    public function testEmptySeparatedFormatReturnsEmptyArray(): void
    {
        $transformer = $this->createTransformer(ValueFormat::SEPARATED);

        $this->assertSame([
            'iso' => '',
            'prefix' => '',
            'national_number' => '',
        ], $transformer->reverseTransform([
            'country_iso' => 'ES',
            'national_number' => '',
        ]));
    }

    public function testTransformNullUsesDefaultCountry(): void
    {
        $transformer = $this->createTransformer(ValueFormat::CONCATENATED);

        $view = $transformer->transform(null);

        $this->assertSame('ES', $view['country_iso']);
        $this->assertSame('', $view['national_number']);
    }

    public function testTransformUnsupportedValueThrows(): void
    {
        $transformer = $this->createTransformer(ValueFormat::CONCATENATED);

        $this->expectException(TransformationFailedException::class);

        /* @phpstan-ignore argument.type */
        $transformer->transform(12345);
    }

    public function testTransformArrayValue(): void
    {
        $transformer = $this->createTransformer(ValueFormat::CONCATENATED);

        $view = $transformer->transform([
            'iso' => 'FR',
            'national_number' => '612345678',
        ]);

        $this->assertSame('FR', $view['country_iso']);
        $this->assertSame('612345678', $view['national_number']);
    }

    public function testWithoutSelectorKeepsE164StringInView(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $view = $transformer->transform('+34612345678');

        $this->assertSame('+34612345678', $view['national_number']);
    }

    public function testWithoutCountryPrefixSelector(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $view = $transformer->transform('+34612345678');
        $this->assertArrayNotHasKey('country_iso', $view);
        $this->assertSame('+34612345678', $view['national_number']);

        $model = $transformer->reverseTransform(['national_number' => '612345678']);
        $this->assertSame('+34612345678', $model);
    }

    public function testWithoutSelectorReverseTransformE164NationalNumber(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $model = $transformer->reverseTransform(['national_number' => '+34612345678']);

        $this->assertSame('+34612345678', $model);
    }

    public function testWithoutSelectorReverseTransformUsesDefaultCountryForNationalNumber(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $model = $transformer->reverseTransform(['national_number' => '612345678']);

        $this->assertSame('+34612345678', $model);
    }

    public function testWithoutSelectorReverseTransformReturnsEmptyModelForBlankNationalNumber(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $this->assertSame('', $transformer->reverseTransform(['national_number' => '']));
    }

    public function testWithoutSelectorReverseTransformParsesE164NationalNumber(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $this->assertSame('+34612345678', $transformer->reverseTransform(['national_number' => '+34612345678']));
    }

    public function testBuildModelWithoutSelectorParsesLeadingPlusViaReflection(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $method = new \ReflectionMethod(PhoneNumberTransformer::class, 'buildModelWithoutSelector');
        $result = $method->invoke($transformer, '+34612345678');

        $this->assertSame('+34612345678', $result);
    }

    public function testWithoutSelectorReverseTransformUsesDefaultCountryWhenIsoMissing(): void
    {
        $provider = TestFixtures::countryProvider(defaultCountry: 'ES');

        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ZZ',
        );

        $this->assertSame('+34612345678', $transformer->reverseTransform(['national_number' => '612345678']));
    }

    /**
     * Regression: without the prefix selector, +447911123456 with default country ES was saved as +347911123456.
     */
    public function testWithoutSelectorKeepsNonDefaultInternationalPrefix(): void
    {
        $provider = TestFixtures::countryProvider();
        $factory = static fn (ValueFormat $format): PhoneNumberTransformer => new PhoneNumberTransformer(
            valueFormat: $format,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $this->assertSame('+447911123456', $factory(ValueFormat::CONCATENATED)->reverseTransform(['national_number' => '+44 7911 123456']));
        $this->assertSame(
            ['iso' => 'GB', 'prefix' => '+44', 'national_number' => '7911123456'],
            $factory(ValueFormat::SEPARATED)->reverseTransform(['national_number' => '+447911123456']),
        );

        $object = $factory(ValueFormat::OBJECT)->reverseTransform(['national_number' => '+447911123456']);
        $this->assertInstanceOf(PhoneNumber::class, $object);
        $this->assertSame('GB', $object->iso);
        $this->assertSame('+44', $object->prefix);
        $this->assertSame('7911123456', $object->nationalNumber);
    }

    public function testWithoutSelectorStripsLeadingZerosFromNationalNumber(): void
    {
        $provider = TestFixtures::countryProvider();
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::CONCATENATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );

        $this->assertSame('+34612345678', $transformer->reverseTransform(['national_number' => ' 0612 345 678 ']));
    }

    public function testConcatenatedSharedDialCodeKeepsTheSelectedCountry(): void
    {
        $transformer = $this->createSharedDialCodeTransformer(ValueFormat::SEPARATED, 'ES', []);

        $model = $transformer->reverseTransform(['country_iso' => 'CA', 'national_number' => '+1 416 555 0123']);

        $this->assertSame(['iso' => 'CA', 'prefix' => '+1', 'national_number' => '4165550123'], $model);
    }

    public function testTypedPrefixOfAnotherCountryWinsOverTheSelector(): void
    {
        $transformer = $this->createSharedDialCodeTransformer(ValueFormat::CONCATENATED, 'ES', []);

        $this->assertSame('+34612345678', $transformer->reverseTransform(['country_iso' => 'CA', 'national_number' => '+34 612 345 678']));
    }

    public function testTypedPrefixAloneIsEmpty(): void
    {
        $transformer = $this->createSharedDialCodeTransformer(ValueFormat::CONCATENATED, 'ES', []);

        $this->assertSame('', $transformer->reverseTransform(['country_iso' => 'CA', 'national_number' => '+1']));
    }

    public function testConcatenatedTransformPrefersTheDefaultCountryForASharedDialCode(): void
    {
        $transformer = $this->createSharedDialCodeTransformer(ValueFormat::CONCATENATED, 'CA', []);

        $this->assertSame(['country_iso' => 'CA', 'national_number' => '4165550123'], $transformer->transform('+14165550123'));
    }

    public function testConcatenatedTransformPrefersSelectorCountriesForASharedDialCode(): void
    {
        $transformer = $this->createSharedDialCodeTransformer(ValueFormat::CONCATENATED, 'ES', ['ES', 'CA']);

        $this->assertSame(['country_iso' => 'CA', 'national_number' => '4165550123'], $transformer->transform('+14165550123'));
    }

    public function testWithoutSelectorTheDefaultCountryIsKeptForASharedDialCode(): void
    {
        $provider = new CountryProvider(__DIR__.'/../../../Fixtures/countries-multi-plus-one.json', 'CA');
        $transformer = new PhoneNumberTransformer(
            valueFormat: ValueFormat::SEPARATED,
            countryPrefixSelector: false,
            countryProvider: $provider,
            e164Parser: new E164Parser($provider),
            defaultCountryIso: 'CA',
        );

        $this->assertSame(
            ['iso' => 'CA', 'prefix' => '+1', 'national_number' => '4165550123'],
            $transformer->reverseTransform(['national_number' => '+14165550123']),
        );
    }

    /**
     * @param list<string> $selectorIsos
     */
    private function createSharedDialCodeTransformer(ValueFormat $format, string $default, array $selectorIsos): PhoneNumberTransformer
    {
        $provider = new CountryProvider(__DIR__.'/../../../Fixtures/countries-multi-plus-one.json', $default);

        return new PhoneNumberTransformer(
            valueFormat: $format,
            countryPrefixSelector: true,
            countryProvider: $provider,
            e164Parser: new E164Parser($provider),
            defaultCountryIso: $default,
            preferredCountryIsos: $selectorIsos,
        );
    }

    private function createTransformer(ValueFormat $format): PhoneNumberTransformer
    {
        $provider = TestFixtures::countryProvider();

        return new PhoneNumberTransformer(
            valueFormat: $format,
            countryPrefixSelector: true,
            countryProvider: $provider,
            e164Parser: TestFixtures::e164Parser($provider),
            defaultCountryIso: 'ES',
        );
    }
}
