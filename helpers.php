<?php

/*
|-----------------------------------------------------------------------------
| Application Functions
|-----------------------------------------------------------------------------
|
| This file contains commonly used functions in the application. These
| functions provide utility for our Framework.
|
|-----------------------------------------------------------------------------
*/

use Framework\Component\Application;
use Framework\Component\Config\ConfigRepository;
use Framework\Component\Container;
use Framework\Component\View;
use Framework\Http\HeaderBag;
use Framework\Http\Redirector;
use Framework\Http\RedirectResponse;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Http\Server;
use Framework\Routing\Generator\UrlGenerator;
use Framework\Routing\Router;
use Framework\Session\Session;

if (!function_exists('redirect')) {
    /**
     * Redirect to a specified route.
     *
     * @param string|null $route [optional] The route to redirect to. If null, path of redirect should be set using 'route' method instead.
     * @return RedirectResponse A RedirectResponse instance representing the redirection.
     */
    function redirect(string $route = null): RedirectResponse
    {
        $redirector = Application::get_instance()->get(Redirector::class);

        return is_null($route) ? $redirector->make() : $redirector->to($route);
    }
}

if (!function_exists('response')) {
    /**
     * Helper function to create an instance of the Response class.
     *
     * @param mixed $content The content of the response.
     * @param int $status_code [optional] The HTTP status code of the response. Default is 200 (OK).
     * @param HeaderBag|null $headers [optional] The HeaderBag instance containing HTTP headers (default is an empty HeaderBag).
     * @return Response The created Response instance.
     */
    function response($content = null, int $status_code = Response::HTTP_OK, HeaderBag $headers = null): Response
    {
        return new Response($content, $status_code, $headers ?: new HeaderBag());
    }
}

if (!function_exists('view')) {
    /**
     * Create a new View instance for rendering views.
     *
     * @param string $path The path to the view file.
     * @param array $data [optional] Data to pass to the view.
     * @return View
     */
    function view(string $path, array $data = []): View
    {
        return new View($path, $data);
    }
}

if (!function_exists('resource_path')) {
    /**
     * Get the path to the 'resources' directory.
     *
     * @param string|null $path [optional] Additional path within the 'resources' directory.
     * @return string The absolute path to the 'resources' directory or its subdirectory.
     */
    function resource_path(string $path = null): string
    {
        return Application::get_instance()->base_path('resources/') . ltrim($path, '/');
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the path to the 'public' directory.
     *
     * @param string|null $path [optional] Additional path within the 'public' directory.
     * @return string The absolute path to the 'public' directory or its subdirectory.
     */
    function public_path(string $path = null): string
    {
        return Application::get_instance()->base_path('public/') . ltrim($path, '/');
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the path to the 'storage' directory.
     *
     * @param string|null $path [optional] Additional path within the 'storage' directory.
     * @return string The absolute path to the 'storage' directory or its subdirectory.
     */
    function storage_path(string $path = null): string
    {
        return Application::get_instance()->base_path('storage/') . ltrim($path, '/');
    }
}

if (!function_exists('session')) {
    /**
     * Get or set a session value.
     *
     * If both $key and $value are provided, it sets the session value.
     * If only $key is provided, it retrieves the session value.
     *
     * @template T
     * @param string|null $key [optional] The key of the session value.
     * @param T|null $value [optional] The value to set for the session key.
     * @return Session|T|string
     */
    function session(string $key = null, $value = null)
    {
        $session = Application::get_instance()->get(Session::class);

        if (!is_null($key) && !is_null($value)) {
            $session->put($key, $value);
        }

        if (!is_null($key)) {
            return $session->pull($key);
        }

        return $session;
    }
}

if (!function_exists('error')) {
    /**
     * Get the error associated with the given key from the session.
     *
     * @param string $key The key to retrieve the error.
     * @return string|null The error message or null if not found.
     */
    function error(string $key): ?string
    {
        $errors = Application::get_instance()->get(Session::class)->get('errors.form.' . $key, []);

        return !empty($errors) ? $errors[0] : null;
    }
}

if (!function_exists('route')) {
    /**
     * Get URL for a named route.
     *
     * @param string $name The name of the route.
     * @param array $parameters [optional] Associative array of route parameters.
     * @return string|null The URL for the named route with parameters applied.
     */
    function route(string $name, array $parameters = []): ?string
    {
        return Application::get_instance()->get(Router::class)->route($name, $parameters);
    }
}

if (!function_exists('server')) {
    /**
     * Get server value by key or retrieve the entire Server instance.
     *
     * @param string|null $key The key to retrieve from the server. If null, the entire Server instance is returned.
     * @return mixed|Server If $key is provided, the value associated with that key from the server is returned. If $key is null, the entire Server instance is returned.
     */
    function server(string $key = null)
    {
        $server = Application::get_instance()->get(Server::class);

        return $key ? $server->get($key) : $server;
    }
}

if (!function_exists('request')) {
    /**
     * Get the current request instance.
     *
     * This function provides a convenient way to obtain the current request object
     * throughout the application. It ensures that only a single instance of the
     * Request class is created and reused.
     *
     * @return Request The instance of the Request class.
     */
    function request(): Request
    {
        return Application::get_instance()->get(Request::class);
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve the previous input value for a given key from the session.
     *
     * This function is commonly used in the context of form submissions
     * where validation fails, and you need to repopulate form fields
     * with the previously submitted values.
     *
     * @param string $key The key for which the previous input value should be retrieved.
     * @param string|null $default [optional] The default value if the previous input value cannot be retrieved.
     * @return mixed Returns the previous input value for the specified key or null if not found.
     */
    function old(string $key, ?string $default = null)
    {
        return Application::get_instance()->get(Request::class)->old($key) ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * Get the value of a configuration key or set a configuration value at runtime.
     *
     * If $key is null, it retrieves the entire configuration array. If $key
     * is an array, it sets multiple configuration values at once. If $key is a
     * string, it retrieves the value for the specified key.
     *
     * If $key is null and $default is provided, the default value will be
     * returned if the configuration key is not found.
     *
     * If $key is an array, it sets multiple configuration values at runtime and
     * returns the array of key-value pairs that were set.
     *
     * @template T
     * @param string|array<T>|null $key [optional] The configuration key or an array of key-value pairs to set.
     * @param T $default [optional] The default value to return if the key is not found.
     * @return T|array The value of the configuration key, the entire configuration array, or the default value.
     */
    function config($key = null, $default = null)
    {
        $config = Application::get_instance()->get(ConfigRepository::class);

        if (is_null($key)) {
            return $config->all();
        }

        if (is_array($key)) {
            $config->set($key);
            return $key;
        }

        return $config->get($key, $default);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die function for quick debugging.
     *
     * @param mixed|null $message The variable or message to be dumped.
     * @return void
     */
    function dd(...$message)
    {
        if (!empty($message)) {
            var_dump($message);
        }

        die();
    }
}

if (!function_exists('get')) {
    /**
     * Get an instance of the specified class from the Container.
     *
     * This function acts as a convenient entry point to retrieve instances of
     * classes from the service container. The container serves as a collection of instances of
     * classes used in this application, allowing for reuse and reference to instances without declaring
     * anything on a global scope.
     *
     * If no class name is provided, the function returns the service container itself.
     *
     * @template T
     * @param class-string<T> $abstract The fully qualified class name to resolve.
     * @return T An instance of the specified class.
     *
     * @see Container
     */
    function get(string $abstract)
    {
        return Application::get_instance()->get($abstract);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the absolute path to the Base directory of the application.
     *
     * @param string|null $path [optional] The relative path to append to the Base path.
     * @return string The absolute path to the Base directory of the application.
     */
    function base_path(string $path = null): string
    {
        return Application::get_instance()->base_path($path);
    }
}

if (!function_exists('normalize_path')) {
    /**
     * Normalizes a file path by converting backslashes to slashes.
     *
     * This function replaces all occurrences of backslashes with forward slashes (/)
     * in the given file path string.
     *
     * @param string $input The file path string to normalize.
     * @return string The normalized file path with backslashes converted to slashes.
     */
    function normalize_path(string $input): string
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', $input);
    }
}

if (!function_exists('back')) {
    /**
     * Generate a redirect response back to the previous page.
     *
     * This function creates a redirect response to the URL specified in the 'Referer' header
     * or defaults to the home URL if the 'Referer' header is not present.
     *
     * @return RedirectResponse
     */
    function back(): RedirectResponse
    {
        return Application::get_instance()->get(Redirector::class)->back();
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL based on the given route.
     *
     * @param string|null $path [optional] The path for the URL.
     * @return UrlGenerator|string The generated URL or UrlGenerator if $path is not specified.
     */
    function url(string $path = null)
    {
        $url = Application::get_instance()->get(UrlGenerator::class);

        return $path ? $url->to($path) : $url;
    }
}

if (!function_exists('asset')) {
    /**
     * Generates the URL for an asset based on the provided relative path.
     *
     * @param string $path The relative path to the asset.
     * @return string The full URL for the asset.
     */
    function asset(string $path): string
    {
        return Application::get_instance()->get(UrlGenerator::class)->to('public/') . trim($path, '/');
    }
}

