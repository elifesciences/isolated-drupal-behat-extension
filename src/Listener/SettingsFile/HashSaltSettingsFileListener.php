<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use eLife\IsolatedDrupalBehatExtension\RandomString\RandomStringGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class HashSaltSettingsFileListener implements EventSubscriber
{
    /**
     * @var RandomStringGenerator
     */
    private $generator;

    public static function getSubscribedEvents()
    {
        return [
            WritingSiteSettingsFile::NAME => 'onWritingSiteSettingsFile',
        ];
    }

    /**
     * @param RandomStringGenerator $generator
     */
    public function __construct(RandomStringGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param WritingSiteSettingsFile $event
     */
    public function onWritingSiteSettingsFile(WritingSiteSettingsFile $event)
    {
        $event->addSettings('$drupal_hash_salt = "' . $this->generator->generate() . '";');
    }
}
