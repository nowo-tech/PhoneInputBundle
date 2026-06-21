<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Form\DataTransformer;

use Nowo\PhoneInputBundle\Form\DataTransformer\PhoneNumberTransformer;
use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use Nowo\PhoneInputBundle\Form\ValueFormat;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use PHPUnit\Framework\TestCase;

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

        $this->expectException(\Symfony\Component\Form\Exception\TransformationFailedException::class);

        $transformer->reverseTransform([
            'country_iso' => 'ZZ',
            'national_number' => '612345678',
        ]);
    }

    public function testReverseTransformThrowsForNonArrayValue(): void
    {
        $transformer = $this->createTransformer(ValueFormat::CONCATENATED);

        $this->expectException(\Symfony\Component\Form\Exception\TransformationFailedException::class);

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

        $this->expectException(\Symfony\Component\Form\Exception\TransformationFailedException::class);

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
