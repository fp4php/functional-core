<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Tests\Runtime\Either;

use Whsv26\Functional\Core\Either;
use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Whsv26\Functional\Core\Either\Left;
use Whsv26\Functional\Core\Either\Right;

final class EitherTest extends TestCase
{
    public function testCreation(): void
    {
        $this->assertInstanceOf(Right::class, Either::right(1));
        $this->assertEquals(1, Either::right(1)->get());
        $this->assertInstanceOf(Left::class, Either::left('err'));
        $this->assertEquals('err', Either::left('err')->get());
    }

    public function testMap(): void
    {
        $right = Right::of(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $left = Left::of(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $this->assertEquals(3, $right->get());
        $this->assertEquals(1, $left->get());
    }

    public function testFlatMap(): void
    {
        $getRight = function(): Either {
            /** @psalm-var Either<string, int> */
            return Either::right(1);
        };

        $getLeft = function(): Either {
            /** @psalm-var Either<string, int> */
            return Either::left('error');
        };

        $right = $getRight()
            ->flatMap(fn(int $r) => Right::of($r + 1))
            ->flatMap(fn(int $r) => Right::of($r + 1));

        $left = $getLeft()
            ->flatMap(fn(int $r) => Right::of($r + 1))
            ->flatMap(function(int $r) {
                /** @psalm-var Either<string, int> */
                return Either::left('error');
            })
            ->flatMap(fn(int $r) => Either::right($r + 1));

        $this->assertEquals(3, $right->get());
        $this->assertEquals('error', $left->get());
    }

    public function testMapLeft(): void
    {
        /** @psalm-var Either<string, int> $either1 */
        $either1 = Either::right(1);

        /** @psalm-var Either<string, int> $either2 */
        $either2 = Either::left('error');

        $right = $either1
            ->map(fn(int $r) => $r + 1)
            ->mapLeft(fn(string $l) => match($l) {
                'error' => true,
                default => false,
            })
            ->mapLeft(fn(bool $l) => (int) $l)
            ->map(fn(int $r) => $r + 1);

        $left = $either2
            ->map(fn(int $r) => $r + 1)
            ->mapLeft(fn(string $l) => match($l) {
                'error' => true,
                default => false,
            })
            ->mapLeft(fn(bool $l) => (int) $l)
            ->mapLeft(fn(int $l) => $l + 9)
            ->map(fn(int $r) => $r + 1);

        $this->assertEquals(3, $right->get());
        $this->assertEquals(10, $left->get());
    }

    public function testIsMethods(): void
    {
        $this->assertFalse(Either::right(1)->isLeft());
        $this->assertTrue(Either::right(1)->isRight());
    }

    public function testTry(): void
    {
        $this->assertInstanceOf(Right::class, Either::try(fn() => 1));
        $this->assertEquals(1, Either::try(fn() => 1)->get());

        $this->assertInstanceOf(Left::class, Either::try(fn() => throw new Exception()));
        $this->assertInstanceOf(Exception::class, Either::try(fn() => throw new Exception())->get());
    }

    public function testFold(): void
    {
        $foldRight = Either::right(1)->fold(
            fn(int $some) => $some + 1,
            fn() => 0,
        );

        $foldLeft = Either::left('err')->fold(
            fn(int $some) => $some + 1,
            fn() => 0,
        );

        $this->assertEquals(2, $foldRight);
        $this->assertEquals(0, $foldLeft);
    }

    public function testGetOrElse(): void
    {
        $this->assertEquals(1, Either::right(1)->getOrElse(0));
        $this->assertEquals(0, Either::left('err')->getOrElse(0));
    }

    public function testGetOrCall(): void
    {
        $this->assertEquals(1, Either::right(1)->getOrCall(fn() => 0));
        $this->assertEquals(0, Either::left('err')->getOrCall(fn() => 0));
    }

    public function testGetOrThrow(): void
    {
        $this->assertEquals(1, Either::right(1)->getOrThrow(fn($err) => new RuntimeException($err)));
        $this->expectExceptionMessage('err');
        Either::left('err')->getOrThrow(fn($err) => new RuntimeException($err));
    }

    public function testOrElse(): void
    {
        $this->assertEquals(
            1,
            Either::right(1)->orElse(fn() => Either::right(2))->get()
        );

        $this->assertEquals(
            2,
            Either::left('err')->orElse(fn() => Either::right(2))->get()
        );
    }

    public function testCond(): void
    {
        $this->assertEquals(
            1,
            Either::cond(true, fn() => 1, fn() => 'err')->get()
        );

        $this->assertEquals(
            'err',
            Either::cond(false, fn() => 1, fn() => 'err')->get()
        );
    }

    public function testToOption(): void
    {
        $this->assertEquals(1, Either::right(1)->toOption()->get());
        $this->assertNull(Either::left(1)->toOption()->get());
    }
}
