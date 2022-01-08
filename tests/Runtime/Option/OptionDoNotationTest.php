<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Tests\Runtime\Option;

use Whsv26\Functional\Core\Option;
use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Core\Unit;
use Whsv26\Functional\Core\Option\None;
use Whsv26\Functional\Core\Option\Some;

final class OptionDoNotationTest extends TestCase
{
    public function testWithUnitAsReturnType(): void
    {
        $option = Option::do(function () {
            yield Option::fromNullable(false);
            return Unit::getInstance();
        });

        $this->assertEquals(Unit::getInstance(), $option->get());
    }

    public function testWithoutYieldStatements(): void
    {
        $items = [];

        $mappedOption = Option::do(function() use ($items) {
            $mapped = [];

            /** @psalm-suppress MixedAssignment */
            foreach ($items as $item) {
                $mapped[] = yield Option::fromNullable($item);
            }

            return $mapped;
        });

        $this->assertEquals([], $mappedOption->get());
    }

    public function testWithAtLeastOneYieldStatement(): void
    {
        $mappedOption = Option::do(function() {
            $a = 1;
            $b = yield Option::fromNullable(2);
            $c = yield Option::some(3);
            $d = yield Option::some(4);
            $e = 5;

            return [$a, $b, $c, $d, $e];
        });

        $this->assertEquals([1, 2, 3, 4, 5], $mappedOption->get());
        $this->assertInstanceOf(Some::class, $mappedOption);
    }

    public function testShortCircuit(): void
    {
        $mappedOption = Option::do(function() {
            $a = 1;
            $b = yield Option::fromNullable(2);
            $c = yield Option::some(3);
            $d = yield Option::none();
            $e = 5;

            return [$a, $b, $c, $d, $e];
        });

        $this->assertNull($mappedOption->get());
        $this->assertInstanceOf(None::class, $mappedOption);
    }
}
