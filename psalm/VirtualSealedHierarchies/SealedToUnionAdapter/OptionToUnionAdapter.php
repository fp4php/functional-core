<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\SealedToUnionAdapter;

use Whsv26\Functional\Core\Option;
use PhpParser\Node\Expr\MethodCall;
use Psalm\NodeTypeProvider;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;
use Whsv26\Functional\Core\Option\None;
use Whsv26\Functional\Core\Option\Some;

final class OptionToUnionAdapter extends AbstractSealedToUnionAdapter
{
    public function getUnion(NodeTypeProvider $type_provider, MethodCall $from_assertion_method): Option
    {
        return self::extractAtomic($from_assertion_method->var, $type_provider, Option::class)
            ->filter(fn($generic_object) => isset($generic_object->type_params[0]))
            ->map(fn($generic_object) => $generic_object->type_params[0])
            ->map(fn($type_param) => [
                new TGenericObject(Some::class, [$type_param]),
                new TNamedObject(None::class),
            ])
            ->map(fn($types) => new Union($types));
    }
}
