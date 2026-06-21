<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Form;

/**
 * Country prefix display modes in the selector widget.
 */
enum PrefixDisplay: string
{
    case FULL = 'FULL';
    case PREFIX_ONLY = 'PREFIX_ONLY';
    case FLAG_ONLY = 'FLAG_ONLY';
    case FLAG_AND_PREFIX = 'FLAG_AND_PREFIX';
    case ISO_AND_PREFIX = 'ISO_AND_PREFIX';
}
