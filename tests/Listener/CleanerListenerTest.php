<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteCloned;
use eLife\IsolatedDrupalBehatExtension\Event\SiteEvent;
use eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Process\ProcessBuilder;

final class CleanerListenerTest extends ListenerTest
{
    /**
     * @test
     * @dataProvider eventProvider
     */
    public function itRegistersSitePaths(
        $eventName,
        SiteEvent $event,
        array $toClean
    ) {
        vfsStream::setup('foo');

        $cleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner');

        $listener = new CleanerListener($cleaner->reveal());
        $dispatcher = $this->getDispatcher($listener);

        $dispatcher->dispatch($eventName, $event);

        foreach ($toClean as $path => $times) {
            $cleaner->register($path)->shouldHaveBeenCalledTimes($times);
        }
    }

    public function eventProvider()
    {
        $drupal = new Drupal('vfs://foo/bar', 'http://localhost/', 'standard');

        return [
            [
                InstallingSite::NAME,
                new InstallingSite($drupal, new ProcessBuilder()),
                [$drupal->getSitePath() => 1],
            ],
            [
                SiteInstalled::NAME,
                new SiteInstalled($drupal),
                [$drupal->getSitePath() => 1],
            ],
            [
                SiteCloned::NAME,
                new SiteCloned($drupal),
                [$drupal->getSitePath() => 1],
            ],
        ];
    }
}
