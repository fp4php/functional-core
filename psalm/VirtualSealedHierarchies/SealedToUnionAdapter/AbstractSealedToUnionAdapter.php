<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\SealedToUnionAdapter;

use Whsv26\Functional\Core\Option;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Return_;
use Psalm\NodeTypeProvider;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

abstract class AbstractSealedToUnionAdapter implements SealedToUnionAdapter
{
    /**
     * @psalm-return Option<TGenericObject>
     */
    public static function extractAtomic(Expr|Name|Return_ $node, NodeTypeProvider $provider, string $adtClass): Option
    {
        return Option::do(function() use ($node, $provider, $adtClass) {
            $generic_object = yield Option::fromNullable($provider->getType($node))
                ->map(fn(Union $type) => array_values($type->getAtomicTypes()))
                ->filter(fn(array $atomics) => 1 === count($atomics))
                ->map(fn(array $atomics) => $atomics[0])
                ->filter(fn(Atomic $atomic) => $atomic instanceof TGenericObject);

            return yield Option::when($adtClass === $generic_object->value, fn() => $generic_object);
        });
    }
}
