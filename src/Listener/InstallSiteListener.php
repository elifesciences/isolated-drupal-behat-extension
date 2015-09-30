<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled;
use eLife\IsolatedDrupalBehatExtension\Process\ProcessRunner;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;
use Symfony\Component\Process\ProcessBuilder;

final class InstallSiteListener implements EventSubscriber
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var Drupal
     */
    private $drupal;

    /**
     * @var string
     */
    private $binary;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public static function getSubscribedEvents()
    {
        return [
            // Drupal extension will try and bootstrap at the beginning of the suite.
            SuiteTested::BEFORE => ['onBeforeScenarioTested', 255],
            ScenarioTested::BEFORE => ['onBeforeScenarioTested', 255],
            ExampleTested::BEFORE => ['onBeforeScenarioTested', 255],
        ];
    }

    /**
     * @param EventDispatcher $eventDispatcher
     * @param Drupal $drupal
     * @param string $drush
     * @param ProcessRunner $processRunner
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        Drupal $drupal,
        $drush,
        ProcessRunner $processRunner
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->drupal = $drupal;
        $this->binary = $drush;
        $this->processRunner = $processRunner;
    }

    public function onBeforeScenarioTested()
    {
        $processBuilder = ProcessBuilder::create()
            ->setPrefix($this->binary)
            ->add('site-install')
            ->add($this->drupal->getProfile())
            ->add('--sites-subdir=' . $this->drupal->getSiteDir())
            ->add('--account-name=admin')
            ->add('--account-pass=password')
            ->add('--yes')
            ->setWorkingDirectory($this->drupal->getSitePath())
            ->setTimeout(null)
            ->setEnv('PHP_OPTIONS', '-d sendmail_path=' . `which true`);

        $this->eventDispatcher->dispatch(
            InstallingSite::NAME,
            $event = new InstallingSite($this->drupal, $processBuilder)
        );

        if (!$event->isPropagationStopped()) {
            $this->processRunner->run($processBuilder->getProcess());
            $this->eventDispatcher->dispatch(
                SiteInstalled::NAME,
                new SiteInstalled($this->drupal)
            );
        }
    }
}
