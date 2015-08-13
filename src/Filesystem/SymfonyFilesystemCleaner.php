<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

final class SymfonyFilesystemCleaner implements FilesystemCleaner
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string[]
     */
    private $toClean = [];

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        // @codeCoverageIgnoreStart
        register_shutdown_function(function () {
            $this->clean();
        });
        // @codeCoverageIgnoreEnd
    }

    public function register($path)
    {
        $this->toClean[] = $path;
    }

    public function clean()
    {
        $this->filesystem->remove(array_unique($this->toClean));
        $this->toClean = [];
    }
}
