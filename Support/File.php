<?php

namespace Framework\Support;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class File
 *
 * Provides utility functions for handling files and directories.
 */
class File
{
    /**
     * Get files from a directory matching a specific extension.
     *
     * @param string $directory The directory path.
     * @param string|array|null $extension The extension to filter by. If null, returns all files.
     * @return array An array of file paths.
     */
    public static function files(string $directory, $extension = null): array
    {
        return self::get_paths($directory, fn($file) => $file->isFile() && self::has_extension($file, $extension));
    }

    /**
     * Get directories within a directory.
     *
     * @param string $directory The directory path.
     * @return array An array of directory paths.
     */
    public static function directories(string $directory): array
    {
        return self::get_paths($directory, fn($file) => $file->isDir());
    }

    /**
     * Get all files from a directory, including subdirectories.
     *
     * @param string $directory The directory path.
     * @param string|array|null $extension The extension to filter by. If null, returns all files.
     * @return array An array of file paths.
     */
    public static function all_files(string $directory, $extension = null): array
    {
        return self::get_paths($directory, fn($file) => $file->isFile() && self::has_extension($file, $extension));
    }

    /**
     * Retrieve paths from a directory based on a callback condition.
     *
     * @param string $directory The directory path.
     * @param callable $callback The callback function defining the condition.
     * @return array An array of paths that meet the condition.
     */
    private static function get_paths(string $directory, callable $callback): array
    {
        $paths = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($callback($file)) {
                $paths[] = $file->getPathname();
            }
        }

        return $paths;
    }

    /**
     * Check if a file has a specific extension.
     *
     * @param SplFileInfo $file The file object.
     * @param string|array $extension The extension(s) to check against.
     * @return bool True if the file has the specified extension, false otherwise.
     */
    private static function has_extension(SplFileInfo $file, $extension): bool
    {
        if (is_null($extension)) {
            return true;
        }

        $file_extension = pathinfo($file->getPathname(), PATHINFO_EXTENSION);

        return in_array($file_extension, is_array($extension) ? $extension : [$extension]);
    }
}
