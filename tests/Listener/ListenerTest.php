<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use eLife\IsolatedDrupalBehatExtension\TestCase;

abstract class ListenerTest extends TestCase
{
    final protected function getDispatcher(EventSubscriberInterface $listener)
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->addSubscriber($listener);

        return $dispatcher;
    }
}
