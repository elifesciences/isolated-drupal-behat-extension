<?php

namespace eLife\IsolatedDrupalBehatExtension\Process;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

interface ProcessRunner
{
    /**
     * @param Process $process
     * @param callable|null $callback
     *
     * @throws ProcessFailedException if the process didn't terminate successfully
     */
    public function run(Process $process, callable $callback = null);
}
