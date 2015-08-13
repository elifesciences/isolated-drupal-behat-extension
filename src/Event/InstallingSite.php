<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use Symfony\Component\Process\ProcessBuilder;

final class InstallingSite extends SiteEvent
{
    const NAME = 'elife_drupal.installing_site';

    /**
     * @var ProcessBuilder
     */
    private $command;

    /**
     * @param Drupal $drupal
     * @param ProcessBuilder $command
     */
    public function __construct(Drupal $drupal, ProcessBuilder $command)
    {
        parent::__construct($drupal);

        $this->command = $command;
    }

    /**
     * @return ProcessBuilder
     */
    public function getCommand()
    {
        return $this->command;
    }
}
