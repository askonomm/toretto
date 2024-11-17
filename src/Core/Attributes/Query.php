<?php

namespace Asko\Toretto\Core\Attributes;

#[\Attribute]
class Query
{
    public function __construct(public string $query) {}
}