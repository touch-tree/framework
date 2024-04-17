<?php

namespace Framework\Console;

use RuntimeException;

/**
 * The Process class is a simple wrapper around proc_open to execute external processes and capture their output.
 *
 * @package Framework\Console
 */
class Process
{
    /**
     * Command to execute.
     *
     * @var array
     */
    protected array $command;

    /**
     * Output of the process.
     *
     * @var string|null
     */
    protected ?string $output;

    /**
     * Error output of the process.
     *
     * @var string|null
     */
    protected ?string $error_output;

    /**
     * Exit code of the process.
     *
     * @var int|null
     */
    protected ?int $exit_code;

    /**
     * Timeout for the process in seconds.
     *
     * @var float|null
     */
    protected $timeout;

    /**
     * Additional options for the process.
     *
     * @var array
     */
    protected array $options;

    /**
     * Process constructor.
     *
     * @param array $command Command to execute.
     * @param float|null $timeout [optional] Timeout for the process in seconds (default is 60).
     * @param array $options [optional] Additional options for the process.
     */
    public function __construct(array $command, float $timeout = 60, array $options = [])
    {
        $this->command = $command;
        $this->timeout = $timeout;
        $this->options = $options;
    }

    /**
     * Runs the process.
     *
     * @return Process
     *
     * @throws RuntimeException If unable to open process.
     */
    public function run(): Process
    {
        $command = implode(' ', $this->command);

        $process = proc_open($command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );

        if (!is_resource($process)) {
            throw new RuntimeException('Unable to open the process for command: ' . $command);
        }

        $this->output = stream_get_contents($pipes[1]);
        $this->error_output = stream_get_contents($pipes[2]);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $this->exit_code = proc_close($process);

        return $this;
    }

    /**
     * Checks if the process ran successfully.
     *
     * @return bool true if successful, false otherwise.
     */
    public function is_successful(): bool
    {
        return (bool)$this->exit_code;
    }

    /**
     * Gets the output of the process.
     *
     * @return string|null Output of the process.
     */
    public function get_output(): ?string
    {
        return $this->output;
    }

    /**
     * Gets the error output of the process.
     *
     * @return string|null Error output of the process.
     */
    public function get_error_output(): ?string
    {
        return $this->error_output;
    }

    /**
     * Gets the exit code of the process.
     *
     * @return int|null Exit code of the process.
     */
    public function get_exit_code(): ?int
    {
        return $this->exit_code;
    }
}