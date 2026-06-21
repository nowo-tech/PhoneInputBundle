<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Validator\Constraints;

use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use Symfony\Component\Validator\Constraint;

/**
 * Validates a phone value (E.164 string, separated array or PhoneNumber object).
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class ValidPhoneNumber extends Constraint
{
    public const INVALID_PHONE_NUMBER_ERROR = 'nowo_phone_input.invalid_phone_number';

    protected const ERROR_NAMES = [
        self::INVALID_PHONE_NUMBER_ERROR => 'INVALID_PHONE_NUMBER_ERROR',
    ];

    public string $message = 'The phone number is invalid for the selected country.';

    public PhoneValidationMode $mode = PhoneValidationMode::COUNTRY;

    public string $defaultCountry = 'ES';

    /**
     * @param array<string, mixed>|null $options
     */
    public function __construct(
        ?PhoneValidationMode $mode = null,
        ?string $message = null,
        ?string $defaultCountry = null,
        ?array $groups = null,
        mixed $payload = null,
        ?array $options = null,
    ) {
        if (\is_array($options) && (isset($options['mode']) && \is_string($options['mode']))) {
            $options['mode'] = PhoneValidationMode::from(strtoupper($options['mode']));
        }

        parent::__construct($options ?? [], $groups, $payload);

        if ($mode instanceof PhoneValidationMode) {
            $this->mode = $mode;
        } elseif (\is_array($options) && isset($options['mode']) && $options['mode'] instanceof PhoneValidationMode) {
            $this->mode = $options['mode'];
        }

        if (null !== $message) {
            $this->message = $message;
        }

        if (null !== $defaultCountry) {
            $this->defaultCountry = strtoupper($defaultCountry);
        }
    }

    public function validatedBy(): string
    {
        return ValidPhoneNumberValidator::class;
    }
}
