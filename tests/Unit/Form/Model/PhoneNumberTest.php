<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Form\Model;

use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use PHPUnit\Framework\TestCase;

final class PhoneNumberTest extends TestCase
{
    public function testGetE164AndSeparatedArray(): void
    {
        $phone = new PhoneNumber('ES', '+34', '612345678');

        $this->assertSame('+34612345678', $phone->getE164());
        $this->assertSame([
            'iso' => 'ES',
            'prefix' => '+34',
            'national_number' => '612345678',
        ], $phone->toSeparatedArray());
    }
}
