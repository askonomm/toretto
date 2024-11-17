<?php

declare(strict_types=1);

namespace Asko\Toretto;

interface ExpressionModifier
{
    /**
     * Modifies the provided value based on given options.
     *
     * @param mixed $value The value to be modified.
     * @param List<string>|null $opts Optional settings that can influence how the value is modified.
     * @return mixed The modified value.
     */
    public static function modify(mixed $value, ?array $opts = null): mixed;
}