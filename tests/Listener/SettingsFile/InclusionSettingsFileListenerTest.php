<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;

final class InclusionSettingsFileListenerTest extends SettingsFileListenerTest
{
    /**
     * @test
     */
    public function itIncludesASettingsFile()
    {
        $listener = new InclusionSettingsFileListener('/foo/bar.php');

        $dispatcher = $this->getDispatcher($listener);

        $dispatcher->dispatch(
            WritingSiteSettingsFile::NAME,
            $event = new WritingSiteSettingsFile($this->generateDrupal())
        );

        $this->assertSame('require "/foo/bar.php";', $event->getSettings());
    }
}
