<?php

declare(strict_types=1);

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\Core\Attributes\Query;
use Dom\Node;
use DOM\NodeList;

#[Query('//*[@*[starts-with(name(), ":")]]')]
class GenericValueAttributeParser extends BaseAttributeParser
{
    /**
     * @param NodeList<Node> $nodeList
     * @return void
     */
    #[\Override]
    public function parse(NodeList &$nodeList): void
    {
        foreach($nodeList as $node) {
            foreach($node->attributes as $attribute) {
                if (str_starts_with($attribute->name, ':')) {
                    $finalAttributeName = substr($attribute->name, 1);
                    $parsedExpression = $this->parseExpression($attribute->value, serialize: true);
                    $node->setAttribute($finalAttributeName, $parsedExpression);
                    $node->removeAttribute($attribute->name);
                }
            }
        }
    }
}