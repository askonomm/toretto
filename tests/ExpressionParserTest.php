<?php

namespace Asko\Toretto\Tests;

use Asko\Toretto\ExpressionParser;
use PHPUnit\Framework\TestCase;

class ExpressionParserTest extends TestCase
{
    public function testParseWithoutInterpolation(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = [];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("test");

        $this->assertEquals(null, $result);
    }

    public function testParseWithInterpolation(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => 'world'];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}");

        $this->assertEquals("hello world", $result);
    }

    public function testParseWithInterpolationNoData(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = [];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}");

        $this->assertEquals("hello null", $result);
    }

    public function testParseWithMultipleInterpolations(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => 'world', 'name' => 'John'];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}, my name is {name}");

        $this->assertEquals("hello world, my name is John", $result);
    }

    public function testStringSerialization(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => 'world'];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}", serialize: true);

        $this->assertEquals("hello world", $result);
    }

    public function testNumericSerialization(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => 1];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}", serialize: true);

        $this->assertEquals("hello 1", $result);
    }

    public function testBooleanSerialization(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => true];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}", serialize: true);

        $this->assertEquals("hello true", $result);
    }

    public function testNullSerialization(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => null];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}", serialize: true);

        $this->assertEquals("hello null", $result);
    }

    public function testArraySerialization(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => ['test']];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}", serialize: true);

        $this->assertEquals("hello [\"test\"]", $result);
    }

    public function testObjectSerialization(): void
    {
        $expressionParser = new ExpressionParser();
        $obj = new \stdClass();
        $obj->name = 'world';
        $expressionParser->data = ['who' => $obj];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}", serialize: true);

        $this->assertEquals("hello {\"name\":\"world\"}", $result);
    }

    public function testClassSerialization(): void
    {
        $expressionParser = new ExpressionParser();
        $obj = new class {
            public string $name = 'world';
        };
        $expressionParser->data = ['who' => $obj];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who}", serialize: true);

        $this->assertEquals("hello {\"name\":\"world\"}", $result);
    }

    public function testModifierNotFound(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => 'world'];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who | test}");

        $this->assertEquals("hello world", $result);
    }

    public function testVarNotFound(): void
    {
        $expressionParser = new ExpressionParser();
        $expressionParser->data = ['who' => []];
        $expressionParser->expressionModifiers = [];
        $result = $expressionParser->parse("hello {who.what}");

        $this->assertEquals("hello null", $result);
    }
}