<?php

namespace App\Model;

use App\Validator\Constraints\Amount;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

class AccountBalanceDto
{
    #[Assert\NotNull]
    #[Assert\Type(Types::STRING)]
    #[Amount]
    public ?string $balance = null;
}
