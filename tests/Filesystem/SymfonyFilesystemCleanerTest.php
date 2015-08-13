<?php

namespace eLife\IsolatedDrupalBehatExtension\Filesystem;

use eLife\IsolatedDrupalBehatExtension\TestCase;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Filesystem\Filesystem;

final class SymfonyFilesystemCleanerTest extends TestCase
{
    /**
     * @test
     */
    public function itCleansPaths()
    {
        $cleaner = new SymfonyFilesystemCleaner(new Filesystem());

        $root = vfsStream::setup('foo');

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

    private function createDirectory($path)
    {
        return vfsStream::newDirectory($path);
    }

    private function createFile($path)
    {
        $file = vfsStream::newFile($path);
        $file->setContent($path);

        return $file;
    }
}
