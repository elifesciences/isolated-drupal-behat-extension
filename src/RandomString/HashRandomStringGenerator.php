<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

final class HashRandomStringGenerator implements RandomStringGenerator
{
    /**
     * @var string
     */
    private $algorithm;

    /**
     * @param string $algorithm
     */
    public function __construct($algorithm = 'crc32')
    {
        $this->algorithm = $algorithm;
    }

    public function generate()
    {
        return hash($this->algorithm, uniqid('', true));
    }
}
