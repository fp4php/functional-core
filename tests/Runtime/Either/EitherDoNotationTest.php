<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Tests\Runtime\Either;

use Whsv26\Functional\Core\Either;
use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Core\Either\Left;
use Whsv26\Functional\Core\Either\Right;

final class EitherDoNotationTest extends TestCase
{
    public function testWithoutYieldStatements(): void
    {
        $items = [];

        $mappedOption = Either::do(function() use ($items) {
            $mapped = [];

            /** @psalm-suppress MixedAssignment */
            foreach ($items as $item) {
                $mapped[] = yield Either::right($item);
            }

            return $mapped;
        });

        $this->assertEquals([], $mappedOption->get());
    }

    public function testWithAtLeastOneYieldStatement(): void
    {
        $mappedOption = Either::do(function() {
            $a = 1;
            $b = yield Either::right(2);
            $c = yield new Right(3);
            $d = yield Either::right(4);
            $e = 5;

            return [$a, $b, $c, $d, $e];
        });

        $this->assertEquals([1, 2, 3, 4, 5], $mappedOption->get());
    }

    public function testShortCircuit(): void
    {
        $mappedOption = Either::do(function() {
            $a = 1;
            $b = yield Either::right(2);
            $c = yield new Right(3);
            $d = yield Either::left('error!');
            $e = 5;

            return [$a, $b, $c, $d, $e];
        });

        $this->assertEquals('error!', $mappedOption->get());
        $this->assertInstanceOf(Left::class, $mappedOption);
    }
}
