<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

final class DuplicateCheckingRandomStringGeneratorTest extends RandomStringGeneratorTest
{
    /**
     * @var DuplicateCheckingRandomStringGenerator
     */
    private $generator;

    /**
     * @var QueuedRandomStringGenerator
     */
    private $queue;

    public function setUp()
    {
        $this->queue = new QueuedRandomStringGenerator();

        $this->queue->enqueue('foo');

        $this->generator = new DuplicateCheckingRandomStringGenerator($this->queue);
    }

    /**
     * @test
     */
    public function itWillNotGenerateTheSameValueTwice()
    {
        $this->queue->clear();

        $this->queue->enqueue('foo');
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');

        $this->assertSame('foo', $this->generator->generate());
        $this->assertSame('bar', $this->generator->generate());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to generate a string, tried 5 times
     */
    public function itWillFailOnTooManyAttempts()
    {
        $static = new StaticStringGenerator('foo');
        $generator = new DuplicateCheckingRandomStringGenerator($static, 5);

        $generator->generate();
        $generator->generate();
    }

    public function getGenerator()
    {
        return $this->generator;
    }
}
