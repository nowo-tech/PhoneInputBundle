<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\DemoFormData;
use App\Form\DemoFormType;
use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Demo controller showcasing all PhoneType format combinations.
 */
class DemoController extends AbstractController
{
    #[Route('/', name: 'demo_form')]
    public function form(Request $request): Response
    {
        $form = $this->createForm(DemoFormType::class, DemoFormData::defaults());

        $form->handleRequest($request);

        $submitted = false;
        /** @var array<string, mixed>|null $data */
        $data = null;
        /** @var array<string, array{type: string, display: string}>|null $formattedResults */
        $formattedResults = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $submitted = true;
            $data = $form->getData();
            $formattedResults = $this->formatSubmittedData($data ?? []);
        }

        return $this->render('demo/form.html.twig', [
            'form'             => $form,
            'submitted'        => $submitted,
            'data'             => $data,
            'formattedResults' => $formattedResults,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, array{type: string, display: string}>
     */
    private function formatSubmittedData(array $data): array
    {
        unset($data['submit']);

        $results = [];
        foreach ($data as $field => $value) {
            if ($value instanceof PhoneNumber) {
                $results[$field] = [
                    'type'    => 'PhoneNumber',
                    'display' => sprintf(
                        'iso=%s, prefix=%s, national=%s, e164=%s',
                        $value->iso,
                        $value->prefix,
                        $value->nationalNumber,
                        $value->getE164(),
                    ),
                ];
                continue;
            }

            if (is_array($value)) {
                $results[$field] = [
                    'type'    => 'array',
                    'display' => json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            $results[$field] = [
                'type'    => is_string($value) ? 'string' : get_debug_type($value),
                'display' => (string) $value,
            ];
        }

        return $results;
    }
}
