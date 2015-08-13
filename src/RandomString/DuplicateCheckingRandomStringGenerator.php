<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

use RuntimeException;

final class DuplicateCheckingRandomStringGenerator implements RandomStringGenerator
{
    /**
     * @var RandomStringGenerator
     */
    private $generator;

    /**
     * @var string[]
     */
    private $randomStrings = [];

    /**
     * @var integer
     */
    private $maxAttempts;

    /**
     * @param RandomStringGenerator $generator
     * @param integer $maxAttempts
     */
    public function __construct(
        RandomStringGenerator $generator,
        $maxAttempts = 10
    ) {
        $this->generator = $generator;
        $this->maxAttempts = $maxAttempts;
    }

    public function generate()
    {
        $i = 0;
        while (in_array(
            $generated = $this->generator->generate(),
            $this->randomStrings
        )) {
            if ($i > ($this->maxAttempts - 1)) {
                throw new RuntimeException('Unable to generate a string, tried ' . $i . ' times');
            }
            // Try again.
            $i++;
        }

        $this->randomStrings[] = $generated;

        return $generated;
    }
}
