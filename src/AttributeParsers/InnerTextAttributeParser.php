<?php

declare(strict_types=1);

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\Core\Attributes\Query;
use Dom\Node;
use Dom\NodeList;

#[Query('//*[@inner-text]')]
class InnerTextAttributeParser extends BaseAttributeParser
{
    /**
     * @param NodeList<Node> $nodeList
     * @return void
     */
    #[\Override]
    public function parse(NodeList &$nodeList): void
    {
        foreach($nodeList as $node) {
            $parsedExpression = $this->parseExpression($node->getAttribute('inner-text'), serialize: true);
            $node->textContent = $parsedExpression;
            $node->removeAttribute('inner-text');
        }
    }
}