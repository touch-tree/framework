<?php

namespace Framework\Support;

class Console
{
    /**
     * Call a command using exec.
     *
     * @param string $command The command to execute.
     * @param array $parameters The parameters for the command.
     * @return string
     */
    public static function call(string $command, array $parameters = []): string
    {
        $full_command = implode(chr(32),
            [
                $command,
                ...$parameters
            ]
        );

        exec($full_command, $out);

        return implode(PHP_EOL, $out);
    }
}