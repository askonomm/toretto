<?php

declare(strict_types=1);

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\ExpressionParser;
use DOM\NodeList;

abstract class BaseAttributeParser
{
    public ExpressionParser $expressionParser;

    /**
     * @param NodeList $nodeList
     * @return void
     */
    abstract public function parse(NodeList &$nodeList): void;

    /**
     * @param string $expression
     * @return mixed
     */
    protected function parseExpression(string $expression, bool $serialize = false): mixed
    {
        return $this->expressionParser->parse($expression, $serialize);
    }
}