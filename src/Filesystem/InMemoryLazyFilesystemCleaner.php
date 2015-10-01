<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

final class InMemoryLazyFilesystemCleaner implements LazyFilesystemCleaner
{
    /**
     * @var FilesystemCleaner
     */
    private $filesystemCleaner;

    /**
     * @var string[]
     */
    private $toClean = [];

    /**
     * @param FilesystemCleaner $filesystem
     */
    public function __construct(FilesystemCleaner $filesystem)
    {
        $this->filesystemCleaner = $filesystem;
    }

    public function register($path)
    {
        $this->toClean[] = $path;
    }

    public function cleanRegistered()
    {
        $this->filesystemCleaner->clean(array_values(array_unique($this->toClean)));
        $this->toClean = [];
    }
}
