<?php

declare(strict_types=1);

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\Core\Attributes\Query;
use Dom\Node;
use Dom\NodeList;

#[Query('//*[@t-inner-html]')]
class InnerHtmlAttributeParser extends BaseAttributeParser
{
    /**
     * @param NodeList<Node> $nodeList
     * @return void
     */
    #[\Override]
    public function parse(NodeList &$nodeList): void
    {
        foreach($nodeList as $node) {
            $parsedExpression = $this->parseExpression($node->getAttribute('t-inner-html'), serialize: true);
            $node->innerHTML = $parsedExpression;
            $node->removeAttribute('t-inner-html');
        }
    }
}