<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle;

use Nowo\PhoneInputBundle\DependencyInjection\Compiler\TwigPathsPass;
use Nowo\PhoneInputBundle\DependencyInjection\NowoPhoneInputExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle for phone input fields with optional country prefix selector.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 * @copyright 2026 Nowo.tech
 */
class NowoPhoneInputBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TwigPathsPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!$this->extension instanceof ExtensionInterface) {
            $this->extension = new NowoPhoneInputExtension();
        }

        $extension = $this->extension;

        /* @phpstan-ignore identical.alwaysFalse */
        return false === $extension ? null : $extension;
    }
}
