<?php

namespace eLife\IsolatedDrupalBehatExtension\Process;

use eLife\IsolatedDrupalBehatExtension\TestCase;

final class MustRunProcessRunnerTest extends TestCase
{
    /**
     * @test
     */
    public function itRunsTheProcess()
    {
        $processRunner = new MustRunProcessRunner();
        $callback = function () {
        };

        $process = $this->prophesize('Symfony\Component\Process\Process');

        $processRunner->run($process->reveal(), $callback);

        $process->mustRun($callback)->shouldHaveBeenCalledTimes(1);
    }
}
