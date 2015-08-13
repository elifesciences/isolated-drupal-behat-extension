<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

final class SiteClonedTest extends SiteEventTest
{
    protected function getEvent()
    {
        return new SiteCloned($this->generateDrupal());
    }
}
