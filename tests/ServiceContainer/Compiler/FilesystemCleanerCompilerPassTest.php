<?php

namespace eLife\IsolatedDrupalBehatExtension\ServiceContainer;

use eLife\IsolatedDrupalBehatExtension\ServiceContainer\Compiler\FilesystemCleanerCompilerPass;
use eLife\IsolatedDrupalBehatExtension\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class FilesystemCleanerCompilerPassTest extends TestCase
{
    /**
     * @test
     */
    public function itCleansTheFilesystem()
    {
        $compilerPass = new FilesystemCleanerCompilerPass();

        $container = $this->getContainer(true);

        $compilerPass->process($container);

        $container->compile();

        $this->assertInstanceOf(
            'eLife\IsolatedDrupalBehatExtension\Filesystem\SymfonyFilesystemCleaner',
            $container->get('elife.isolated_drupal_behat.filesystem_cleaner')
        );
    }

    /**
     * @test
     */
    public function itDoesNotCleanTheFilesystem()
    {
        $compilerPass = new FilesystemCleanerCompilerPass();

        $container = $this->getContainer(false);

        $compilerPass->process($container);

        $container->compile();

        $this->assertInstanceOf(
            'eLife\IsolatedDrupalBehatExtension\Filesystem\NoOpFilesystemCleaner',
            $container->get('elife.isolated_drupal_behat.filesystem_cleaner')
        );
    }

    private function getContainer($cleanUp)
    {
        $container = new ContainerBuilder();
        $container->setParameter('elife_drupal_behat.clean_up', $cleanUp);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../../src/ServiceContainer/config')
        );

        $loader->load('filesystem.yml');

        $container->setDefinition(
            'elife.isolated_drupal_behat.symfony_filesystem',
            new Definition('Symfony\Component\Filesystem\Filesystem')
        );

        return $container;
    }
}
