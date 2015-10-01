<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use Behat\Testwork\Environment\StaticEnvironment;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Behat\Testwork\Specification\NoSpecificationsIterator;
use Behat\Testwork\Suite\GenericSuite;
use Behat\Testwork\Tester\Result\IntegerTestResult;
use Behat\Testwork\Tester\Setup\SuccessfulTeardown;
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

        $cleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\LazyFilesystemCleaner');

        $listener = new CleanerListener($cleaner->reveal());
        $dispatcher = $this->getDispatcher($listener);

        $dispatcher->dispatch($eventName, $event);

        foreach ($toClean as $path => $times) {
            $cleaner->register($path)->shouldHaveBeenCalledTimes($times);
        }

        $cleaner->cleanRegistered()->willReturn();
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

    /**
     * @test
     */
    public function itClearsRegisteredPathsAfterTheSuiteIsRun()
    {
        $cleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\LazyFilesystemCleaner');

        $listener = new CleanerListener($cleaner->reveal());
        $dispatcher = $this->getDispatcher($listener);

        $suite = new GenericSuite('foo', []);

        $dispatcher->dispatch(
            SuiteTested::AFTER,
            new AfterSuiteTested(
                new StaticEnvironment($suite),
                new NoSpecificationsIterator($suite),
                new IntegerTestResult(1),
                new SuccessfulTeardown()
            )
        );

        $cleaner->cleanRegistered()->shouldHaveBeenCalled();
    }
}
