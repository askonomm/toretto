<?php

namespace Asko\Toretto\AttributeParsers\ForeachAttributeParser;

class IterationExpression
{
    public function __construct(
        public ?array $collection = [],
        public ?string $asKey = null,
        public ?string $asVar = null,
    ) {}
}