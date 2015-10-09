<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

interface FilesystemCleaner
{
    /**
     * @param string[] $paths
     */
    public function clean(array $paths);
}
