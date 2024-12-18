<?php

declare(strict_types=1);

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\Core\Attributes\Query;
use Dom\Node;
use DOM\NodeList;

#[Query('//*[@unless]')]
class UnlessAttributeParser extends BaseAttributeParser
{
    /**
     * @param NodeList<Node> $nodeList
     * @return void
     */
    #[\Override]
    public function parse(NodeList &$nodeList): void
    {
        foreach($nodeList as $node) {
            $unless = $node->getAttribute('unless');
            $node->removeAttribute('unless');

            if (!empty($this->parseExpression($unless))) {
                $node->parentNode->removeChild($node);
            }
        }
    }
}