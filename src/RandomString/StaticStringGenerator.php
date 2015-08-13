<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

final class StaticStringGenerator implements RandomStringGenerator
{
    /**
     * @var string
     */
    private $string;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    public function generate()
    {
        return $this->string;
    }
}
