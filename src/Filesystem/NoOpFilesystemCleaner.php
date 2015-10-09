<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

final class NoOpFilesystemCleaner implements FilesystemCleaner
{
    public function clean(array $paths)
    {
        // Do nothing.
    }
}
