<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

use Symfony\Component\Process\ProcessBuilder;

final class InstallingSiteTest extends SiteEventTest
{
    /**
     * @test
     */
    public function itHasACommand()
    {
        $processBuilder = new ProcessBuilder();

        $event = new InstallingSite($this->generateDrupal(), $processBuilder);

        $this->assertSame($processBuilder, $event->getCommand());
    }

    protected function getEvent()
    {
        return new InstallingSite(
            $this->generateDrupal(),
            new ProcessBuilder()
        );
    }
}
