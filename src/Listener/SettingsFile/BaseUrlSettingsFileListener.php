<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class BaseUrlSettingsFileListener implements EventSubscriber
{
    public static function getSubscribedEvents()
    {
        return [
            WritingSiteSettingsFile::NAME => 'onWritingSiteSettingsFile',
        ];
    }

    /**
     * @param WritingSiteSettingsFile $event
     */
    public function onWritingSiteSettingsFile(WritingSiteSettingsFile $event)
    {
        $uri = rtrim($event->getDrupal()->getUri(), '/');

        $event->addSettings('$base_url = "' . $uri . '";');
    }
}
