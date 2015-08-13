<?php

namespace eLife\IsolatedDrupalBehatExtension;

use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    final protected function generateDrupal()
    {
        return new Drupal('/foo/bar', 'http://localhost/', 'standard');
    }
}
