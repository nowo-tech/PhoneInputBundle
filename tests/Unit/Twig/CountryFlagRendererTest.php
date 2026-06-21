<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Twig;

use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use PHPUnit\Framework\TestCase;

final class CountryFlagRendererTest extends TestCase
{
    public function testRenderCssIcon(): void
    {
        $renderer = new CountryFlagRenderer(new IconSupportChecker());

        $html = $renderer->render(['iso' => 'ES'], FlagDisplay::CSS_ICON->value);

        $this->assertSame(
            '<span class="nowo-phone-input__flag fi fi-es" aria-hidden="true"></span>',
            $html,
        );
    }

    public function testRenderEmoji(): void
    {
        $renderer = new CountryFlagRenderer(new IconSupportChecker());

        $html = $renderer->render(['iso' => 'ES', 'flag' => '🇪🇸'], FlagDisplay::EMOJI->value);

        $this->assertSame(
            '<span class="nowo-phone-input__flag nowo-phone-input__flag--emoji" aria-hidden="true">🇪🇸</span>',
            $html,
        );
    }

    public function testRenderNoneReturnsEmptyString(): void
    {
        $renderer = new CountryFlagRenderer(new IconSupportChecker());

        $this->assertSame('', $renderer->render(['iso' => 'ES'], FlagDisplay::NONE->value));
    }

    public function testUxIconFallsBackToCssWhenUxIconsUnavailable(): void
    {
        $renderer = new CountryFlagRenderer(new IconSupportChecker(uxIconsAvailable: false, httpClientAvailable: false));

        $html = $renderer->render(['iso' => 'ES', 'flag_icon' => 'circle-flags:es'], FlagDisplay::UX_ICON->value);

        $this->assertSame(
            '<span class="nowo-phone-input__flag fi fi-es" aria-hidden="true"></span>',
            $html,
        );
    }

    public function testUxIconUsesIconRendererWhenAvailable(): void
    {
        $iconRenderer = new class {
            public function renderIcon(string $name, array $attributes = []): string
            {
                return '<svg data-icon="'.$name.'" class="'.$attributes['class'].'"></svg>';
            }
        };

        $renderer = new CountryFlagRenderer(
            new IconSupportChecker(uxIconsAvailable: true, httpClientAvailable: true),
            $iconRenderer,
        );

        $html = $renderer->render(['iso' => 'ES', 'flag_icon' => 'circle-flags:es'], FlagDisplay::UX_ICON->value);

        $this->assertStringContainsString('data-icon="circle-flags:es"', $html);
        $this->assertStringContainsString('nowo-phone-input__flag--ux', $html);
    }
}
