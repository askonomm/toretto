<?php

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\Core\Attributes\Query;
use Asko\Toretto\Toretto;
use Dom\AdjacentPosition;
use Dom\Node;
use DOM\NodeList;

#[Query('//*[@foreach]')]
class ForeachAttributeParser extends BaseAttributeParser
{
    /**
     * @param NodeList<Node> $nodeList
     * @return void
     */
    #[\Override]
    public function parse(NodeList &$nodeList): void
    {
        foreach($nodeList as $node) {
            $iter_expression = trim($node->getAttribute('foreach'));

            if (empty($iter_expression)) {
                continue;
            }

            $parsed_iter_expression = $this->parseIterExpression($iter_expression);

            if (empty($parsed_iter_expression)) {
                continue;
            }

            $collection = $this->parseExpression($parsed_iter_expression['collection']);

            if (empty($collection)) {
                continue;
            }

            $node->removeAttribute('foreach');
            $asKey = $parsed_iter_expression['asKey'];
            $asVar = $parsed_iter_expression['asVar'];

            // If the node is the root node, we can't add adjacent elements, because
            // it results in "Cannot have more than one element child in a document" error,
            // thus we need to add a parent ourselves.
            if ($node->parentNode instanceof \DOM\Document) {
                $parent = $node->parentNode->createElement('div');
                $cloned_node = $node->cloneNode(true);
                $parent->appendChild($cloned_node);
                $node->parentNode->replaceChild($parent, $node);
                $this->loop($cloned_node, $collection, $asKey, $asVar);
            } else {
                $this->loop($node, $collection, $asKey, $asVar);
            }
        }
    }

    private function loop(Node $node, array $collection, ?string $asKey = null, ?string $asVar = null): void
    {
        foreach ($collection as $key => $value) {
            $data = $this->data;

            if ($asKey !== null) {
                $data[$asKey] = $key;
            }

            if ($asVar !== null) {
                $data[$asVar] = $value;
            }

            $toretto = new Toretto($node->ownerDocument->saveHtml($node), $data);
            $element = $toretto->toHtmlElement();

            if ($element) {
                $node->insertAdjacentElement(AdjacentPosition::BeforeBegin, $element);
            }
        }

        $node->parentNode->removeChild($node);
    }

    private function parseIterExpression(string $expression): array
    {
        $parts = array_map(fn($p) => trim($p), explode(' ', $expression));
        $parts = array_values(array_filter($parts, fn($p) => !empty($p)));

        // Only collection exists
        if (count($parts) === 1) {
            return [
                'collection' => $parts[0],
                'operator' => null,
                'asKey' => null,
                'asVar' => null,
            ];
        }

        // as {something} requires three parts where the second is the operator
        if (count($parts) >= 3) {
            $rest = implode('', array_slice($parts, 2));
            $as_parts = explode(':', $rest);

            if (count($as_parts) === 1) {
                return [
                    'collection' => $parts[0],
                    'operator' => $parts[1],
                    'asKey' => null,
                    'asVar' => $as_parts[0],
                ];
            }

            return [
                'collection' => $parts[0],
                'operator' => $parts[1],
                'asKey' => $as_parts[0],
                'asVar' => $as_parts[1],
            ];
        }

        return [];
    }
}