<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class RawPHPSettingsFileListener implements EventSubscriber
{
    /**
     * @var string
     */
    private $php;

    public static function getSubscribedEvents()
    {
        return [
            WritingSiteSettingsFile::NAME => ['onWritingSiteSettingsFile'],
        ];
    }

    /**
     * @param string $php
     */
    public function __construct($php)
    {
        $this->php = $php;
    }

    /**
     * @param WritingSiteSettingsFile $event
     */
    public function onWritingSiteSettingsFile(WritingSiteSettingsFile $event)
    {
        $event->addSettings($this->php);
    }
}
