<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Form\Model;

use Nowo\PhoneInputBundle\Phone\PhoneNumber as PhoneNumberValue;

/**
 * Phone number value object with separated country prefix and national number.
 *
 * Kept for backward compatibility: `ValueFormat::OBJECT` still returns this class, and it behaves exactly like its
 * parent. Type your code against {@see PhoneNumberValue} (`Nowo\PhoneInputBundle\Phone\PhoneNumber`).
 *
 * @deprecated since 1.7.0, use Nowo\PhoneInputBundle\Phone\PhoneNumber; removed in 2.0 (ValueFormat::OBJECT then
 *             returns the Phone\PhoneNumber)
 */
final readonly class PhoneNumber extends PhoneNumberValue
{
}
