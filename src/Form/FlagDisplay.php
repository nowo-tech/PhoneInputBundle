<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Form;

/**
 * Country flag rendering modes in the selector widget.
 */
enum FlagDisplay: string
{
    case EMOJI = 'EMOJI';
    case CSS_ICON = 'CSS_ICON';
    case UX_ICON = 'UX_ICON';
    case NONE = 'NONE';
}
