<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Amount extends Constraint
{
    public string $message = 'La valeur "{{ value }}" n\'est pas un montant décimal valide.';

    public function validatedBy(): string
    {
        return AmountValidator::class;
    }
}
