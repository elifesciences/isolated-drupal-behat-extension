<?php

namespace eLife\IsolatedDrupalBehatExtension\Process;

use Symfony\Component\Process\Process;

final class MustRunProcessRunner implements ProcessRunner
{
    public function run(Process $process, callable $callback = null)
    {
        $process->mustRun($callback);
    }
}
