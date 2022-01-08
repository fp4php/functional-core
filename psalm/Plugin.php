<?php

declare(strict_types=1);


namespace Whsv26\Functional\Core\Psalm;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;
use Whsv26\Functional\Core\Psalm\OptionFilterMethodRefinement\OptionFilterMethodReturnTypeProvider;
use Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\Analyzer\EitherSealedHierarchyAnalyzer;
use Whsv26\Functional\Core\Psalm\VirtualSealedHierarchies\Analyzer\OptionSealedHierarchyAnalyzer;

/**
 * Plugin entrypoint
 */
class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $register =
            /**
             * @param class-string $hook
             */
            function (string $hook) use ($registration): void {
                class_exists($hook);
                $registration->registerHooksFromClass($hook);
            };

        $register(OptionFilterMethodReturnTypeProvider::class);
        $register(OptionSealedHierarchyAnalyzer::class);
        $register(EitherSealedHierarchyAnalyzer::class);
    }
}
