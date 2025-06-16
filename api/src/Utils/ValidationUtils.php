<?php

namespace App\Utils;

class ValidationUtils
{
    public static function isDecimalString(string $value): bool
    {
        return preg_match('/^-?\d+(\.\d{1,2})?$/', $value);
    }
}
