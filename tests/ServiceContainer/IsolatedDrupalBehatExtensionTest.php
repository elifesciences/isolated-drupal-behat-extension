<?php

namespace eLife\IsolatedDrupalBehatExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\ExtensionManager;
use eLife\IsolatedDrupalBehatExtension\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class IsolatedDrupalBehatExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function itHasAConfigKey()
    {
        $extension = new IsolatedDrupalBehatExtension();

        $this->assertSame('isolated_drupal', $extension->getConfigKey());
    }

    /**
     * @test
     */
    public function itInitializes()
    {
        $extension = new IsolatedDrupalBehatExtension();

        $extension->initialize(new ExtensionManager([]));
    }

    /**
     * @test
     */
    public function itProcesses()
    {
        $extension = new IsolatedDrupalBehatExtension();

        $extension->process(new ContainerBuilder());
    }

    /**
     * @test
     * @dataProvider successfulConfigurationProvider
     */
    public function itHasConfiguration($config, $expected)
    {
        $extension = new IsolatedDrupalBehatExtension();

        $builder = new ArrayNodeDefinition('foo');

        $extension->configure($builder);

        $this->assertEquals($builder->getNode()->finalize($config), $expected);
    }

    public function successfulConfigurationProvider()
    {
        return [
            [
                [
                    'db_url' => 'mysql://localhost/db',
                ],
                [
                    'db_url' => 'mysql://localhost/db',
                    'profile' => 'standard',
                    'settings_file' => null,
                    'clean_up' => true,
                ],
            ],
            [
                [
                    'db_url' => 'mysql://localhost/db',
                    'profile' => 'foo',
                    'settings_file' => __FILE__,
                    'clean_up' => false,
                ],
                [
                    'db_url' => 'mysql://localhost/db',
                    'profile' => 'foo',
                    'settings_file' => __FILE__,
                    'clean_up' => false,
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function itLoadsServices()
    {
        $extension = new IsolatedDrupalBehatExtension();

        $container = new ContainerBuilder();

        $extension->load(
            $container,
            [
                'db_url' => $dbUrl = 'mysql://localhost/db',
                'profile' => $profile = 'standard',
                'settings_file' => $settingsFile = null,
                'clean_up' => $cleanUp = true,
            ]
        );

        $this->assertSame(
            $dbUrl,
            $container->getParameter('elife_drupal_behat.db_url')
        );
        $this->assertSame(
            $profile,
            $container->getParameter('elife_drupal_behat.profile')
        );
        $this->assertSame(
            $settingsFile,
            $container->getParameter('elife_drupal_behat.settings_file')
        );
        $this->assertSame(
            $cleanUp,
            $container->getParameter('elife_drupal_behat.clean_up')
        );
    }
}
