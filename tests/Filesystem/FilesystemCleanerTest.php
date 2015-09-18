<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

use eLife\IsolatedDrupalBehatExtension\TestCase;
use org\bovigo\vfs\vfsStream;

abstract class FilesystemCleanerTest extends TestCase
{
    final protected function createFilesystem($name)
    {
        return vfsStream::setup($name);
    }

    final protected function createDirectory($path)
    {
        return vfsStream::newDirectory($path);
    }

    final protected function createFile($path)
    {
        $file = vfsStream::newFile($path);
        $file->setContent($path);

        return $file;
    }
}
