<?php

declare(strict_types=1);

namespace Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\Analyzer;

use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\Assertion\TypeAssertion;
use Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\SealedToUnionAdapter\EitherToUnionAdapter;

final class EitherSealedHierarchyAnalyzer implements AfterMethodCallAnalysisInterface
{
    private const ASSERTION_METHODS = ['isLeft', 'isRight'];

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        TypeAssertion::changeTypeAfterAssertionCall($event, new EitherToUnionAdapter(), self::ASSERTION_METHODS);
    }
}
