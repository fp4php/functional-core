<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Tests\Static\Classes\Either;

use Whsv26\Functional\Core\Either;

final class EitherStaticTest
{
    /**
     * @return Either<empty, 1>
     */
    public function testRightFabricMethod(): Either
    {
        return Either::right(1);
    }

    /**
     * @return Either<1, empty>
     */
    public function testLeftFabricMethod(): Either
    {
        return Either::left(1);
    }

    /**
     * @psalm-return int|string
     */
    public function testGet(): int|string
    {
        $getEither = fn (): Either => rand(0, 1)
            ? Either::right(1)
            : Either::left("error!");

        return $getEither()->get();
    }

    /**
     * @psalm-return 1|string
     */
    public function testMap(): int|string
    {
        return Either::left(1)
            ->map(fn(mixed $v) => (string) $v)
            ->get();
    }

    /**
     * @psalm-return '1'
     */
    public function testFlatMap(): string
    {
        return Either::right(1)
            ->flatMap(fn(int $v) => Either::right((string) $v))
            ->get();
    }

    /**
     * @return Either<bool,string>
     */
    public function testSwap(): Either
    {
        $getEither = fn (): Either => rand(0, 1)
            ? Either::right(1)
            : Either::left("error!");

        return $getEither()
            ->flatMap(fn(int $v) => Either::right((bool) $v))
            ->swap();
    }

    /**
     * @return Either<0|1,float>
     */
    public function testMapLeft(): Either
    {
        $getEither = fn (): Either => rand(0, 1)
            ? Either::right(1)
            : Either::left("error!");

        return $getEither()
            ->flatMap(fn(int $v) => Either::right((float) $v))
            ->mapLeft(fn(string $e) => (bool) $e)
            ->mapLeft(fn(bool $e) => (int) $e);
    }
}
