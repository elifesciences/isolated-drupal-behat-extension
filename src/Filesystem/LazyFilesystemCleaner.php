<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

interface LazyFilesystemCleaner
{
    /**
     * @param string $path
     */
    public function register($path);

    public function cleanRegistered();
}
