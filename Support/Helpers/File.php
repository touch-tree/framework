<?php

namespace Framework\Support\Helpers;

use Framework\Filesystem\Filesystem;
use SplFileInfo;

/**
 * File helper.
 *
 * @package Framework\Support\Helpers
 * @see Filesystem
 */
class File extends Helper
{
    /**
     * Set the accessor for the facade.
     *
     * @return Filesystem
     */
    protected static function accessor(): object
    {
        return get(Filesystem::class);
    }

    /**
     * Get files from a directory matching a specific extension.
     *
     * @param string $directory The directory path.
     * @param string|array|null $extension [optional] The extension(s) to filter by. If null, returns all files.
     * @return array<SplFileInfo> An array of files.
     */
    public static function files(string $directory, $extension = []): array
    {
        return self::accessor()->files($directory, $extension);
    }

    /**
     * Retrieve all files and directories within a directory.
     *
     * @param string $directory The directory path.
     * @param bool $recursive [optional] Whether to include subdirectories recursively.
     * @return array<SplFileInfo> An array of files and directories.
     */
    public static function all_files(string $directory, bool $recursive = true): array
    {
        return self::accessor()->all_files($directory, $recursive);
    }

    /**
     * Write content to a file.
     *
     * @param string $file_path The path to the file.
     * @param string $content The content to write to the file.
     * @return bool true if the content was successfully written to the file, false otherwise.
     */
    public static function put(string $file_path, string $content): bool
    {
        return self::accessor()->put($file_path, $content);
    }

    /**
     * Get directories within a directory.
     *
     * @param string $directory The directory path.
     * @return array An array of directory paths.
     */
    public static function directories(string $directory): array
    {
        return self::accessor()->directories($directory);
    }

    /**
     * Get the contents of a file.
     *
     * @param string $file_path The path to the file.
     * @return string|false The contents of the file, or false on failure.
     */
    public static function get(string $file_path)
    {
        return self::accessor()->get($file_path);
    }

    /**
     * Check if a file or directory exists.
     *
     * @param string $path The path to the file or directory.
     * @return bool true if the file or directory exists, false otherwise.
     */
    public static function exists(string $path): bool
    {
        return self::accessor()->exists($path);
    }

    /**
     * Create a directory.
     *
     * @param string $directory The directory path to create.
     * @return bool true on success, false on failure.
     */
    public static function make_directory(string $directory): bool
    {
        return self::accessor()->make_directory($directory);
    }

    /**
     * Check if a file has a specific extension.
     *
     * @param SplFileInfo $file The file object.
     * @param string|array $extension The extension(s) to check against.
     * @return bool true if the file has the specified extension, false otherwise.
     */
    public static function has_extension(SplFileInfo $file, $extension): bool
    {
        return self::accessor()->has_extension($file, $extension);
    }

    /**
     * Delete one or multiple files or directories.
     *
     * @param string|array $paths The path(s) to the file(s) or directory(s) to delete.
     * @return bool true if all paths were successfully deleted, false otherwise.
     */
    public static function delete($paths): bool
    {
        return self::accessor()->delete($paths);
    }

    /**
     * Recursively delete a directory.
     *
     * @param string $directory The directory to delete.
     * @return bool true if successfully deleted, false otherwise.
     */
    public static function delete_directory(string $directory): bool
    {
        return self::accessor()->delete_directory($directory);
    }
}