<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

final class NoOpFilesystemCleaner implements FilesystemCleaner
{
    public function register($path)
    {
        // Do nothing.
    }
}
