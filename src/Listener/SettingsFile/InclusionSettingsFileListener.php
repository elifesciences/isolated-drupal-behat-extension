<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class InclusionSettingsFileListener implements EventSubscriber
{
    /**
     * @var string
     */
    private $settingsFile;

    public static function getSubscribedEvents()
    {
        return [
            WritingSiteSettingsFile::NAME => ['onWritingSiteSettingsFile', 255],
        ];
    }

    /**
     * @param string $settingsFile
     */
    public function __construct($settingsFile)
    {
        $this->settingsFile = $settingsFile;
    }

    /**
     * @param WritingSiteSettingsFile $event
     */
    public function onWritingSiteSettingsFile(WritingSiteSettingsFile $event)
    {
        if (!empty($this->settingsFile)) {
            $event->addSettings('require "' . $this->settingsFile . '";');
        }
    }
}
