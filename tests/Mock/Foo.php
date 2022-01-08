<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Tests\Mock;

/**
 * @internal
 */
class Foo
{
    public function __construct(
        public int $a,
        public bool $b = true,
        public bool $c = true
    ) { }
}
