<?php

declare(strict_types=1);

namespace Asko\Toretto;

use Asko\Toretto\Core\Attributes\Name;

class ExpressionParser
{
    /** @var array<string, mixed> */
    public array $data = [];

    /** @var List<ExpressionModifier> */
    public array $expressionModifiers = [];

    /**
     * @param string $expression
     * @param bool $serialize
     * @return mixed
     */
    public function parse(string $expression, bool $serialize = false): mixed
    {
        // If there are no interpolations, then the whole thing is an interpolation.
        if (!preg_match('/{.*?}/', $expression)) {
            $parsedInterpolation = $this->parseInterpolation($expression);

            if ($serialize) {
                return $this->serialize($parsedInterpolation);
            }

            return $parsedInterpolation;
        }

        // Otherwise, interpolate the parts within {}.
        return preg_replace_callback('/{(?<var>.*?)}/', [$this, 'pregReplaceCallbackFn'], $expression);
    }

    /**
     * @param array<string> $matches
     * @return string
     */
    private function pregReplaceCallbackFn(array $matches): string
    {
        $parsedExpression = $this->parseInterpolation($matches['var']);

        return $this->serialize($parsedExpression);
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function serialize(mixed $value): string
    {
        /** @var string $result */
        $result = match(true) {
            is_string($value) => $value,
            is_numeric($value) => (string) $value,
            is_bool($value) => $value ? 'true' : 'false',
            is_null($value) => 'null',
            is_array($value), is_object($value) => json_encode($value),
            default => ''
        };

        return $result;
    }

    /**
     * @param string $interpolation
     * @return mixed
     */
    private function parseInterpolation(string $interpolation): mixed
    {
        $parts = array_map(fn(string $item) => trim($item), explode('|', $interpolation));
        $var = $parts[0];
        $modifiers = count($parts) > 1 ? array_slice($parts, 1) : [];
        $value = $this->varVal($var);

        foreach ($modifiers as $modifier) {
            $modifier_parts = array_map(fn(string $item) => trim($item), explode(':', $modifier));
            $modifier_name = $modifier_parts[0];
            $modifier_opts = array_slice($modifier_parts, 1);
            $value = $this->parseWithModifier($value, $modifier_name, $modifier_opts);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param string $modifier_name
     * @param List<string> $modifier_opts
     * @return mixed
     */
    private function parseWithModifier(mixed $value, string $modifier_name, array $modifier_opts): mixed
    {
        if ($modifier = $this->findModifier($modifier_name)) {
            return $modifier::modify($value, $modifier_opts);
        }

        return $value;
    }

    /**
     * @param string $name
     * @return ExpressionModifier|null
     */
    private function findModifier(string $name): ?ExpressionModifier
    {
        foreach ($this->expressionModifiers as $modifier) {
            $reflected = new \ReflectionClass($modifier);
            // todo: harden this
            $modifier_name = $reflected->getAttributes(Name::class)[0]->newInstance()->name;

            if ($modifier_name === $name) {
                return new $modifier();
            }
        }

        return null;
    }

    /**
     * @param string $var
     * @return mixed
     */
    private function varVal(string $var): mixed
    {
        $parts = explode('.', $var);
        $value = $this->data;

        foreach ($parts as $part) {
            if (is_array($value) && array_key_exists($part, $value)) {
                $value = $value[$part];
            } else {
                return null;
            }
        }

        return $value;
    }
}