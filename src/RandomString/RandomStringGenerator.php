<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

interface RandomStringGenerator
{
    /**
     * @return string
     */
    public function generate();
}
