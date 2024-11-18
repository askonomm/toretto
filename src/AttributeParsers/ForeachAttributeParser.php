<?php

namespace Asko\Toretto\AttributeParsers;

use Asko\Toretto\AttributeParsers\ForeachAttributeParser\IterationExpression;
use Asko\Toretto\Core\Attributes\Query;
use Asko\Toretto\Toretto;
use Dom\AdjacentPosition;
use Dom\Node;
use DOM\NodeList;

#[Query('//*[@foreach]')]
class ForeachAttributeParser extends BaseAttributeParser
{
    /**
     * Parses a NodeList and processes each Node that contains a 'foreach' attribute.
     *
     * @param NodeList<Node> $nodeList The list of nodes to be parsed.
     * @return void
     */
    #[\Override]
    public function parse(NodeList &$nodeList): void
    {
        foreach($nodeList as $node) {
            $iter_expression = trim($node->getAttribute('foreach'));

            // There is no iter expression.
            if (empty($iter_expression)) {
                $this->setError($node, 'No iter expression in foreach attribute.');
                continue;
            }

            $parsed_iter_expression = $this->parseIterExpression($iter_expression);

            // Could not parse iter expression.
            if (!$parsed_iter_expression) {
                $this->setError($node, 'Invalid iter expression.');
                continue;
            }

            // Nothing to loop over.
            if (empty($parsed_iter_expression->collection)) {
                $node->parentNode->removeChild($node);
            }

            $node->removeAttribute('foreach');

            // If the node is the root node, we can't add adjacent elements, because
            // it results in "Cannot have more than one element child in a document" error,
            // thus we need to add a parent ourselves.
            if ($node->parentNode instanceof \DOM\Document) {
                $parent = $node->parentNode->createElement('div');
                $cloned_node = $node->cloneNode(true);
                $parent->appendChild($cloned_node);
                $node->parentNode->replaceChild($parent, $node);
                $this->loop($cloned_node, $parsed_iter_expression);
            } else {
                $this->loop($node, $parsed_iter_expression);
            }
        }
    }

    /**
     * Processes each item in the given collection, performing transformations and inserting resulting HTML elements.
     *
     * @param Node $node The DOM node which will be processed.
     * @param IterationExpression $iterationExpression
     * @return void
     */
    private function loop(Node $node, IterationExpression $iterationExpression): void
    {
        foreach ($iterationExpression->collection as $key => $value) {
            $data = $this->data;

            if ($iterationExpression->asKey !== null) {
                $data[$iterationExpression->asKey] = $key;
            }

            if ($iterationExpression->asVar !== null) {
                $data[$iterationExpression->asVar] = $value;
            }

            $toretto = new Toretto($node->ownerDocument->saveHtml($node), $data);
            $element = $toretto->toHtmlElement();

            if ($element) {
                $node->insertAdjacentElement(AdjacentPosition::BeforeBegin, $element);
            }
        }

        $node->parentNode->removeChild($node);
    }

    /**
     * Parses an iteration expression into its components.
     *
     * @param string $expression The iteration expression to parse.
     * @return array|null Returns an associative array.
     */
    private function parseIterExpression(string $expression): ?IterationExpression
    {
        // only allow alphanumeric, space and :
        if (!preg_match('/^[\w+:|\s]+$/', $expression)) {
            return null;
        }

        $parts = array_map(fn($p) => trim($p), explode(' as ', $expression));
        $parts = array_values(array_filter($parts, fn($p) => !empty($p)));

        // Only left-side of operator exists
        if (count($parts) === 1) {
            return new IterationExpression(
                collection: $this->parseExpression($parts[0])
            );
        }

        // The right-side of operator exists
        $asParts = explode(':', $parts[1]);

        if (count($asParts) === 1) {
            return new IterationExpression(
                collection:$this->parseExpression($parts[0]),
                asVar: $asParts[0],
            );
        }

        return new IterationExpression(
            collection: $this->parseExpression($parts[0]),
            asKey:  $asParts[0],
            asVar: $asParts[1],
        );
    }

    /**
     * Replaces the given node with a comment node containing an error message.
     *
     * @param Node $node The node to be replaced with an error comment. This parameter is passed by reference.
     * @param string $message The error message to be included in the comment node.
     * @return void
     */
    private function setError(Node &$node, string $message): void
    {
        $errorNode = $node->ownerDocument->createComment($message);
        $node->parentNode->replaceChild($errorNode, $node);
    }
}