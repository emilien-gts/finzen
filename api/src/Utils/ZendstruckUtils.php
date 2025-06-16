<?php

namespace App\Utils;

use Random\RandomException;

class ZendstruckUtils
{
    /**
     * Generate a random amount between two values, formatted as a decimal string with 2 digits.
     *
     * @param float $min Minimum amount (inclusive)
     * @param float $max Maximum amount (inclusive)
     *
     * @return string The amount as a string with 2 decimal places
     *
     * @throws \InvalidArgumentException If $min is greater than $max
     */
    public static function generateAmount(float $min, float $max): string
    {
        if ($min > $max) {
            throw new \InvalidArgumentException('Minimum value cannot be greater than maximum value.');
        }

        $minCents = (int) round($min * 100);
        $maxCents = (int) round($max * 100);

        try {
            $randomCents = random_int($minCents, $maxCents);
        } catch (RandomException $e) {
            $randomCents = '10';
        }

        return number_format($randomCents / 100, 2, '.', '');
    }
}
