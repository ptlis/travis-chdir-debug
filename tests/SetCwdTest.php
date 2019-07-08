<?php

final class SetCwdTest extends \PHPUnit\Framework\TestCase
{
    private const STDOUT = 1;
    private const STDERR = 2;

    private const BINARY_DIR = './bin-dir';
    private const COMMAND = './test.sh';

    /**
     * Verify that chdir works correctly with shell_exec.
     */
    public function testChdirShellExec()
    {
        $prevCwd = getcwd();
        chdir(self::BINARY_DIR);

        $stdOut = shell_exec(self::COMMAND);

        $this->assertEquals(
            'success',
            trim($stdOut, "\r\n")
        );

        chdir($prevCwd);
    }

    /**
     * Verify that chdir works correctly with proc_open.
     */
    public function testChdirCwdProcOpen()
    {
        $prevCwd = getcwd();
        chdir(self::BINARY_DIR);

        // Setup process with proc_open
        $pipeList = [];
        $process = proc_open(
            self::COMMAND,
            [
                self::STDOUT => ['pipe', 'w'],
                self::STDERR => ['pipe', 'w']
            ],
            $pipeList
        );

        // Poll for completion
        $status = proc_get_status($process);
        $stdOut = '';
        $stdErr = '';
        while ($status['running']) {
            $stdOut .= stream_get_contents($pipeList[self::STDOUT]);
            $stdErr .= stream_get_contents($pipeList[self::STDERR]);
            $status = proc_get_status($process);
        }

        // Ensure that any remaining output is read
        $stdOut .= stream_get_contents($pipeList[self::STDOUT]);
        $stdErr .= stream_get_contents($pipeList[self::STDERR]);

        $this->assertEquals(
            'success',
            trim($stdOut, "\r\n")
        );

        $this->assertEquals(
            '',
            $stdErr
        );

        chdir($prevCwd);
    }
}