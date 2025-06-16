<?php

namespace App\Utils;

/**
 * Utilitaires de manipulation de nombres décimaux (représentés en string).
 * Idéal pour des montants monétaires précis (via BCMath).
 */
class MathUtils
{
    public const int SCALE = 2;

    /**
     * Compare deux montants.
     *
     * @return int -1 si $a < $b, 0 si égal, 1 si $a > $b
     */
    public static function comp(string $a, string $b): int
    {
        return bccomp($a, $b, self::SCALE);
    }

    public static function isNegative(string $amount): bool
    {
        return self::comp($amount, '0') < 0;
    }

    public static function isPositive(string $amount): bool
    {
        return self::comp($amount, '0') > 0;
    }

    public static function isZero(string $amount): bool
    {
        return 0 === self::comp($amount, '0');
    }

    public static function abs(string $amount): string
    {
        return self::isNegative($amount)
            ? bcmul($amount, '-1', self::SCALE)
            : $amount;
    }

    public static function negative(string $amount): string
    {
        return bcmul(self::abs($amount), '-1', self::SCALE);
    }

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        if (self::isZero($b)) {
            throw new \InvalidArgumentException('Division by zero.');
        }

        return bcdiv($a, $b, self::SCALE);
    }

    public static function isEqualToZero(string $amount): bool
    {
        return 0 === self::comp($amount, '0');
    }
}
