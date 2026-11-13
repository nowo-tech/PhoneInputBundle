<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Phone;

use Nowo\PhoneInputBundle\Form\Model\PhoneNumber as FormPhoneNumber;
use Nowo\PhoneInputBundle\Phone\PhoneNumber;
use PHPUnit\Framework\TestCase;

/**
 * REQ-ARCH-001 rule 3: the value object lives outside the form layer; the deprecated `Form\Model\PhoneNumber` IS a
 * `Phone\PhoneNumber` and behaves exactly like the 1.6 class.
 */
final class PhoneNumberTest extends TestCase
{
    public function testValueObject(): void
    {
        $phone = new PhoneNumber('ES', '+34', '612345678');

        $this->assertSame('+34612345678', $phone->getE164());
        $this->assertSame(['iso' => 'ES', 'prefix' => '+34', 'national_number' => '612345678'], $phone->toSeparatedArray());
    }

    public function testFormModelIsEquivalent(): void
    {
        $legacy = new FormPhoneNumber('US', '+1', '2025550123');
        $value = new PhoneNumber('US', '+1', '2025550123');

        $parent = (new \ReflectionClass(FormPhoneNumber::class))->getParentClass();
        $this->assertNotFalse($parent);
        $this->assertSame(PhoneNumber::class, $parent->getName());
        $this->assertSame($value->getE164(), $legacy->getE164());
        $this->assertSame($value->toSeparatedArray(), $legacy->toSeparatedArray());
        $this->assertSame([$value->iso, $value->prefix, $value->nationalNumber], [$legacy->iso, $legacy->prefix, $legacy->nationalNumber]);
    }
}
