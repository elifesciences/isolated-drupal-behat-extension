<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

final class SiteInstalledTest extends SiteEventTest
{
    protected function getEvent()
    {
        return new SiteInstalled($this->generateDrupal());
    }
}
