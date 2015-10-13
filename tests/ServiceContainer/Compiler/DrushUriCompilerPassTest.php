<?php

namespace eLife\IsolatedDrupalBehatExtension\ServiceContainer;

use Drupal\Driver\DrushDriver;
use eLife\IsolatedDrupalBehatExtension\ServiceContainer\Compiler\DrushUriCompilerPass;
use eLife\IsolatedDrupalBehatExtension\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class DrushUriCompilerPassTest extends TestCase
{
    /**
     * @test
     */
    public function itCleansTheFilesystem()
    {
        $compilerPass = new DrushUriCompilerPass();

        $container = new ContainerBuilder();
        $container->setParameter('mink.base_url', 'http://localhost/');
        $container->setParameter('drupal.driver.drush.alias', 'foo');
        $container->setParameter('drupal.driver.drush.root', __DIR__ . '/../');
        $container->setParameter('drupal.driver.drupal.drupal_root', __DIR__);
        $container->setDefinition(
            'drupal.driver.drush',
            new Definition('Drupal\Driver\DrushDriver', [
                '%drupal.driver.drush.alias%',
                '%drupal.driver.drush.root%',
            ])
        );

        $compilerPass->process($container);

        $container->compile();

        /** @var DrushDriver $drush */
        $drush = $container->get('drupal.driver.drush');

        $this->assertNull($drush->alias);
        $this->assertSame(__DIR__, $drush->root);
        $this->assertSame('--uri=http://localhost/', $drush->getArguments());
    }
}
