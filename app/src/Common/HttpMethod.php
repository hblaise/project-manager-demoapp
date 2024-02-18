<?php

namespace App\Common;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpMethod
{
    private array $methods;

    public function __construct(string ...$methods)
    {
        $this->methods = $methods;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }
}
