<?php

namespace Framework\Support;

/**
 * The UrlParser class parses and extracts information from a given URL string.
 *
 * @package Framework\Support
 */
class UrlParser
{
    /**
     * The URL string to parse.
     *
     * @var string
     */
    protected string $url;

    /**
     * UrlParser constructor.
     *
     * @param string $url The URL string to parse.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get the URL string.
     *
     * @return string The URL string.
     */
    public function get_url(): string
    {
        return $this->url;
    }

    /**
     * Get the scheme of the URL.
     *
     * @return string|null The scheme of the URL, or null if not present.
     */
    public function get_scheme(): ?string
    {
        return parse_url($this->url, PHP_URL_SCHEME);
    }

    /**
     * Get the host of the URL.
     *
     * @return string|null The host of the URL, or null if not present.
     */
    public function get_host(): ?string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Get the path of the URL.
     *
     * @return string|null The path of the URL, or null if not present.
     */
    public function get_path(): ?string
    {
        return parse_url($this->url, PHP_URL_PATH);
    }

    /**
     * Get the query parameters of the URL.
     *
     * @return array The query parameters of the URL.
     */
    public function get_query_parameters(): array
    {
        parse_str(parse_url($this->url, PHP_URL_QUERY), $query_parameters);

        return $query_parameters;
    }

    /**
     * Get the fragment of the URL.
     *
     * @return string|null The fragment of the URL, or null if not present.
     */
    public function get_fragment(): ?string
    {
        return parse_url($this->url, PHP_URL_FRAGMENT);
    }
}
