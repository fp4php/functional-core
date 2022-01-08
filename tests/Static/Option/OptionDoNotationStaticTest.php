<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Tests\Static\Option;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Core\Unit;

final class OptionDoNotationStaticTest
{
    /**
     * @return Option<Unit>
     */
    public function testUnitReturn(): Option
    {
        return Option::do(function () {
            yield Option::fromNullable(false);
            return Unit::getInstance();
        });
    }

    /**
     * @return Option<1|Unit>
     */
    public function testUnitReturnConditionally(): Option
    {
        return Option::do(function () {
            yield Option::fromNullable(false);

            if (rand(0, 1) === 1) {
                return 1;
            }

            return Unit::getInstance();
        });
    }

    /**
     * @return Option<positive-int>
     */
    public function testWithFilter(): Option
    {
        return Option::do(function() {
            $num = yield Option::some(10);

            if ($num < 10) {
                return yield Option::none();
            }

            return $num + 32;
        });
    }
}
