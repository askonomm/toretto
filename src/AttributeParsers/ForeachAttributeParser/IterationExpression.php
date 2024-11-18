<?php

namespace Asko\Toretto\AttributeParsers\ForeachAttributeParser;

readonly class IterationExpression
{
    public function __construct(
        public ?iterable $collection = [],
        public ?string $asKey = null,
        public ?string $asVar = null,
    ) {}
}