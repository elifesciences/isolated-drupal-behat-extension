<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;

final class RawPHPSettingsFileListenerTest extends SettingsFileListenerTest
{
    /**
     * @test
     */
    public function itAddsSettings()
    {
        $listener = new RawPHPSettingsFileListener('$foo = "bar";');
        $dispatcher = $this->getDispatcher($listener);

        $event = $this->getEvent();
        $dispatcher->dispatch($event::NAME, $event);

        $this->assertSame('$foo = "bar";', $event->getSettings());
    }

    public function getEvent()
    {
        $drupal = new Drupal('/foo/bar', 'http://localhost/', 'standard');

        return new WritingSiteSettingsFile($drupal);
    }
}
