<?php

declare(strict_types=1);

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\Core\Attributes\Query;
use Dom\Node;
use DOM\NodeList;

#[Query('//*[@if]')]
class IfAttributeParser extends BaseAttributeParser
{
    /**
     * @param NodeList<Node> $nodeList
     * @return void
     */
    #[\Override]
    public function parse(NodeList &$nodeList): void
    {
        foreach($nodeList as $node) {
            $if = $node->getAttribute('if');
            $node->removeAttribute('if');

            if (empty($this->parseExpression($if))) {
                $node->parentNode->removeChild($node);
            }
        }
    }
}