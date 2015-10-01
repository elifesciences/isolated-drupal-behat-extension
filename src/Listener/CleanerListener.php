<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteCloned;
use eLife\IsolatedDrupalBehatExtension\Event\SiteEvent;
use eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled;
use eLife\IsolatedDrupalBehatExtension\Filesystem\LazyFilesystemCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class CleanerListener implements EventSubscriber
{
    /**
     * @var LazyFilesystemCleaner
     */
    private $cleaner;

    public static function getSubscribedEvents()
    {
        return [
            SuiteTested::AFTER => ['onAfterSuiteTested', -255],
            InstallingSite::NAME => 'onSiteEvent',
            SiteInstalled::NAME => 'onSiteEvent',
            SiteCloned::NAME => 'onSiteEvent',
        ];
    }

    /**
     * @param LazyFilesystemCleaner $cleaner
     */
    public function __construct(LazyFilesystemCleaner $cleaner)
    {
        $this->cleaner = $cleaner;

        // @codeCoverageIgnoreStart
        register_shutdown_function(function () {
            $this->onAfterSuiteTested(); // In case of a fatal error.
        });
        // @codeCoverageIgnoreEnd
    }

    public function onAfterSuiteTested()
    {
        $this->cleaner->cleanRegistered();
    }

    /**
     * @param SiteEvent $event
     */
    public function onSiteEvent(SiteEvent $event)
    {
        $this->cleaner->register($event->getDrupal()->getSitePath());
    }
}
