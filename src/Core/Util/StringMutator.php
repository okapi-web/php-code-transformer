<?php

namespace Okapi\CodeTransformer\Core\Util;

/**
 * A utility class for mutating strings.
 *
 * This class is used to mutate strings in a way that preserves the positions of
 * edits. This is useful for transforming code, where edits to the code may
 * change the positions of other edits.
 */
class StringMutator
{
    /**
     * The string to mutate.
     *
     * @var string
     */
    private string $string;

    /**
     * The edits to apply to the string.
     *
     * @var array{int, int, string}[] Start position, length, replacement
     */
    public array $edits = [];

    /**
     * StringMutator constructor.
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * Add an edit to the string.
     *
     * @param int    $start
     * @param int    $length
     * @param string $replacement
     *
     * @return $this
     */
    public function edit(int $start, int $length, string $replacement): self
    {
        $this->edits[] = [$start, $length, $replacement];
        return $this;
    }

    /**
     * Get the mutated string.
     *
     * @return string
     */
    public function getMutatedString(): string
    {
        // Sort the edits by start position.
        usort($this->edits, function ($a, $b) {
            return $a[0] <=> $b[0];
        });

        // Apply the edits.
        $result = $this->string;
        $offset = 0;
        foreach ($this->edits as $edit) {
            [$start, $length, $replacement] = $edit;
            $start += $offset;
            $result = substr_replace($result, $replacement, $start, $length);
            $offset += strlen($replacement) - $length;
        }
        return $result;
    }
}
