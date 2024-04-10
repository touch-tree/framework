<?php

namespace Framework\Support\Helpers;

use Framework\Component\Exceptions\BindingResolutionException;
use Framework\Filesystem\Filesystem;
use SplFileInfo;

/**
 * File facade.
 *
 * @package Framework\Support\Helpers
 * @see Filesystem
 */
class File extends Facade
{
    /**
     * Set the accessor for the facade.
     *
     * @return string
     */
    protected static function accessor(): string
    {
        return Filesystem::class;
    }

    /**
     * Get files from a directory matching a specific extension.
     *
     * @param string $directory The directory path.
     * @param string|array|null $extension The extension to filter by. If null, returns all files.
     * @return array An array of file paths.
     */
    public static function files(string $directory, $extension = null): array
    {
        return self::get_accessor_class()->files($directory, $extension);
    }

    /**
     * Retrieve all files and directories within a directory.
     *
     * @param string $directory The directory path.
     * @param bool $recursive [optional] Whether to include subdirectories recursively.
     * @return array An array containing the paths of files and directories.
     */
    public static function all_files(string $directory, bool $recursive = true): array
    {
        return self::get_accessor_class()->all_files($directory, $recursive);
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
        return self::get_accessor_class()->put($file_path, $content);
    }

    /**
     * Get directories within a directory.
     *
     * @param string $directory The directory path.
     * @return array An array of directory paths.
     */
    public static function directories(string $directory): array
    {
        return self::get_accessor_class()->directories($directory);
    }

    /**
     * Get the contents of a file.
     *
     * @param string $file_path The path to the file.
     * @return string|false The contents of the file, or false on failure.
     */
    public static function get(string $file_path)
    {
        return self::get_accessor_class()->get($file_path);
    }

    /**
     * Check if a file or directory exists.
     *
     * @param string $path The path to the file or directory.
     * @return bool true if the file or directory exists, false otherwise.
     */
    public static function exists(string $path): bool
    {
        return self::get_accessor_class()->exists($path);
    }

    /**
     * Create a directory.
     *
     * @param string $directory The directory path to create.
     * @return bool true on success, false on failure.
     */
    public static function make_directory(string $directory): bool
    {
        return self::get_accessor_class()->make_directory($directory);
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
        return self::get_accessor_class()->get_paths($directory, $callback);
    }

    /**
     * Check if a file has a specific extension.
     *
     * @param SplFileInfo $file The file object.
     * @param string|array $extension The extension(s) to check against.
     * @return bool true if the file has the specified extension, false otherwise.
     */
    private static function has_extension(SplFileInfo $file, $extension): bool
    {
        return self::get_accessor_class()->has_extension($file, $extension);
    }

    /**
     * Delete one or multiple files or directories.
     *
     * @param string|array $paths The path(s) to the file(s) or directory(s) to delete.
     * @return bool true if all paths were successfully deleted, false otherwise.
     */
    public static function delete($paths): bool
    {
        return self::get_accessor_class()->delete($paths);
    }

    /**
     * Recursively delete a directory.
     *
     * @param string $directory The directory to delete.
     * @return bool true if successfully deleted, false otherwise.
     */
    private static function delete_directory(string $directory): bool
    {
        return self::get_accessor_class()->delete_directory($directory);
    }
}