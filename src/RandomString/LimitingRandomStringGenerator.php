<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

final class LimitingRandomStringGenerator implements RandomStringGenerator
{
    /**
     * @var RandomStringGenerator
     */
    private $generator;

    /**
     * @var integer
     */
    private $maxLength;

    /**
     * @param RandomStringGenerator $generator
     * @param integer $maxLength
     */
    public function __construct(RandomStringGenerator $generator, $maxLength)
    {
        $this->generator = $generator;
        $this->maxLength = $maxLength;
    }

    public function generate()
    {
        return substr($this->generator->generate(), 0, $this->maxLength);
    }
}
