<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteCloned;
use eLife\IsolatedDrupalBehatExtension\Event\SiteEvent;
use eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled;
use eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class CleanerListener implements EventSubscriber
{
    /**
     * @var FilesystemCleaner
     */
    private $cleaner;

    public static function getSubscribedEvents()
    {
        return [
            InstallingSite::NAME => 'onSiteEvent',
            SiteInstalled::NAME => 'onSiteEvent',
            SiteCloned::NAME => 'onSiteEvent',
        ];
    }

    /**
     * @param FilesystemCleaner $cleaner
     */
    public function __construct(FilesystemCleaner $cleaner)
    {
        $this->cleaner = $cleaner;
    }

    /**
     * @param SiteEvent $event
     */
    public function onSiteEvent(SiteEvent $event)
    {
        $this->cleaner->register($event->getDrupal()->getSitePath());
    }
}
