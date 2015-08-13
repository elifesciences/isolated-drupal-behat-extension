<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use Symfony\Component\EventDispatcher\Event;

abstract class SiteEvent extends Event
{
    /**
     * @var Drupal
     */
    private $drupal;

    /**
     * @param Drupal $drupal
     */
    public function __construct(Drupal $drupal)
    {
        $this->drupal = $drupal;
    }

    /**
     * @return Drupal
     */
    final public function getDrupal()
    {
        return $this->drupal;
    }
}
