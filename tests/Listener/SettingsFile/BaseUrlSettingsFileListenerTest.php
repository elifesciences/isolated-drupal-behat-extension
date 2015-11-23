<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;

final class BaseUrlSettingsFileListenerTest extends SettingsFileListenerTest
{
    /**
     * @test
     */
    public function itWritesToTheSettingsFile()
    {
        $drupal = new Drupal('/foo/bar', 'http://localhost/', 'standard');
        $listener = new BaseUrlSettingsFileListener();

        $dispatcher = $this->getDispatcher($listener);

        $dispatcher->dispatch(
            WritingSiteSettingsFile::NAME,
            $event = new WritingSiteSettingsFile($drupal)
        );

        $this->assertSame(
            '$base_url = "http://localhost";',
            $event->getSettings()
        );
    }
}
