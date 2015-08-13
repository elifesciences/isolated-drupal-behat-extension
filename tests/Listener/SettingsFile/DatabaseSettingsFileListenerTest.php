<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener\SettingsFile;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\WritingSiteSettingsFile;

final class DatabaseSettingsFileListenerTest extends SettingsFileListenerTest
{
    /**
     * @test
     * @dataProvider dbProvider
     */
    public function itAddsSettings($dbUrl, $expected)
    {
        $listener = new DatabaseSettingsFileListener($dbUrl);
        $dispatcher = $this->getDispatcher($listener);

        $event = $this->getEvent();
        $dispatcher->dispatch($event::NAME, $event);

        $expected = strtr('$databases = [
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
          "prefix" => ":prefix",
        ],
    ],
];', $expected);

        $this->assertSame($expected, $event->getSettings());
    }

    public function dbProvider()
    {
        return [
            [
                'mysql://localhost/db',
                [
                    ':driver' => 'mysql',
                    ':database' => 'db',
                    ':username' => '',
                    ':password' => '',
                    ':host' => 'localhost',
                    ':port' => '',
                    ':prefix' => '',
                ],
            ],
            [
                'mysqli://localhost/db',
                [
                    ':driver' => 'mysql',
                    ':database' => 'db',
                    ':username' => '',
                    ':password' => '',
                    ':host' => 'localhost',
                    ':port' => '',
                    ':prefix' => '',
                ],
            ],
            [
                'mysql://foo:bar@localhost:1234/db',
                [
                    ':driver' => 'mysql',
                    ':database' => 'db',
                    ':username' => 'foo',
                    ':password' => 'bar',
                    ':host' => 'localhost',
                    ':port' => '1234',
                    ':prefix' => '',
                ],
            ],
            [
                'sqlite:/foo/db.sqlite',
                [
                    ':driver' => 'sqlite',
                    ':database' => 'foo/db.sqlite',
                    ':username' => '',
                    ':password' => '',
                    ':host' => '',
                    ':port' => '',
                    ':prefix' => '',
                ],
            ],
        ];
    }

    public function getEvent()
    {
        $drupal = new Drupal('/foo/bar', 'http://localhost/', 'standard');

        return new WritingSiteSettingsFile($drupal);
    }
}
