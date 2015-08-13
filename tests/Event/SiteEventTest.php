<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

use eLife\IsolatedDrupalBehatExtension\TestCase;

abstract class SiteEventTest extends TestCase
{
    /**
     * @test
     */
    final public function itHasASite()
    {
        $this->assertInstanceOf(
            'eLife\IsolatedDrupalBehatExtension\Drupal',
            $this->getEvent()->getDrupal()
        );
    }

    /**
     * @return SiteEvent
     */
    abstract protected function getEvent();
}
