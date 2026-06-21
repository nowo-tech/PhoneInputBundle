<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig helpers for rendering phone prefix country flags.
 */
final class CountryFlagExtension extends AbstractExtension
{
    public function __construct(
        private readonly CountryFlagRenderer $countryFlagRenderer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nowo_phone_country_flag',
                $this->renderCountryFlag(...),
                ['is_safe' => ['html']],
            ),
        ];
    }

    /**
     * @param array{iso: string, name?: string, dial_code?: string, flag?: string, flag_icon?: string} $country
     */
    public function renderCountryFlag(array $country, string $flagDisplay = 'CSS_ICON'): string
    {
        return $this->countryFlagRenderer->render($country, $flagDisplay);
    }
}
