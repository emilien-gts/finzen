<?php

namespace App\Validator\Constraints;

use App\Utils\ValidationUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AmountValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Amount) {
            throw new \UnexpectedValueException('Expected an Amount object');
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            return; // handle by assert\type
        }

        if (!ValidationUtils::isDecimalString($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
