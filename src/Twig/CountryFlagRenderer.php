<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Twig;

use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;

/**
 * Renders country flags for the phone prefix selector without requiring Twig UX icon functions.
 */
final class CountryFlagRenderer
{
    public function __construct(
        private readonly IconSupportChecker $iconSupportChecker,
        private readonly ?object $iconRenderer = null,
    ) {
    }

    /**
     * @param array{iso: string, name?: string, dial_code?: string, flag?: string, flag_icon?: string} $country
     */
    public function render(array $country, string $flagDisplay = FlagDisplay::CSS_ICON->value): string
    {
        if (FlagDisplay::NONE->value === $flagDisplay) {
            return '';
        }

        $iso = strtolower($country['iso']);

        if (
            FlagDisplay::UX_ICON->value === $flagDisplay
            && $this->iconSupportChecker->isIconRenderingSupported()
            && null !== $this->iconRenderer
        ) {
            $iconName = $country['flag_icon'] ?? 'circle-flags:'.$iso;

            try {
                $html = $this->renderUxIcon($iconName);
                if ('' !== $html) {
                    return $html;
                }
            } catch (\Throwable) {
                // Fall back to bundled CSS flags when UX Icons cannot render the icon.
            }
        }

        if (FlagDisplay::CSS_ICON->value === $flagDisplay || FlagDisplay::UX_ICON->value === $flagDisplay) {
            return \sprintf(
                '<span class="nowo-phone-input__flag fi fi-%s" aria-hidden="true"></span>',
                htmlspecialchars($iso, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8'),
            );
        }

        $emoji = $country['flag'] ?? '';

        return \sprintf(
            '<span class="nowo-phone-input__flag nowo-phone-input__flag--emoji" aria-hidden="true">%s</span>',
            htmlspecialchars($emoji, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8'),
        );
    }

    private function renderUxIcon(string $iconName): string
    {
        if (!\is_object($this->iconRenderer) || !method_exists($this->iconRenderer, 'renderIcon')) {
            return '';
        }

        /** @var callable(string, array<string, mixed>): string $renderIcon */
        $renderIcon = [$this->iconRenderer, 'renderIcon'];

        return $renderIcon($iconName, [
            'class' => 'nowo-phone-input__flag nowo-phone-input__flag--ux',
            'height' => '1.25rem',
            'width' => '1.25rem',
        ]);
    }
}
