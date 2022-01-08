<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Either;

use Whsv26\Functional\Core\Either;

/**
 * @template-covariant L
 * @psalm-immutable
 * @extends Either<L, empty>
 */
final class Left extends Either
{
    /**
     * @psalm-param L $value
     */
    public function __construct(protected mixed $value)
    {
    }

    /**
     * @template LI
     * @psalm-param LI $value
     * @psalm-return self<LI>
     * @psalm-pure
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }

    /**
     * @psalm-return L
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
