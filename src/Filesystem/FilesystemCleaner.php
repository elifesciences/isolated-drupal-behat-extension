<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

interface FilesystemCleaner
{
    /**
     * @param string $path
     */
    public function register($path);
}
