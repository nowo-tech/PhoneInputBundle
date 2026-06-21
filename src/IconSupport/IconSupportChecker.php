<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\IconSupport;

/**
 * Detects whether Symfony UX Icons and HTTP Client are available for flag rendering.
 */
final class IconSupportChecker
{
    private const UX_ICONS_RUNTIME_CLASSES = [
        'Symfony\UX\Icons\Twig\UXIconRuntime',
        'Symfony\UX\Icons\Twig\UXIconsRuntime',
    ];

    private const HTTP_CLIENT_INTERFACE = 'Symfony\Contracts\HttpClient\HttpClientInterface';

    public function __construct(
        private readonly ?bool $uxIconsAvailable = null,
        private readonly ?bool $httpClientAvailable = null,
        /** @var \Closure(string): bool|null */
        private readonly ?\Closure $classExistsChecker = null,
    ) {
    }

    public function isUxIconsAvailable(): bool
    {
        if (null !== $this->uxIconsAvailable) {
            return $this->uxIconsAvailable;
        }

        foreach (self::UX_ICONS_RUNTIME_CLASSES as $class) {
            if ($this->runtimeClassExists($class)) {
                return true;
            }
        }

        return false;
    }

    private function runtimeClassExists(string $class): bool
    {
        if ($this->classExistsChecker instanceof \Closure) {
            return ($this->classExistsChecker)($class);
        }

        return class_exists($class);
    }

    public function isHttpClientAvailable(): bool
    {
        return $this->httpClientAvailable ?? interface_exists(self::HTTP_CLIENT_INTERFACE);
    }

    public function isIconRenderingSupported(): bool
    {
        return $this->isUxIconsAvailable() && $this->isHttpClientAvailable();
    }
}
