# Toretto

[![codecov](https://codecov.io/gh/askonomm/toretto/graph/badge.svg?token=nYfiv0Lmmk)](https://codecov.io/gh/askonomm/toretto)

A simple and extendable templating library built on top of the new `\DOM\HTMLDocument` of PHP 8.4. 
Still very much a work in progress, and specification may change. 

## Features

- **Simple syntax**: Toretto is a superset of HTML, so you can write your templates in any text editor with full support.
- **Interpolation**: You can interpolate values from a data array into your templates.
- **Modifiers**: You can modify the interpolated values using modifiers.
- **Conditionals**: You can show or hide blocks using simple or complex expressions.
- **Partials**: You can include other templates inside your templates.
- **Loops**: You can loop over arrays in your data array.
- **Extendable**: You can implement custom attribute parsers and expression modifiers.

## Example syntax

```html
<!DOCTYPE html>
<html>
<head>
    <title inner-text="{title}"></title>
</head>
<body>
    <h1 inner-text="{title}"></h1>
    
    <div class="posts" if="posts">
        <div foreach="posts as post">
            <h2 class="post-title">
                <a :href="/blog/{post.url}" inner-text="{post.title | capitalize}"></a>
            </h2>
            <div class="post-date" inner-text="{post.date | date:yyyy-MM-dd}"></div>
            <div class="post-content" inner-html="{post.body}"></div>
        </div>
    </div>
</body>
</html>
```

## Installation

Not yet installable.

## Usage

A simple example of how to use Toretto with default configuration looks like this:

```php
use Asko\Toretto;

$toretto = new Toretto('<p inner-text="Hello {who}"></p>', ['who' => 'World']);
$html = $toretto->toHtml(); // <p>Hello World</p>
```

## Attributes

Toretto works by parsing attributes in the template. 

### `inner-text`

Sets the inner text of the element to the value of the attribute.

Toretto template where `title` key is `Hello, World!`:

```html
<h1 inner-text="{title}"></h1>
```

Results in:

```html
<h1>Hello, World!</h1>
```

### `inner-html`

Sets the inner HTML of the element to the value of the attribute.

Toretto template where `content` key is `<p>Hello, World!</p>`:

```html
<div inner-html="{content}"></div>
```

Results in:

```html
<div>
    <p>Hello, World!</p>
</div>
```

### `if`

Removes the element if the attribute is false-y.

Toretto template where `show` key is `false`:

```html
<div if="show">Hello, World!</div>
```

Results in:

```html
<!-- Empty -->
```

### `unless`

Removes the element if the attribute is truthy.

Htmt template where `hide` key is `true`:

```html
<div unless="hide">Hello, World!</div>
```

Results in:

```html
<!-- Empty -->
```

### `foreach`

Loops anything iterable. 

For example, to loop over a collection of `posts` and then use `post` as the variable of each iteration, you can do something 
like this:

```php
<div foreach="posts as post">
    <h2 inner-text="post.title"></h2>
</div>
```

If you do not care about using any of the iteration data, you can also entirely omit `as ...` from the iterative expression, 
like so:

```php
<div foreach="posts">
    ...
</div>
```

And, you can also assign the key of the iteration to a variable, like so:

```php
<div foreach="posts as index:post">
    <h2 :class="post-{post.index}" inner-text="post.title"></h2>
</div>
```

### `:*` (Generic Value Attributes)

You can use the `:*` attribute to set any attribute on an element to the interpolated value of the generic value attribute.

For example, to set the `href` attribute of an element, you can use the `:href` attribute:

```html
<a :href="/blog/{slug}">Hello, World!</a>
```

Results in:

```html
<a href="/blog/hello-world">Hello, World!</a>
```

If the `slug` key is `hello-world`.

## Modifiers

All interpolated values in expressions can be modified using modifiers. Modifiers are applied to the value of the attribute, and they can be chained, like so:

```html
<h1 inner-text="{title | uppercase | reverse}"></h1>
```

Note that if you have nothing other than the interpolated variable in the attribute, then you can omit the curly brackets, and so 
this would also work:

```html
<h1 inner-text="title | uppercase | reverse"></h1>
```

### `truncate`

Truncates the value to the specified length.

```html
<p inner-text="{title | truncate:10}"></p>
```

This also works on collections, so you can use `truncate` to limit items in an array as well.

## Extending

### Attribute Parsers

You can add (or replace) attribute parsers in Toretto by adding them to the `$attributeParsers` array,
when creating a new instance of the `Toretto` class, like so:

```php
use Asko\Toretto;
use Asko\Toretto\AttributeParsers;

$toretto = new Toretto('<p inner-text="Hello {who}"></p>', ['who' => 'World']);
$toretto->attributeParsers = [
    new InnerTextAttributeParser(),
    ...
];

$html = $toretto->toHtml(); // <p>Hello World</p>
```

A custom attribute parser must extend the `BaseAttributeParser` class, like so:

```php
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
```

All attributes are matched via XTag queries, and you can match your attribute parser class with a XTag query by using the 
`Query` attribute.

#### List of built-in attribute parsers

- `Asko\Toretto\AttributeParsers\GenericValueAttributeParser` - Parser the `:*` attributes.
- `Asko\Toretto\AttributeParsers\IfAttributeParser` - Parser the `if` attributes.
- `Asko\Toretto\AttributeParsers\UnlessAttributeParser` - Parser the `unless` attributes.
- `Asko\Toretto\AttributeParsers\InnerHtmlAttributeParser` - Parser the `inner-html` attributes.
- `Asko\Toretto\AttributeParsers\InnerTextAttributeParser` - Parser the `inner-text` attributes.
- `Asko\Toretto\AttributeParsers\ForeachAttributeParser` - Parses the `foreach` attributes.

### Expression Modifiers

You can add (or replace) expression modifiers in Toretto by adding them to the `$expressionModiifers` array,
when creating a new instance of the `Toretto` class, like so:

```php
use Asko\Toretto;
use Asko\Toretto\ExpressionModifiers;

$toretto = new Toretto('<p inner-text="Hello {who}"></p>', ['who' => 'World']);
$toretto->expressionModifiers = [
    new TruncateExpressionModifier(),
    ...
];

$html = $toretto->toHtml(); // <p>Hello World</p>
```

A custom expression attribute must implement the `ExpressionModifier` interface, like so:

```php
<?php

use Asko\Toretto\Core\Attributes\Name;
use Asko\Toretto\ExpressionModifier;

#[Name('my-custom-modifier')]
class MyCustomExpressionModifier implements ExpressionModifier
{
    /**
     * @param mixed $value The value to be modified.
     * @param List<string>|null $opts Optional parameter.
     * @return mixed The modified expression.
     */
    public static function modify(mixed $value, ?array $opts = null): mixed
    {
        // Do something here.
    }
}
```

All expression modifiers need to have a name, and you can name your modifier with the `Name` attribute.

#### List of built-in expression modifiers

- `Asko\Toretto\ExpressionModifiers\TruncateExpressionModiifer` - Truncates the value (both strings and collections).
