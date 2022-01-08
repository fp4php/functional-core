<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\SealedToUnionAdapter;

use Whsv26\Functional\Core\Option;
use PhpParser\Node\Expr\MethodCall;
use Psalm\NodeTypeProvider;
use Psalm\Type\Union;

interface SealedToUnionAdapter
{
    /**
     * @psalm-return Option<Union>
     */
    public function getUnion(NodeTypeProvider $type_provider, MethodCall $from_assertion_method): Option;
}
