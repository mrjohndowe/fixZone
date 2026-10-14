<?php

namespace App\Helpers;

class CustomHelper
{
    /**
     * Replace placeholders in a string with values from an array.
     *
     * @param  string  $input
     * @param  array   $replacements
     * @return string
     */
    public static function replacePlaceholders($input, $replacements)
    {
        return preg_replace_callback('/\{([^}]+)\}/', function ($matches) use ($replacements) {
            $key = $matches[1];
            return isset($replacements[$key]) ? $replacements[$key] : $matches[0];
        }, $input);
    }

    // Other helper functions can go here
}
