<?php

namespace eLife\IsolatedDrupalBehatExtension\Event;

use eLife\IsolatedDrupalBehatExtension\Drupal;

final class WritingSiteSettingsFile extends SiteEvent
{
    const NAME = 'elife_drupal.site_settings';

    /**
     * @var string[]
     */
    private $settings = [];

    /**
     * @param Drupal $drupal
     * @param string $settings
     */
    public function __construct(Drupal $drupal, $settings = '')
    {
        parent::__construct($drupal);

        $this->addSettings($settings);
    }

    /**
     * @return string
     */
    public function getSettings()
    {
        return implode(PHP_EOL . PHP_EOL, $this->settings);
    }

    /**
     * @param string $settings
     */
    public function setSettings($settings)
    {
        $this->settings = [];
        $this->addSettings($settings);
    }

    /**
     * @param string $settings
     */
    public function addSettings($settings)
    {
        if (empty($settings)) {
            return;
        }

        $this->settings[] = trim($settings);
    }
}
