<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use Behat\Testwork\Environment\StaticEnvironment;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\EventDispatcher\Event\LifecycleEvent;
use Behat\Testwork\Specification\NoSpecificationsIterator;
use Behat\Testwork\Suite\GenericSuite;
use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled;
use PHPUnit_Framework_Assert as Assert;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prediction\CallbackPrediction;

final class InstallSiteListenerTest extends ListenerTest
{
    /**
     * @test
     * @dataProvider eventProvider
     */
    public function itInstallsASite($eventName, LifecycleEvent $event)
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $drupal = $this->generateDrupal();
        $processRunner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Process\ProcessRunner');

        $listener = new InstallSiteListener(
            $dispatcher->reveal(),
            $drupal,
            '/path/to/drush',
            $processRunner->reveal()
        );

        $dispatcher
            ->dispatch(
                InstallingSite::NAME,
                Argument::type('eLife\IsolatedDrupalBehatExtension\Event\InstallingSite')
            )
            ->shouldBeCalledTimes(1)
            ->should(new CallbackPrediction(function (array $calls) use ($drupal) {
                /** @var Call $call */
                $call = $calls[0];

                /** @var InstallingSite $event */
                $event = $call->getArguments()[1];

                $process = $event->getCommand()->getProcess();

                Assert::assertSame(
                    "'/path/to/drush' 'site-install' 'standard' " .
                    "'--sites-subdir=localhost' '--account-name=admin' " .
                    "'--account-pass=password' '--yes'",
                    $process->getCommandLine()
                );
                Assert::assertArrayHasKey('PHP_OPTIONS', $process->getEnv());
                Assert::assertSame(
                    '-d sendmail_path=' . `which true`,
                    $process->getEnv()['PHP_OPTIONS']
                );
                Assert::assertSame(
                    $drupal->getPath(),
                    $process->getWorkingDirectory()
                );
            }));

        $processRunner
            ->run(Argument::type('Symfony\Component\Process\Process'))
            ->shouldBeCalledTimes(1);

        $dispatcher
            ->dispatch(
                SiteInstalled::NAME,
                Argument::type('eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled')
            )
            ->shouldBeCalledTimes(1)
            ->should(new CallbackPrediction(function (array $calls) use ($drupal
            ) {
                /** @var Call $call */
                $call = $calls[0];

                /** @var SiteInstalled $event */
                $event = $call->getArguments()[1];

                Assert::assertEquals($drupal, $event->getDrupal());
            }));

        $this->getDispatcher($listener)->dispatch($eventName, $event);
    }

    public function eventProvider()
    {
        $suite = new GenericSuite('foo', []);

        return [
            [
                BeforeSuiteTested::BEFORE,
                new BeforeSuiteTested(
                    new StaticEnvironment($suite),
                    new NoSpecificationsIterator($suite)
                )
            ]
        ];
    }
}
