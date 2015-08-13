<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

final class DatabaseSettingsFileListener implements EventSubscriber
{
    /**
     * @var string
     */
    private $dbUri;

    public static function getSubscribedEvents()
    {
        return [
            WritingSiteSettingsFile::NAME => [
                'onWritingSiteSettingsFile',
                -255,
            ],
        ];
    }

    /**
     * @param string $dbUri
     */
    public function __construct($dbUri)
    {
        $this->dbUri = $dbUri;
    }

    /**
     * @param WritingSiteSettingsFile $event
     */
    public function onWritingSiteSettingsFile(WritingSiteSettingsFile $event)
    {
        $url = parse_url($this->dbUri);

        $url += [
            'scheme' => '',
            'path' => '',
            'user' => '',
            'pass' => '',
            'host' => '',
            'port' => '',
        ];

        $database = array(
            // MySQLi uses the mysql driver.
            ':driver' => $url['scheme'] == 'mysqli' ? 'mysql' : $url['scheme'],
            // Remove the leading slash to get the database name.
            ':database' => ltrim(urldecode($url['path']), '/'),
            ':username' => urldecode($url['user']),
            ':password' => urldecode($url['pass']),
            ':host' => urldecode($url['host']),
            ':port' => urldecode($url['port']),
        );

        $event->addSettings(strtr('$databases = [
  "default" =>
    [
      "default" =>
        [
          "driver" => ":driver",
          "database" => ":database",
          "username" => ":username",
          "password" => ":password",
          "host" => ":host",
          "port" => ":port",
          "prefix" => "",
        ],
    ],
];', $database));
    }
}
