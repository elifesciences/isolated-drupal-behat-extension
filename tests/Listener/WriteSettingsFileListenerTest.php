<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteCloned;
use eLife\IsolatedDrupalBehatExtension\Event\SiteEvent;
use eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile\RawPHPSettingsFileListener;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

final class WriteSettingsFileListenerTest extends ListenerTest
{
    /**
     * @test
     * @dataProvider eventProvider
     */
    public function itWritesASettingsFile($eventName, SiteEvent $event)
    {
        $root = vfsStream::setup('foo');

        $dispatcher = new EventDispatcher();

        $listener = new WriteSettingsFileListener(
            $dispatcher,
            new Filesystem()
        );
        $dispatcher->addSubscriber($listener);

        $dispatcher->dispatch($eventName, $event);

        $settingsFile = substr(
            $event->getDrupal()->getSitePath() . '/settings.php',
            10
        );
        $contents = '<?php ';

        $this->assertTrue($root->hasChild($settingsFile));
        $this->assertSame(
            $contents,
            $root->getChild($settingsFile)->getContent()
        );
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function itFindsContentForTheSettingsFile(
        $eventName,
        SiteEvent $event
    ) {
        $root = vfsStream::setup('foo');

        $dispatcher = $this->getDispatcher(new RawPHPSettingsFileListener('$foo = "bar";'));

        $listener = new WriteSettingsFileListener(
            $dispatcher,
            new Filesystem()
        );
        $dispatcher->addSubscriber($listener);

        $dispatcher->dispatch($eventName, $event);

        $settingsFile = substr(
            $event->getDrupal()->getSitePath() . '/settings.php',
            10
        );
        $contents = '<?php $foo = "bar";';

        $this->assertTrue($root->hasChild($settingsFile));
        $this->assertSame(
            $contents,
            $root->getChild($settingsFile)->getContent()
        );
    }

    public function eventProvider()
    {
        $drupal = new Drupal('vfs://foo/bar', 'http://localhost/', 'standard');

        return [
            [
                InstallingSite::NAME,
                new InstallingSite($drupal, new ProcessBuilder()),
            ],
            [
                SiteCloned::NAME,
                new SiteCloned($drupal),
            ],
        ];
    }
}
