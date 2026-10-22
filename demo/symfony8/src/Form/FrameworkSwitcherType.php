<?php

declare(strict_types=1);

namespace App\Form;

use App\DemoFramework;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_flip;

/**
 * Demo CSS-framework switcher (GET ?framework=…), rendered as a Symfony Form instead of a raw <select>
 * (REQ-TWIG-005). The page script reloads on change; read-only, so no CSRF token.
 */
final class FrameworkSwitcherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('framework', ChoiceType::class, [
            'label'   => 'CSS framework',
            'choices' => array_flip(DemoFramework::choices()),
            'attr'    => ['id' => 'demo-framework-select', 'class' => 'form-select form-select-sm', 'aria-label' => 'CSS framework'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method'          => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
