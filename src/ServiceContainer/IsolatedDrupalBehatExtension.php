<?php

namespace eLife\IsolatedDrupalBehatExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension as TestworkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class IsolatedDrupalBehatExtension implements TestworkExtension
{
    public function process(ContainerBuilder $container)
    {
    }

    public function getConfigKey()
    {
        return 'isolated_drupal';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('db_url')->isRequired()->end()
                    ->scalarNode('profile')->defaultValue('standard')->end()
                    ->scalarNode('settings_file')->defaultNull()->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/config')
        );

        $loader->load('filesystem.yml');
        $loader->load('listeners.yml');
        $loader->load('listeners_settings_file.yml');
        $loader->load('process_runners.yml');
        $loader->load('random_string_generators.yml');
        $loader->load('services.yml');

        $container->setParameter(
            'elife_drupal_behat.db_url',
            $config['db_url']
        );
        $container->setParameter(
            'elife_drupal_behat.profile',
            $config['profile']
        );
        $container->setParameter(
            'elife_drupal_behat.settings_file',
            $config['settings_file']
        );
    }
}
