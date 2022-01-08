<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\Assertion;

use Whsv26\Functional\Core\Option;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\SealedToUnionAdapter\SealedToUnionAdapter;

final class TypeAssertion
{
    /**
     * Turns known pseudo ADT to union.
     * For example Either<L, R> to Left<L> | Right<R>
     * or Option<T> to None | Some<T>.
     *
     * @psalm-param non-empty-array<string> $assertion_methods
     */
    public static function changeTypeAfterAssertionCall(
        AfterMethodCallAnalysisEvent $event,
        SealedToUnionAdapter $pseudo_adt_to_union,
        array $assertion_methods,
    ): void
    {
        Option::do(function() use ($event, $pseudo_adt_to_union, $assertion_methods) {
            $context = $event->getContext();
            $source = $event->getStatementsSource();
            $type_provider = $source->getNodeTypeProvider();

            $assertion_method = yield self::getAssertionMethodCall($event->getExpr(), $assertion_methods);
            $variable_name = yield self::getVariableName($assertion_method);

            $adt_union = yield $pseudo_adt_to_union->getUnion($type_provider, $assertion_method);

            $type_provider->setType($assertion_method->var, $adt_union);
            $context->vars_in_scope[$variable_name] = $adt_union;
        });
    }

    /**
     * @psalm-param non-empty-array<string> $assertion_methods
     * @psalm-return Option<MethodCall>
     */
    private static function getAssertionMethodCall(Expr $expr, array $assertion_methods): Option
    {
        return Option::do(function() use ($expr, $assertion_methods) {
            $method_call = yield Option::some($expr)
                ->filterOf(MethodCall::class);

            $method_identifier = yield Option::some($method_call->name)
                ->filterOf(Identifier::class);

            return yield Option::when(
                in_array($method_identifier->name, $assertion_methods, true),
                fn() => $method_call
            );
        });
    }

    /**
     * @psalm-return Option<string>
     */
    private static function getVariableName(MethodCall $method_call): Option
    {
        return Option::do(function() use ($method_call) {
            $variable = yield Option::some($method_call->var)
                ->filterOf(Variable::class);

            $name = yield Option::some($variable->name)
                ->filter(fn(string|Expr $name) => is_string($name));

            return '$' . $name;
        });
    }
}
