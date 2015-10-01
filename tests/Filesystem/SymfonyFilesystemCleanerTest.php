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

        $root->addChild($child1 = $this->createDirectory('bar'));
        $child1->addChild($this->createFile('baz'));
        $root->addChild($this->createDirectory('qux'));
        $root->addChild($child3 = $this->createDirectory('quxx'));

        $cleaner->clean([$child1->url(), $child3->url()]);

        $this->assertCount(1, $root->getChildren());
        $this->assertTrue($root->hasChild('qux'));
    }
}
