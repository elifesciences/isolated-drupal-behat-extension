<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteCloned;
use eLife\IsolatedDrupalBehatExtension\Event\SiteEvent;
use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;
use Symfony\Component\Filesystem\Filesystem;

final class WriteSettingsFileListener implements EventSubscriber
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public static function getSubscribedEvents()
    {
        return [
            InstallingSite::NAME => 'onSettingUpSite',
            SiteCloned::NAME => 'onSettingUpSite',
        ];
    }

    /**
     * @param EventDispatcher $eventDispatcher
     * @param Filesystem $filesystem
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        Filesystem $filesystem
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystem = $filesystem;
    }

    /**
     * @param SiteEvent $event
     */
    public function onSettingUpSite(SiteEvent $event)
    {
        $drupal = $event->getDrupal();

        $this->eventDispatcher->dispatch(
            WritingSiteSettingsFile::NAME,
            $settings = new WritingSiteSettingsFile($drupal)
        );

        $this->filesystem->mkdir($drupal->getSitePath());

        file_put_contents(
            $drupal->getSitePath() . '/settings.php',
            '<?php ' . $settings->getSettings()
        );
    }
}
