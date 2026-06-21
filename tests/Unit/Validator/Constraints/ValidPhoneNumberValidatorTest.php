<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Validator\Constraints;

use Nowo\PhoneInputBundle\Phone\E164Parser;
use Nowo\PhoneInputBundle\Phone\PhonePatternCatalog;
use Nowo\PhoneInputBundle\Phone\PhoneValidator;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use Nowo\PhoneInputBundle\Validator\Constraints\ValidPhoneNumber;
use Nowo\PhoneInputBundle\Validator\Constraints\ValidPhoneNumberValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ValidPhoneNumberValidatorTest extends TestCase
{
    private ValidPhoneNumberValidator $constraintValidator;

    protected function setUp(): void
    {
        $provider = TestFixtures::countryProvider();
        $phoneValidator = new PhoneValidator(
            new E164Parser($provider),
            new PhonePatternCatalog(
                __DIR__.'/../../../../src/Resources/data/phone_patterns.json',
                $provider,
            ),
            useLibPhoneNumber: false,
        );

        $this->constraintValidator = new ValidPhoneNumberValidator($phoneValidator);
    }

    public function testValidValueDoesNotBuildViolation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $this->constraintValidator->initialize($context);
        $this->constraintValidator->validate('+34612345678', new ValidPhoneNumber());
    }

    public function testInvalidValueBuildsViolation(): void
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->method('setCode')->willReturnSelf();
        $builder->expects($this->once())->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->with('The phone number is invalid for the selected country.')
            ->willReturn($builder);

        $this->constraintValidator->initialize($context);
        $this->constraintValidator->validate('+34123', new ValidPhoneNumber());
    }

    public function testNoneModeSkipsValidation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $this->constraintValidator->initialize($context);
        $this->constraintValidator->validate('+34123', new ValidPhoneNumber(mode: PhoneValidationMode::NONE));
    }

    public function testNullAndEmptyValuesAreSkipped(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $this->constraintValidator->initialize($context);
        $this->constraintValidator->validate(null, new ValidPhoneNumber());
        $this->constraintValidator->validate('', new ValidPhoneNumber());
    }

    public function testWrongConstraintTypeThrows(): void
    {
        $this->expectException(\Symfony\Component\Validator\Exception\UnexpectedTypeException::class);

        $this->constraintValidator->validate('+34612345678', new class extends \Symfony\Component\Validator\Constraint {
        });
    }

    public function testUnexpectedValueTypeThrows(): void
    {
        $this->expectException(\Symfony\Component\Validator\Exception\UnexpectedValueException::class);

        $this->constraintValidator->validate(12345, new ValidPhoneNumber());
    }
}
