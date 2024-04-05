<?php

namespace Framework\Http;

/**
 * The UploadedFile class represents a file uploaded through an HTTP request.
 *
 * This class provides methods to interact with the uploaded file.
 *
 * @package Framework\Http
 */
class UploadedFile
{
    /**
     * The temporary file path.
     *
     * @var string
     */
    protected string $path;

    /**
     * The original name of the file.
     *
     * @var string
     */
    protected string $name;

    /**
     * The size of the file in bytes.
     *
     * @var int|null
     */
    protected ?int $size;

    /**
     * The MIME type of the file.
     *
     * @var string|null
     */
    protected ?string $type;

    /**
     * The error code of the file upload.
     *
     * @var int|null
     */
    protected ?int $error;

    /**
     * Create a new uploaded file instance.
     *
     * @param string $path The temporary file path.
     * @param string $name The original name of the file.
     * @param string|null $type [optional] The MIME type of the file.
     * @param int|null $size [optional] The size of the file in bytes.
     * @param int|null $error [optional] The error code of the file upload.
     */
    public function __construct(string $path, string $name, string $type = null, int $size = null, int $error = null)
    {
        $this->path = $path;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->error = $error;
    }

    /**
     * Get the path to the file.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get the original name of the file.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the MIME type of the file.
     *
     * @return string|null
     */
    public function get_mime_type(): ?string
    {
        return $this->type;
    }

    /**
     * Get the size of the file in bytes.
     *
     * @return int|null
     */
    public function size(): ?int
    {
        return $this->size;
    }

    /**
     * Check if the file upload was successful and the file is valid.
     *
     * @return bool
     */
    public function is_valid(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    /**
     * Retrieve the contents of the uploaded file.
     *
     * @return string
     */
    public function get_contents(): string
    {
        return file_get_contents($this->path);
    }

    /**
     * Move the uploaded file to a new location.
     *
     * @param string $path The path to move the file to.
     * @return bool
     */
    public function move(string $path): bool
    {
        return move_uploaded_file($this->path, $path);
    }

    /**
     * Get the error code associated with the uploaded file.
     *
     * @return int|null
     */
    public function get_error(): ?int
    {
        return $this->error;
    }

    /**
     * Retrieve the filename without the extension.
     *
     * @return string The filename without the extension.
     */
    public function get_basename(): string
    {
        return pathinfo($this->name, PATHINFO_FILENAME);
    }
}