<?php

// @codeCoverageIgnoreStart
if (!function_exists('str_starts_with_any_but_not')) {
    // @codeCoverageIgnoreEnd
    /**
     * Determines if the given haystack starts with any of the provided needles,
     * but not with any of the provided negative needles.
     *
     * @param string $haystack        The string to search in.
     * @param array  $needles         An array of strings to search for at the
     *                                beginning of the $haystack.
     * @param array  $negativeNeedles An array of strings to ensure are not at
     *                                the beginning of $haystack.
     *
     * @return bool Returns {@link true} if $haystack starts with any string in
     *              $needles, but not with any string in $negativeNeedles.
     *              Returns {@link false} otherwise.
     */
    function str_starts_with_any_but_not(
        string $haystack,
        array  $needles,
        array  $negativeNeedles = [],
    ): bool {
        // Check for negative needles first
        foreach ($negativeNeedles as $negativeNeedle) {
            if (str_starts_with($haystack, $negativeNeedle)) {
                return false;
            }
        }

        // Check for positive needles
        foreach ($needles as $needle) {
            if (str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
