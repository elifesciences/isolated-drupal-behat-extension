<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use Behat\Testwork\Environment\StaticEnvironment;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Behat\Testwork\Specification\NoSpecificationsIterator;
use Behat\Testwork\Suite\GenericSuite;
use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteCloned;
use eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_Assert as Assert;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prediction\CallbackPrediction;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

final class BypassInstallSiteListenerTest extends ListenerTest
{
    /**
     * @test
     */
    public function itRemovesExistingCopiesBeforeTheSuiteIsRun()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $root = vfsStream::setup('foo');
        $root->addChild($sites = vfsStream::newDirectory('sites'));
        $sites->addChild(vfsStream::newDirectory('localhost.master'));
        $drupal = new Drupal('vfs://foo/', 'http://localhost/', 'standard');
        $processRunner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Process\ProcessRunner');
        $cleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner');
        $lazyCleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\LazyFilesystemCleaner');

        $listener = new BypassInstallSiteListener(
            $dispatcher->reveal(),
            $drupal,
            new Filesystem(),
            '/path/to/drush',
            $processRunner->reveal(),
            $cleaner->reveal(),
            $lazyCleaner->reveal()
        );

        $masterPath = $drupal->getSitePath() . '.master';

        $realDispatcher = $this->getDispatcher($listener);

        $suite = new GenericSuite('foo', []);

        $realDispatcher->dispatch(
            SuiteTested::BEFORE,
            new BeforeSuiteTested(
                new StaticEnvironment($suite),
                new NoSpecificationsIterator($suite)
            )
        );

        $cleaner->clean([$masterPath])->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itTakesACopyOfAnInstalledSite()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $root = vfsStream::setup('foo');
        $root->addChild($sites = vfsStream::newDirectory('sites'));
        $sites->addChild($site = vfsStream::newDirectory('localhost'));
        $site->addChild($file = vfsStream::newFile('foo.tmp'));
        $drupal = new Drupal('vfs://foo/', 'http://localhost/', 'standard');
        $processRunner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Process\ProcessRunner');
        $cleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner');
        $lazyCleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\LazyFilesystemCleaner');

        $listener = new BypassInstallSiteListener(
            $dispatcher->reveal(),
            $drupal,
            new Filesystem(),
            '/path/to/drush',
            $processRunner->reveal(),
            $cleaner->reveal(),
            $lazyCleaner->reveal()
        );

        $masterPath = $drupal->getSitePath() . '.master';

        $realDispatcher = $this->getDispatcher($listener);

        $realDispatcher->dispatch(
            SiteInstalled::NAME,
            new SiteInstalled($drupal)
        );

        $this->assertFileExists($masterPath . '/foo.tmp');

        $processRunner
            ->run(Argument::type('Symfony\Component\Process\Process'))
            ->shouldBeCalledTimes(1)
            ->should(new CallbackPrediction(function (array $calls) {
                /** @var Call $call */
                $call = $calls[0];

                /** @var Process $process */
                $process = $call->getArguments()[0];

                Assert::assertSame(
                    "'/path/to/drush' 'sql-dump' '--result-file=vfs://foo/sites/localhost.master/db.sql' '--uri=http://localhost/' '--yes'",
                    $process->getCommandLine()
                );
            }));


        $lazyCleaner->register($masterPath)->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function nothingHappensIfThereIsNotACopyOfAnInstalledSite()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $root = vfsStream::setup('foo');
        $root->addChild($sites = vfsStream::newDirectory('sites'));
        $drupal = new Drupal('vfs://foo/', 'http://localhost/', 'standard');
        $processRunner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Process\ProcessRunner');
        $cleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner');
        $lazyCleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\LazyFilesystemCleaner');

        $listener = new BypassInstallSiteListener(
            $dispatcher->reveal(),
            $drupal,
            new Filesystem(),
            '/path/to/drush',
            $processRunner->reveal(),
            $cleaner->reveal(),
            $lazyCleaner->reveal()
        );

        $realDispatcher = $this->getDispatcher($listener);

        $realDispatcher->dispatch(
            InstallingSite::NAME,
            new InstallingSite($drupal, new ProcessBuilder())
        );
    }

    /**
     * @test
     */
    public function itUsesACopyOfAnInstalledSite()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $root = vfsStream::setup('foo');
        $root->addChild($sites = vfsStream::newDirectory('sites'));
        $sites->addChild($site = vfsStream::newDirectory('localhost'));
        $site->addChild($file = vfsStream::newFile('bar.tmp'));
        $sites->addChild($site = vfsStream::newDirectory('localhost.master'));
        $site->addChild($file = vfsStream::newFile('foo.tmp'));
        $site->addChild($file = vfsStream::newFile('db.sql'));
        $drupal = new Drupal('vfs://foo/', 'http://localhost/', 'standard');
        $processRunner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Process\ProcessRunner');
        $cleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner');
        $lazyCleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\LazyFilesystemCleaner');

        $listener = new BypassInstallSiteListener(
            $dispatcher->reveal(),
            $drupal,
            new Filesystem(),
            '/path/to/drush',
            $processRunner->reveal(),
            $cleaner->reveal(),
            $lazyCleaner->reveal()
        );

        $realDispatcher = $this->getDispatcher($listener);

        $realDispatcher->dispatch(
            InstallingSite::NAME,
            $event = new InstallingSite($drupal, new ProcessBuilder())
        );

        $this->assertFileExists($drupal->getSitePath() . '/foo.tmp');
        $this->assertFileNotExists($drupal->getSitePath() . '/db.sql');
        $this->assertFileNotExists($drupal->getSitePath() . '/bar.tmp');

        $processRunner
            ->run(Argument::type('Symfony\Component\Process\Process'))
            ->shouldBeCalledTimes(2)
            ->should(new CallbackPrediction(function (array $calls) use ($drupal
            ) {
                /** @var Call[] $calls */

                /** @var Process $process0 */
                $process0 = $calls[0]->getArguments()[0];
                /** @var Process $process1 */
                $process1 = $calls[1]->getArguments()[0];

                Assert::assertSame(
                    "'/path/to/drush' 'sql-drop' '--uri=http://localhost/' '--yes'",
                    $process0->getCommandLine()
                );
                Assert::assertSame(
                    $drupal->getPath(),
                    $process0->getWorkingDirectory()
                );
                Assert::assertSame(
                    "'/path/to/drush' 'sql-query' '--file=vfs://foo/sites/localhost/db.sql' '--uri=http://localhost/' '--yes'",
                    $process1->getCommandLine()
                );
                Assert::assertSame(
                    $drupal->getPath(),
                    $process1->getWorkingDirectory()
                );
            }));

        $dispatcher
            ->dispatch(
                SiteCloned::NAME,
                Argument::type('eLife\IsolatedDrupalBehatExtension\Event\SiteCloned')
            )
            ->shouldHaveBeenCalled();

        $this->assertTrue($event->isPropagationStopped());
    }
}
