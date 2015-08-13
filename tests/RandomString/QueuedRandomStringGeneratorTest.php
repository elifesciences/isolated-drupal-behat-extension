<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

final class QueuedRandomStringGeneratorTest extends RandomStringGeneratorTest
{
    /**
     * @var QueuedRandomStringGenerator
     */
    private $generator;

    public function setUp()
    {
        $this->generator = new QueuedRandomStringGenerator();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Queue is empty
     */
    public function itWillFailIfTheQueueIsEmpty()
    {
        $this->generator->generate();
    }

    public function itCanBeCleared()
    {
        $this->generator->enqueue('foo');

        $this->generator->clear();

        $this->generator->enqueue('bar');

        $this->assertSame('bar', $this->generator->generate());
    }

    public function getGenerator()
    {
        $this->generator->enqueue('foo');

        return $this->generator;
    }
}
