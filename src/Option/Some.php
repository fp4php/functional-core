<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Option;

use Whsv26\Functional\Core\Option;

/**
 * @template-covariant A
 * @psalm-immutable
 * @extends Option<A>
 */
final class Some extends Option
{
    /**
     * @psalm-var A
     */
    protected mixed $value;

    /**
     * @psalm-param A $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * @psalm-return A
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
