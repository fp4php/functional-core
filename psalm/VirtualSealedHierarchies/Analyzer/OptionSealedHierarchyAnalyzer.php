<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\Analyzer;

use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\Assertion\TypeAssertion;
use Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\SealedToUnionAdapter\OptionToUnionAdapter;

final class OptionSealedHierarchyAnalyzer implements AfterMethodCallAnalysisInterface
{
    private const ASSERTION_METHODS = ['isSome', 'isNone', 'isEmpty', 'isNonEmpty'];

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        TypeAssertion::changeTypeAfterAssertionCall($event, new OptionToUnionAdapter(), self::ASSERTION_METHODS);
    }
}
