<?php

namespace Framework\Http;

use Framework\Routing\Generator\UrlGenerator;
use Framework\Session\Session;

/**
 * The Redirector class provides methods to redirect users to specific routes or URLs, creating redirects with flash data,
 * redirect back to the previous page, and generate JSON responses.
 *
 * @package Framework\Http
 */
class Redirector
{
    /**
     * The session manager for storing flash data.
     *
     * @var Session
     */
    protected Session $session;

    /**
     * UrlGenerator instance.
     *
     * @var UrlGenerator
     */
    private UrlGenerator $url;

    /**
     * Redirect constructor.
     *
     * @param Session $session The session manager for storing flash data.
     */
    public function __construct(Session $session, UrlGenerator $url)
    {
        $this->session = $session;
        $this->url = $url;
    }

    /**
     * Redirect to a specified route or URL.
     *
     * @param string $path The path or URL to redirect to.
     * @return RedirectResponse The redirect response object.
     */
    public function to(string $path): RedirectResponse
    {
        return $this->make()->route($path);
    }

    /**
     * Redirect back to the previous page or the base URL if no referer is provided.
     *
     * This method is commonly used to redirect users back to the previous page.
     * It retrieves the URL from the 'Referer' header in the HTTP request headers.
     * If the 'Referer' header is not present, it defaults to the base URL.
     *
     * @return RedirectResponse The redirect response object.
     */
    public function back(): RedirectResponse
    {
        return $this->make()->back();
    }

    /**
     * Return a JsonResponse object with the provided data.
     *
     * @param array $data The data to be included in the JSON response.
     * @return JsonResponse The JSON response object.
     */
    public function json(array $data): JsonResponse
    {
        return new JsonResponse($data);
    }

    /**
     * Make a new RedirectResponse instance with the request and session already set.
     *
     * @return RedirectResponse The redirect response object.
     */
    public function make(): RedirectResponse
    {
        $response = new RedirectResponse();

        $response
            ->set_request($this->url->get_request())
            ->set_session($this->session);

        return $response;
    }
}