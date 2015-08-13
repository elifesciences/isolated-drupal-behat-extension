<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use eLife\IsolatedDrupalBehatExtension\RandomString\StaticStringGenerator;

final class FilePathSettingsFileListenerTest extends SettingsFileListenerTest
{
    /**
     * @test
     */
    public function itWritesToTheSettingsFile()
    {
        $drupal = new Drupal('/foo/bar', 'http://localhost/', 'standard');
        $listener = new FilePathSettingsFileListener(new StaticStringGenerator('baz'));

        $dispatcher = $this->getDispatcher($listener);

        $dispatcher->dispatch(
            WritingSiteSettingsFile::NAME,
            $event = new WritingSiteSettingsFile($drupal)
        );

        $this->assertContains(
            '$conf["file_public_path"] = "' . $drupal->getLocalPath() . '/files";',
            $event->getSettings()
        );
        $this->assertContains(
            '$conf["file_private_path"] = "' . $drupal->getLocalPath() . '/private";',
            $event->getSettings()
        );
        $this->assertContains(
            '$conf["file_temporary_path"] = "' . sys_get_temp_dir() . '/isolated-drupal-behat/baz";',
            $event->getSettings()
        );
    }
}
