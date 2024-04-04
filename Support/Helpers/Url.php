<?php

namespace Framework\Support\Helpers;

use Framework\Routing\Generator\UrlGenerator;

/**
 * Url facade.
 *
 * @package Framework\Support\Helpers
 * @see UrlGenerator
 */
class Url extends Facade
{
    /**
     * Set the accessor for the facade.
     *
     * @return string
     */
    static protected function accessor(): string
    {
        return UrlGenerator::class;
    }
}