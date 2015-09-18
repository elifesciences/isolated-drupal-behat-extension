<?php

namespace eLife\IsolatedDrupalBehatExtension\ServiceContainer\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface as CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FilesystemCleanerCompilerPass implements CompilerPass
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasParameter('elife_drupal_behat.clean_up')) {
            return;
        }

        if (false === $container->getParameter('elife_drupal_behat.clean_up')) {
            $container->getDefinition('elife.isolated_drupal_behat.filesystem_cleaner.symfony')
                ->setDecoratedService(null);
            $container->getDefinition('elife.isolated_drupal_behat.filesystem_cleaner.no_op')
                ->setDecoratedService('elife.isolated_drupal_behat.filesystem_cleaner');
        }
    }
}
