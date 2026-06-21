<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Form;

/**
 * Supported model value formats for PhoneType.
 */
enum ValueFormat: string
{
    case CONCATENATED = 'CONCATENATED';
    case SEPARATED = 'SEPARATED';
    case OBJECT = 'OBJECT';
}
