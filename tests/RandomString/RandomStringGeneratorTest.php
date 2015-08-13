<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

use eLife\IsolatedDrupalBehatExtension\TestCase;

abstract class RandomStringGeneratorTest extends TestCase
{
    /**
     * @test
     */
    final public function itGeneratesAString()
    {
        $string = $this->getGenerator()->generate();

        $this->assertTrue(is_string($string));
        $this->assertNotEmpty(is_string($string));
    }

    /**
     * @return RandomStringGenerator
     */
    abstract protected function getGenerator();
}
