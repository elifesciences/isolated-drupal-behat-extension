<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

final class WritingSiteSettingsFileTest extends SiteEventTest
{
    /**
     * @test
     */
    public function itHInitialSettings()
    {
        $event = new WritingSiteSettingsFile($this->generateDrupal(), 'foo');

        $this->assertSame('foo', $event->getSettings());
    }

    /**
     * @test
     */
    public function theSettingsCanBeOverriden()
    {
        $event = new WritingSiteSettingsFile($this->generateDrupal(), 'foo');

        $event->setSettings('bar');

        $this->assertSame('bar', $event->getSettings());
    }

    /**
     * @test
     */
    public function theSettingsCanBeAddedTo()
    {
        $event = new WritingSiteSettingsFile($this->generateDrupal());

        $this->assertSame('', $event->getSettings());

        $event->addSettings('foo');
        $this->assertSame('foo', $event->getSettings());

        $event->addSettings('bar');
        $this->assertSame(
            'foo' . PHP_EOL . PHP_EOL . 'bar',
            $event->getSettings()
        );
    }

    protected function getEvent()
    {
        return new WritingSiteSettingsFile($this->generateDrupal());
    }
}
