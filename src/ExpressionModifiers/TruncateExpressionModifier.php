<?php

declare(strict_types=1);

namespace Asko\Toretto\ExpressionModifiers;

use Asko\Toretto\Core\Attributes\Name;
use Asko\Toretto\ExpressionModifier;

#[Name('truncate')]
class TruncateExpressionModifier implements ExpressionModifier
{
    /**
     * Truncates the provided value based on the given options.
     *
     * @param mixed $value The value to be modified.
     * @param List<string>|null $opts Optional parameter. If numeric, it will be used to truncate the expression.
     * @return mixed The modified expression.
     */
    public static function modify(mixed $value, ?array $opts = null): mixed
    {
        if (is_string($value)) {
            return self::string($value, $opts);
        }

        if (is_array($value)) {
            /** @var array<int|mixed, mixed> $value */
            return self::collection($value, $opts);
        }

        return $value;
    }

    /**
     * Truncates the given string value based on the specified options.
     *
     * @param string $value The string to be truncated.
     * @param List<string>|null $opts Optional parameter. If numeric, the value will be used as the truncation length.
     * @return string The truncated string with ellipsis if truncated, or the original string.
     */
    private static function string(string $value, ?array $opts = null): string
    {
        if ($opts && is_numeric($opts[0])) {
            $truncate = (int) $opts[0] - 3;

            return substr($value, 0, $truncate) . '...';
        }

        return $value;
    }

    /**
     * @param array<int|mixed, mixed> $value
     * @param List<string>|null $opts
     * @return array<int|mixed, mixed>
     */
    private static function collection(array $value, ?array $opts = null): array
    {
        if ($opts && is_numeric($opts[0])) {
            $truncate = (int) $opts[0];

            return array_slice($value, 0, $truncate);
        }

        return $value;
    }
}