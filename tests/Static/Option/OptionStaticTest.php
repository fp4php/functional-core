<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Tests\Static\Option;

use Whsv26\Functional\Core\Option;

final class OptionStaticTest
{
    /**
     * @return Option<int>
     */
    public function testCreation(?int $in): Option
    {
        return Option::fromNullable($in);
    }

    /**
     * @param Option<int> $in
     * @return int|null
     */
    public function testGet(Option $in): ?int
    {
        return $in->get();
    }

    /**
     * @psalm-return '1'|null
     */
    public function testMap(): ?string
    {
        return Option::fromNullable(1)
            ->map(fn(int $v) => (string) $v)
            ->get();
    }

    /**
     * @psalm-return '1'|null
     */
    public function testFlatMap(): ?string
    {
        return Option::fromNullable(1)
            ->flatMap(fn(int $v) => Option::fromNullable((string) $v))
            ->get();
    }
}
