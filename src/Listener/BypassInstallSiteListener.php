<?php

namespace eLife\IsolatedDrupalBehatExtension\Listener;

use eLife\IsolatedDrupalBehatExtension\Drupal;
use eLife\IsolatedDrupalBehatExtension\Event\SiteCloned;
use eLife\IsolatedDrupalBehatExtension\Event\InstallingSite;
use eLife\IsolatedDrupalBehatExtension\Event\SiteInstalled;
use eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner;
use eLife\IsolatedDrupalBehatExtension\Process\ProcessRunner;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

final class BypassInstallSiteListener implements EventSubscriber
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var Drupal
     */
    private $drupal;

    /**
     * @var string
     */
    private $masterPath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $binary;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var FilesystemCleaner
     */
    private $filesystemCleaner;

    public static function getSubscribedEvents()
    {
        return [
            InstallingSite::NAME => ['onInstallingSite', 255],
            SiteInstalled::NAME => ['onSiteInstalled', 255],
        ];
    }

    /**
     * @param EventDispatcher $eventDispatcher
     * @param Drupal $drupal
     * @param Filesystem $filesystem
     * @param string $drush
     * @param ProcessRunner $processRunner
     * @param FilesystemCleaner $filesystemCleaner
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        Drupal $drupal,
        Filesystem $filesystem,
        $drush,
        ProcessRunner $processRunner,
        FilesystemCleaner $filesystemCleaner
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->drupal = $drupal;
        $this->masterPath = $drupal->getSitePath() . '.master';
        $this->filesystem = $filesystem;
        $this->binary = $drush;
        $this->processRunner = $processRunner;
        $this->filesystemCleaner = $filesystemCleaner;
    }

    public function onInstallingSite(InstallingSite $event)
    {
        if (!is_dir($this->masterPath)) {
            // Master copy doesn't yet exist, so let it be created.
            return;
        }

        // Import the site's folder.
        if (is_dir($this->drupal->getSitePath())) {
            $this->filesystem->chmod(
                $this->drupal->getSitePath(),
                0777,
                0000,
                true
            );
        }
        $this->filesystem->mirror(
            $this->masterPath,
            $this->drupal->getSitePath(),
            null,
            [
                'override' => true,
                'delete' => true,
            ]
        );

        // Clean the database. (Mainly for SQLite.)
        $this->processRunner->run(
            ProcessBuilder::create()
                ->setPrefix($this->binary)
                ->add('sql-drop')
                ->add('--yes')
                ->setWorkingDirectory($this->drupal->getSitePath())
                ->setTimeout(null)
                ->getProcess()
        );

        // Import the database.
        $this->processRunner->run(
            ProcessBuilder::create()
                ->setPrefix($this->binary)
                ->add('sql-query')
                ->add('--file=' . $this->drupal->getSitePath() . '/db.sql')
                ->add('--yes')
                ->setWorkingDirectory($this->drupal->getSitePath())
                ->setTimeout(null)
                ->getProcess()
        );
        $this->filesystem->remove($this->drupal->getSitePath() . '/db.sql');

        $this->eventDispatcher->dispatch(
            SiteCloned::NAME,
            new SiteCloned($this->drupal)
        );

        // Signify that a site isn't actually being installed.
        $event->stopPropagation();
    }

    public function onSiteInstalled()
    {
        $this->filesystemCleaner->register($this->masterPath);

        $this->filesystem->mirror(
            $this->drupal->getSitePath(),
            $this->masterPath,
            null,
            [
                'override' => true,
                'delete' => true,
            ]
        );

        // Take a copy of the database.
        $this->processRunner->run(
            ProcessBuilder::create()
                ->setPrefix($this->binary)
                ->add('sql-dump')
                ->add('--result-file=' . $this->masterPath . '/db.sql')
                ->add('--yes')
                ->setWorkingDirectory($this->drupal->getSitePath())
                ->setTimeout(null)
                ->getProcess()
        );
    }
}
