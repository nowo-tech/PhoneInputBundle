<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Validator\Constraints;

use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use Nowo\PhoneInputBundle\Phone\PhoneValidator;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ValidPhoneNumberValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PhoneValidator $phoneValidator,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidPhoneNumber) {
            throw new UnexpectedTypeException($constraint, ValidPhoneNumber::class);
        }

        if (PhoneValidationMode::NONE === $constraint->mode) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value) && !\is_array($value) && !$value instanceof PhoneNumber) {
            throw new UnexpectedValueException($value, 'string|array|PhoneNumber|null');
        }

        if (!$this->phoneValidator->isValid($value, $constraint->mode, $constraint->defaultCountry)) {
            $this->context->buildViolation($constraint->message)
                ->setCode(ValidPhoneNumber::INVALID_PHONE_NUMBER_ERROR)
                ->addViolation();
        }
    }
}
