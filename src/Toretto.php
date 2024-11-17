<?php

declare(strict_types=1);

namespace Asko\Toretto;

use Asko\Toretto\AttributeParsers\BaseAttributeParser;
use Asko\Toretto\AttributeParsers\GenericValueAttributeParser;
use Asko\Toretto\AttributeParsers\IfAttributeParser;
use Asko\Toretto\AttributeParsers\InnerHtmlAttributeParser;
use Asko\Toretto\AttributeParsers\InnerTextAttributeParser;
use Asko\Toretto\AttributeParsers\UnlessAttributeParser;
use Asko\Toretto\Core\Attributes\Query;
use Asko\Toretto\ExpressionModifiers\TruncateExpressionModifier;
use Dom\HTMLDocument;
use DOM\NodeList;
use DOM\XPath;
use Psr\Log\LoggerInterface;
use const Dom\HTML_NO_DEFAULT_NS;

class Toretto
{
    /**
     * Default list of attribute parsers.
     *
     * @var List<class-string> $defaultAttributeParsers
     */
    private array $defaultAttributeParsers = [
        GenericValueAttributeParser::class,
        IfAttributeParser::class,
        UnlessAttributeParser::class,
        InnerTextAttributeParser::class,
        InnerHtmlAttributeParser::class,
    ];

    /**
     * A list of attribute parsers.
     *
     * @var List<BaseAttributeParser> $attributeParsers
     */
    public array $attributeParsers = [] {
        set {
            foreach($value as $parser) {
                if (!is_subclass_of($parser, BaseAttributeParser::class)) {
                    throw new \InvalidArgumentException('Attribute parser must be a subclass of BaseAttributeParser.');
                }
            }

            $this->attributeParsers = $value;
        }
    }

    /**
     * A list of expression modifiers.
     *
     * @var List<ExpressionModifier> $expressionModifiers
     */
    public array $expressionModifiers {
        get {
            return [
                new TruncateExpressionModifier(),
            ];
        }
    }

    /**
     * @var HTMLDocument $dom
     */
    private(set) HTMLDocument $dom;

    /** @var array<string, mixed> $data */
    private(set) array $data = [];

    /**
     * @var LoggerInterface|null $logger
     */
    public ?LoggerInterface $logger = null;

    /**
     * @param string $template
     * @param array<string, mixed> $data
     */
    public function __construct(string $template, array $data = [])
    {
        // Set attribute parsers
        $this->attributeParsers = array_map(fn($parser) => new $parser, $this->defaultAttributeParsers);

        // Set DOM
        $this->dom = HTMLDocument::createFromString(
            source: $template,
            options: HTML_NO_DEFAULT_NS | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED
        );

        // Set data
        $this->data = $data;
    }

    /**
     * Parses and processes attributes in the DOM using registered attribute parsers.
     *
     * The method iterates through the registered attribute parsers, initializes them,
     * sets up expression parsers with the required data and modifiers,
     * and then queries the DOM based on the specified query attributes.
     *
     * @return void
     */
    private function parseAttributes(): void
    {
        $dom = &$this->dom;
        $xpath = new XPath($dom);

        foreach($this->attributeParsers as $attributeParser) {
            $parser = new $attributeParser();
            $parser->expressionParser = new ExpressionParser();
            $parser->expressionParser->data = $this->data;
            $parser->expressionParser->expressionModifiers = $this->expressionModifiers;

            try {
                $attributes = new \ReflectionClass($parser)->getAttributes(Query::class);

                if (empty($attributes)) throw new \InvalidArgumentException('Query attribute is missing.');

                $query = $attributes[0]->newInstance()?->query;

                if (empty($query)) throw new \InvalidArgumentException('Query attribute is empty.');

                $nodes = $xpath->query($query);

                if ($nodes instanceof NodeList) {
                    $parser->parse($nodes);
                }
            } catch (\Throwable $e) {
                var_dump($e);
                $this->logger?->error($e->getMessage());
            }
        }
    }

    /**
     * Converts the DOM object to an HTML string representation.
     *
     * @return string|false The HTML string generated from the DOM object.
     */
    public function toHtml(): string|false
    {
        $this->parseAttributes();

        return $this->dom->saveHTML();
    }
}