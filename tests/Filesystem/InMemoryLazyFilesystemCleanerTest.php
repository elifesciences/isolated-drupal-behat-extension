<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

use eLife\IsolatedDrupalBehatExtension\TestCase;

final class InMemoryLazyFilesystemCleanerTest extends TestCase
{
    /**
     * @test
     */
    public function itCleansPaths()
    {
        $filesystemCleaner = $this->prophesize('eLife\IsolatedDrupalBehatExtension\Filesystem\FilesystemCleaner');

        $cleaner = new InMemoryLazyFilesystemCleaner($filesystemCleaner->reveal());

        $cleaner->register('/foo');
        $cleaner->register('/foo');
        $cleaner->register('/bar');

        $filesystemCleaner->clean(['/foo', '/bar'])->shouldBeCalled();

        $cleaner->cleanRegistered();
    }
}
