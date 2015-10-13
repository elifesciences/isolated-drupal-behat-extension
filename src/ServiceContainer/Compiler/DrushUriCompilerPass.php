<?php

namespace eLife\IsolatedDrupalBehatExtension\ServiceContainer\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface as CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DrushUriCompilerPass implements CompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('drupal.driver.drush.alias', null);
        $container->setParameter(
            'drupal.driver.drush.root',
            '%drupal.driver.drupal.drupal_root%'
        );

        $container->getDefinition('drupal.driver.drush')
            ->addMethodCall('setArguments', ['--uri=%mink.base_url%']);
    }
}
