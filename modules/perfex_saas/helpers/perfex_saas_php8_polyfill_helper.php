<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This file contain php8+ polyfill methods
 */

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }
}

if (!function_exists('str_ends_with')) {

    function str_ends_with(string $haystack, string $needle): bool
    {
        if ('' === $needle || $needle === $haystack) {
            return true;
        }

        if ('' === $haystack) {
            return false;
        }

        $needleLength = \strlen($needle);

        return $needleLength <= \strlen($haystack) && 0 === substr_compare($haystack, $needle, -$needleLength);
    }
}

if (!function_exists('str_replace_first')) {
    /**
     * Replace first match of a string
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    function str_replace_first($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);

        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}


if (!function_exists('str_replace_last')) {
    /**
     * Replace last occurrence of a string
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    function str_replace_last($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);

        if ($position !== false) {
            $subject = substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }
}


if (!function_exists('str_ireplace_whole_word')) {
    /**
     * Search and replace whole world in a string. Caseinsensitive
     *
     * @param string $subject
     * @param string $search
     * @param string $replacement
     * @return string
     */
    function str_ireplace_whole_word($search, $replacement, $subject)
    {
        $pattern = '/\b' . preg_quote($search, '/') . '\b/ui'; // Case-insensitive and whole-word matching
        return preg_replace($pattern, $replacement, $subject);
    }
}
