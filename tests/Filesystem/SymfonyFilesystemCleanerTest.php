<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

final class SymfonyFilesystemCleanerTest extends FilesystemCleanerTest
{
    /**
     * @test
     */
    public function itCleansPaths()
    {
        $cleaner = new SymfonyFilesystemCleaner(new Filesystem());

        $root = $this->createFilesystem('foo');

        $root->addChild($dir = $this->createDirectory('bar'));
        $cleaner->register($dir->url());
        $dir->addChild($dirFile = $this->createFile('baz'));
        $cleaner->register($dirFile->url());
        $root->addChild($emptyDir = $this->createDirectory('qux'));
        $cleaner->register($root->url() . '/qux');
        $root->addChild($file = $this->createFile('quxx'));
        $cleaner->register($file->url());
        $root->addChild($file = $this->createFile('other'));

        $cleaner->clean();

        $this->assertCount(1, $root->getChildren());
        $this->assertTrue($root->hasChild('other'));
    }
}
