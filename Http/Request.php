<?php

namespace Framework\Http;

use Framework\Component\Exceptions\ValidationException;
use Framework\Component\Validation\Validator;
use Framework\Session\Session;
use Framework\Support\Collection;
use JsonException;

/**
 * The Request class represents an HTTP request entity and provides methods to work with query parameters.
 *
 * This class provides methods to easier retrieve query parameters from a URL and provides
 * methods for handling form data, validation, and accessing request-related information.
 *
 * @package Framework\Http
 */
class Request
{
    /**
     * Server instance.
     *
     * @var Server
     */
    private Server $server;

    /**
     * Header instance.
     *
     * @var HeaderBag
     */
    private HeaderBag $headers;

    /**
     * Session instance.
     *
     * @var Session
     */
    public Session $session;

    /**
     * Request body.
     *
     * @var string
     */
    protected string $content;

    /**
     * Request constructor.
     *
     * @param Server $server The Server instance.
     */
    public function __construct(Server $server, Session $session)
    {
        $this->server = $server;
        $this->session = $session;
    }

    /**
     * Retrieve the value of a query parameter.
     *
     * @param string $parameter The name of the query parameter.
     * @param string|null $default [optional] The default value if the parameter is not set.
     * @return string|null The value of the query parameter or the default value.
     */
    public function get(string $parameter, ?string $default = null): ?string
    {
        return $_GET[$parameter] ?? $default;
    }

    /**
     * Retrieve the value of a form post data parameter.
     *
     * @param string $parameter The name of the form post data parameter.
     * @param string|null $default [optional] The default value if the parameter is not set.
     * @return string|null The value of the form post data parameter or the default value.
     */
    public function input(string $parameter, ?string $default = null): ?string
    {
        return $_POST[$parameter] ?? $default;
    }

    /**
     * Checks if the form input exists in the POST request.
     *
     * @param string $parameter The name of the form post data parameter.
     * @return bool
     */
    public function exists(string $parameter): bool
    {
        return isset($_POST[$parameter]);
    }

    /**
     * Retrieve all form post data as an associative array.
     *
     * @return array The associative array of form post data.
     */
    public function all(): array
    {
        return $_POST;
    }

    /**
     * Retrieve an uploaded file.
     *
     * @param string $key The name of the file input field.
     * @param mixed $default [optional] The default value if the file is not uploaded.
     * @return UploadedFile|mixed The file object or null if the file is not uploaded.
     */
    public function file(string $key, $default = null)
    {
        if (!isset($_FILES[$key])) {
            return $default;
        }

        $file = $_FILES[$key];

        return new UploadedFile($file['tmp_name'], $file['name'], $file['type'] ?? null, $file['size'] ?? null, $file['error'] ?? null);
    }

    /**
     * Validate multiple parameters based on the given validation patterns.
     *
     * @param array $rules An associative array where keys are parameter names and values are validation patterns (e.g. ['name' => 'required|string|max:255']).
     * @return Validator The Validator instance.
     */
    public function validate(array $rules): Validator
    {
        $validator = new Validator($this->all(), $rules);

        if ($validator->validate()) {
            throw new ValidationException($validator->errors()->all());
        }

        return $validator;
    }

    /**
     * Retrieve a server variable from the request.
     *
     * @param string $key The key of the server variable to retrieve.
     * @return mixed|null The value of the server variable if found, null otherwise.
     */
    public function server(string $key)
    {
        return $this->server->get($key);
    }

    /**
     * Flash the current request data into the session and return the session instance.
     *
     * @return Session The session instance with flashed data.
     */
    public function flash(): Session
    {
        return $this->session->flash('form', $_POST);
    }

    /**
     * Retrieve the old input data from the previous request.
     *
     * @param string $key The key to retrieve old input data.
     * @return mixed|null The old input data or null if not found.
     */
    public function old(string $key)
    {
        return $this->session->get('flash.' . $key);
    }

    /**
     * Get the HTTP method of the request.
     *
     * @return string The HTTP method (GET, POST, PUT, DELETE, etc.).
     */
    public function method(): string
    {
        return strtoupper($this->server->get('REQUEST_METHOD'));
    }

    /**
     * Get the full request URI including query parameters.
     *
     * @return string|null The full request URI.
     */
    public function request_uri(): ?string
    {
        return $this->server->get('REQUEST_URI');
    }

    /**
     * Get the request scheme (HTTP or HTTPS).
     *
     * @return string The request scheme.
     */
    public function scheme(): string
    {
        return $this->server->get('REQUEST_SCHEME') ?? 'http';
    }

    /**
     * Get the HTTP host from the request headers. If not available, fallback to the server IP address.
     *
     * @return string The HTTP host or the server IP address.
     */
    public function host(): string
    {
        return $this->server->get('HTTP_HOST') ?? $this->server->get('SERVER_ADDR');
    }

    /**
     * Get the root URL of the application.
     *
     * @return string The root URL.
     */
    public function root(): string
    {
        return ($this->is_secure() ? 'https' : 'http') . '://' . $this->host() . '/';
    }

    /**
     * Determine if the request is served over HTTPS.
     *
     * @return bool
     */
    public function is_secure(): bool
    {
        return $this->server->get('HTTPS') === 'on';
    }

    /**
     * Get the path component of the request URI.
     *
     * @return string|null The path component of the request URI.
     */
    public function path(): ?string
    {
        return parse_url($this->request_uri(), PHP_URL_PATH);
    }

    /**
     * Get the HTTP headers from the request.
     *
     * @return HeaderBag The HTTP headers.
     */
    public function headers(): HeaderBag
    {
        if (!isset($this->headers)) {
            $this->headers = new HeaderBag(array_change_key_case(getallheaders()));
        }

        return $this->headers;
    }

    /**
     * Get the raw body content of the request.
     *
     * @return string The request body content
     */
    public function content(): string
    {
        if (!isset($this->content) && !in_array($this->method(), ['GET', 'HEAD'])) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * Get the parsed JSON content of the request body.
     *
     * @return Collection The parsed JSON content
     */
    public function json(): Collection
    {
        try {
            $array = json_decode($this->content(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $array = [];
        }

        return new Collection($array);
    }

    /**
     * Determine if the request expects a JSON response.
     *
     * @return bool true if the request expects a JSON response, false otherwise.
     */
    public function expects_json(): bool
    {
        $accept_header = $this->headers()->get('Accept');

        if (is_string($accept_header) && strpos($accept_header, '/json') !== false) {
            return true;
        }

        if (is_array($accept_header)) {
            foreach ($accept_header as $type) {
                if (is_string($type) && strpos($type, '/json') !== false) {
                    return true;
                }
            }
        }

        return false;
    }
}