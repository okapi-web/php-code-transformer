<?php

namespace Okapi\CodeTransformer\Util;

use Okapi\Wildcards\Regex;

/**
 * # Finder
 *
 * The `Finder` class is used to match classes against a list of patterns.
 */
class Finder
{
    /**
     * The classes that should be included in the search.
     *
     * @var string[] List with regex patterns
     */
    private array $classesToMatch = [];

    /**
     * Include a class or an array of classes in the search.
     * Can also be a wildcard pattern.
     *
     * @param class-string|class-string[] $path
     *
     * @return $this
     */
    public function includeClass(string|array $path): self
    {
        $path = (array)$path;

        // Convert wildcard patterns to regex
        foreach ($path as &$class) {
            $class = Regex::fromWildcard($class)->getRegex();
        }

        $this->classesToMatch = array_merge(
            $this->classesToMatch,
            $path,
        );

        return $this;
    }

    /**
     * Check if a class exists in the search paths.
     *
     * @param class-string $class
     *
     * @return bool
     */
    public function hasClass(string $class): bool
    {
        // Check if the class matches any of the patterns
        return array_reduce(
            $this->classesToMatch,
            function (bool $carry, string $pattern) use ($class): bool {
                return $carry || preg_match($pattern, $class) === 1;
            },
            false,
        );
    }
}
