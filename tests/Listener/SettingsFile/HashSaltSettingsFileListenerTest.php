<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use eLife\IsolatedDrupalBehatExtension\RandomString\StaticStringGenerator;

final class HashSaltSettingsFileListenerTest extends SettingsFileListenerTest
{
    /**
     * @test
     */
    public function itWritesToTheSettingsFile()
    {
        $drupal = new Drupal('/foo/bar', 'http://localhost/', 'standard');
        $listener = new HashSaltSettingsFileListener(new StaticStringGenerator('baz'));

        $dispatcher = $this->getDispatcher($listener);

        $dispatcher->dispatch(
            WritingSiteSettingsFile::NAME,
            $event = new WritingSiteSettingsFile($drupal)
        );

        $this->assertSame('$drupal_hash_salt = "baz";', $event->getSettings());
    }
}
