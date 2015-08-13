<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use eLife\IsolatedDrupalBehatExtension\RandomString\RandomStringGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class FilePathSettingsFileListener implements EventSubscriber
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
        $event->addSettings('$conf["file_public_path"] = "' . $event->getDrupal()
                ->getLocalPath() . '/files";');
        $event->addSettings('$conf["file_private_path"] = "' . $event->getDrupal()
                ->getLocalPath() . '/private";');
        $event->addSettings('$conf["file_temporary_path"] = "' . sys_get_temp_dir() . '/isolated-drupal-behat/' . $this->generator->generate() . '";');
    }
}
