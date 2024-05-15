<?php

namespace Framework\Filesystem;

use FilesystemIterator;
use Framework\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * The class Filesystem provides utility functions for handling files and directories.
 *
 * @package Framework\Filesystem
 */
class Filesystem
{
    /**
     * Get files from a directory matching a specific extension while ignoring specified extensions.
     *
     * @param string $directory The directory path.
     * @param string|array|null $extension [optional] The extension(s) to filter by. If null, returns all files.
     * @return array<SplFileInfo> An array of files.
     */
    public function files(string $directory, $extension = []): array
    {
        return $this->get_paths($directory, fn(SplFileInfo $file) => $file->isFile() && self::has_extension($file, $extension));
    }

    /**
     * Retrieve all files and directories within a directory.
     *
     * @param string $directory The directory path.
     * @param bool $recursive [optional] Whether to include subdirectories recursively.
     * @return array<SplFileInfo> An array of files and directories.
     */
    public function all_files(string $directory, bool $recursive = true): array
    {
        if ($recursive) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
        } else {
            $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);
        }

        $paths = [];
        foreach ($iterator as $file) {
            $paths[] = $file;
        }

        return $paths;
    }

    /**
     * Write content to a file.
     *
     * @param string $file_path The path to the file.
     * @param string $content The content to write to the file.
     * @return bool true if the content was successfully written to the file, false otherwise.
     */
    public function put(string $file_path, string $content): bool
    {
        return file_put_contents($file_path, $content);
    }

    /**
     * Get directories within a directory.
     *
     * @param string $directory The directory path.
     * @return array An array of directory paths.
     */
    public function directories(string $directory): array
    {
        return $this->get_paths($directory, fn($file) => $file->isDir());
    }

    /**
     * Retrieve paths from a directory based on a callback condition.
     *
     * @param string $directory The directory path.
     * @param callable $callback The callback function defining the condition.
     * @return array<SplFileInfo> An array of files that meet the condition.
     */
    private function get_paths(string $directory, callable $callback): array
    {
        $paths = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($callback($file)) {
                $paths[] = $file;
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
    public function has_extension(SplFileInfo $file, $extension): bool
    {
        if (is_null($extension)) {
            return true;
        }

        $file_extension = pathinfo($file->getPathname(), PATHINFO_EXTENSION);

        return in_array($file_extension, is_array($extension) ? $extension : [$extension], true);
    }

    /**
     * Delete one or multiple files or directories.
     *
     * @param string|array $paths The path(s) to the file(s) or directory(s) to delete.
     * @return bool True if all paths were successfully deleted, false otherwise.
     */
    public function delete($paths): bool
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        $success = true;

        foreach ($paths as $path) {
            if (file_exists($path)) {
                if (is_dir($path)) {
                    $success = $success && $this->delete_directory($path);
                } else {
                    $success = $success && unlink($path);
                }
            } else {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Copy a file or directory to a new location.
     *
     * @param string $source The path to the source file or directory.
     * @param string $destination The path to the destination file or directory.
     * @param bool $overwrite [optional] Whether to overwrite the destination if it already exists.
     * @return bool true on success, false on failure.
     */
    public function copy(string $source, string $destination, bool $overwrite = false): bool
    {
        if (!$this->exists($source)) {
            return false;
        }

        if ($this->exists($destination)) {
            if (!$overwrite) {
                return false;
            }

            $this->delete($destination);
        }

        if ($this->is_directory($source)) {
            $this->make_directory($destination);

            $files = $this->all_files($source);

            foreach ($files as $file) {
                $relative_path = Str::after($file->getPathname(), $source);

                $path = $destination . DIRECTORY_SEPARATOR . $relative_path;

                if ($file->isFile()) {
                    $this->make_directory(dirname($path));

                    if (!copy($file->getPathname(), $path)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return copy($source, $destination);
    }

    /**
     * Check if a path is a directory.
     *
     * @param string $path The path to check.
     * @return bool True if the path is a directory, false otherwise.
     */
    public function is_directory(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Recursively delete a directory.
     *
     * @param string $directory The directory to delete.
     * @return bool True if successfully deleted, false otherwise.
     */
    public function delete_directory(string $directory): bool
    {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($directory);
    }

    /**
     * Get the contents of a file.
     *
     * @param string $file_path The path to the file.
     * @return string|false The contents of the file, or false on failure.
     */
    public function get(string $file_path)
    {
        return file_get_contents($file_path);
    }

    /**
     * Check if a file or directory exists.
     *
     * @param string $path The path to the file or directory.
     * @return bool True if the file or directory exists, false otherwise.
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Create a directory.
     *
     * @param string $directory The directory path to create.
     * @return bool true on success, false on failure.
     */
    public function make_directory(string $directory): bool
    {
        return mkdir($directory, 0777, true);
    }
}