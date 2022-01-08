<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\OptionFilterMethodRefinement;

use Whsv26\Functional\Core\Option;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use Whsv26\Functional\Core\Option\Some;

final class OptionFilterMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Option::class, Some::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        $return_type = Option::do(function() use ($event) {
            yield Option::some($event->getMethodNameLowercase())
                ->filter(fn($method) => 'filter' === $method);

            $predicate = yield Option::some($event->getCallArgs())
                ->filter(fn($args) => count($args) === 1)
                ->map(fn($args) => $args[0])
                ->map(fn(Arg $arg) => $arg->value)
                ->filter(fn(Expr $expr) => $expr instanceof FunctionLike);

            $source = yield Option::some($event->getSource())
                ->filterOf(StatementsAnalyzer::class);

            $option_type_param = yield Option::fromNullable($event->getTemplateTypeParameters())
                ->filter(fn($type_params) => isset($type_params[0]))
                ->map(fn($type_params) => $type_params[0]);

            $refinement_context = new RefinementContext(
                refine_for: $event->getFqClasslikeName(),
                predicate: $predicate,
                execution_context: $event->getContext(),
                codebase: $source->getCodebase(),
                source: $source,
            );

            $result = RefineByPredicate::refine($refinement_context, $option_type_param);

            return new Union([
                new TGenericObject(Option::class, [$result]),
            ]);
        });

        return $return_type->get();
    }
}
