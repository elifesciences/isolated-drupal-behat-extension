<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

use RuntimeException;

final class QueuedRandomStringGenerator implements RandomStringGenerator
{
    /**
     * @var string[]
     */
    private $queue = [];

    /**
     * @param string $string
     */
    public function enqueue($string)
    {
        $this->queue[] = $string;
    }

    public function clear()
    {
        $this->queue = [];
    }

    public function generate()
    {
        if (empty($this->queue)) {
            throw new RuntimeException('Queue is empty');
        }

        return array_shift($this->queue);
    }
}
