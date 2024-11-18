<?php

namespace Asko\Toretto\Tests\AttributeParsers;

use Asko\Toretto\Toretto;
use PHPUnit\Framework\TestCase;

class ForeachAttributeParserTest extends TestCase
{
    public function testParseRootNode(): void
    {
        $toretto = new Toretto(
            template: '<div foreach="test">:)</div>',
            data: ['test' => ['test', 'test2']]
        );

        $result = $toretto->toHtml();
        $this->assertEquals("<div><div>:)</div><div>:)</div></div>", $result);
    }

    public function testParseChildNode(): void
    {
        $toretto = new Toretto(
            template: '<div><div foreach="test">:)</div></div>',
            data: ['test' => ['test', 'test2']]
        );

        $result = $toretto->toHtml();
        $this->assertEquals("<div><div>:)</div><div>:)</div></div>", $result);
    }

    public function testParseAsVar(): void
    {
        $toretto = new Toretto(
            template: '<div foreach="test as item" inner-text="item"></div>',
            data: ['test' => ['test', 'test2']]
        );

        $result = $toretto->toHtml();
        $this->assertEquals("<div><div>test</div><div>test2</div></div>", $result);
    }

    public function testParseAsKeyVar(): void
    {
        $toretto = new Toretto(
            template: '<div foreach="test as key:item" inner-text="{key} and {item}"></div>',
            data: ['test' => ['test', 'test2']]
        );

        $result = $toretto->toHtml();
        $this->assertEquals("<div><div>0 and test</div><div>1 and test2</div></div>", $result);
    }

    public function testParseEmptyCollection(): void
    {
        $toretto = new Toretto(
            template: '<div foreach="test as item" inner-text="item"></div>',
            data: ['test' => []]
        );

        $result = $toretto->toHtml();
        $this->assertEquals("", $result);
    }

    public function testParseEmptyIterExpression(): void
    {
        $toretto = new Toretto(
            template: '<div foreach="" inner-text="item"></div>',
            data: ['test' => []]
        );

        $result = $toretto->toHtml();
        $this->assertEquals("<!--No iter expression in foreach attribute.-->", $result);
    }

    public function testParseInvalidIterExpression(): void
    {
        $toretto = new Toretto(
            template: '<div foreach="@asdasd////" inner-text="item"></div>',
            data: ['test' => []]
        );

        $result = $toretto->toHtml();
        $this->assertEquals("<!--Invalid iter expression.-->", $result);
    }
}