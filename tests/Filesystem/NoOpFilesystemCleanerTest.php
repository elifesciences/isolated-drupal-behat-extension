<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

final class NoOpFilesystemCleanerTest extends FilesystemCleanerTest
{
    /**
     * @test
     */
    public function itDoesNotCleanPaths()
    {
        $cleaner = new NoOpFilesystemCleaner();

        $root = $this->createFilesystem('foo');

        $root->addChild($child = $this->createDirectory('bar'));

        $cleaner->clean([$child->url()]);

        $this->assertCount(1, $root->getChildren());
        $this->assertTrue($root->hasChild('bar'));
    }
}
