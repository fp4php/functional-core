<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\SealedToUnionAdapter;

use Whsv26\Functional\Core\Either;
use Whsv26\Functional\Core\Option;
use PhpParser\Node\Expr\MethodCall;
use Psalm\NodeTypeProvider;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use Whsv26\Functional\Core\Either\Left;
use Whsv26\Functional\Core\Either\Right;

final class EitherToUnionAdapter extends AbstractSealedToUnionAdapter
{
    public function getUnion(NodeTypeProvider $type_provider, MethodCall $from_assertion_method): Option
    {
        return self::extractAtomic($from_assertion_method->var, $type_provider, Either::class)
            ->flatMap(fn($generic_object) => self::getEitherTypeParams($generic_object))
            ->map(fn($type_params) => [
                new TGenericObject(Right::class, [$type_params['right']]),
                new TGenericObject(Left::class, [$type_params['left']]),
            ])
            ->map(fn($types) => new Union($types));
    }

    /**
     * @psalm-return Option<array{left: Union, right: Union}>
     */
    private static function getEitherTypeParams(TGenericObject $generic_object): Option
    {
        return Option::do(function() use ($generic_object) {
            $left = yield Option::fromNullable($generic_object->type_params[0] ?? null);
            $right = yield Option::fromNullable($generic_object->type_params[1] ?? null);

            return [
                'left' => $left,
                'right' => $right,
            ];
        });
    }
}
