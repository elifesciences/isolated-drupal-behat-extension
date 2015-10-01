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
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function clean(array $paths)
    {
        $this->filesystem->remove(array_unique($paths));
    }
}
