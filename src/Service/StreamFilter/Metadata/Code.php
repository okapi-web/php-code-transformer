<?php

namespace Okapi\CodeTransformer\Service\StreamFilter\Metadata;

use Microsoft\PhpParser\DiagnosticsProvider;
use Microsoft\PhpParser\Node\SourceFileNode;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\Token;
use Okapi\CodeTransformer\Exception\Transformer\SyntaxError;
use Okapi\CodeTransformer\Util\StringMutator;

/**
 * # Code
 *
 * The `Code` class is used to store the source code of the stream filter
 * and provide a convenient interface for manipulating it.
 */
class Code
{
    /**
     * String mutator.
     *
     * @var StringMutator
     */
    private StringMutator $stringMutator;

    /**
     * List of strings to be appended to the source code.
     *
     * @var array
     */
    private array $appendList = [];

    /**
     * The source file node.
     *
     * @var SourceFileNode
     */
    public SourceFileNode $sourceFileNode;

    /**
     * Code constructor.
     *
     * @param string $source The source code.
     */
    public function __construct(
        private readonly string $source,
    ) {
        $parser               = new Parser;
        $this->sourceFileNode = $parser->parseSourceFile($this->source);
        $this->stringMutator  = new StringMutator($this->source);
    }

    /**
     * Add an edit to the source code.
     *
     * @param Token  $token       The token to edit.
     * @param string $replacement The replacement string.
     *
     * @return void
     */
    public function edit(Token $token, string $replacement): void
    {
        $this->stringMutator->edit(
            $token->getStartPosition(),
            $token->getWidth(),
            $replacement,
        );
    }

    /**
     * Add a string to the end of the source code.
     *
     * @param string $string
     *
     * @return void
     */
    public function append(string $string): void
    {
        $this->appendList[] = $string;
    }

    /**
     * Get the original source code.
     *
     * @return string
     */
    public function getOriginalSource(): string
    {
        return $this->source;
    }

    /**
     * Get the new source code with all edits and appends applied.
     *
     * @return string
     *
     * @internal
     */
    public function getNewSource(): string
    {
        // If there are no edits or appends, return the original source
        if (!$this->hasChanges()) {
            // @codeCoverageIgnoreStart
            // This is actually being run, but it's not being counted for some reason
            return $this->source;
            // @codeCoverageIgnoreEnd
        }

        // Apply edits
        $newSource = $this->stringMutator->getMutatedString();

        // Append strings
        foreach ($this->appendList as $append) {
            $newSource .= $append;
        }

        // Check the new source code for syntax errors
        $parser         = new Parser;
        $sourceFileNode = $parser->parseSourceFile($newSource);
        $errors         = DiagnosticsProvider::getDiagnostics($sourceFileNode);

        if (count($errors) > 0) {
            $errors = array_reverse($errors);

            // Chain errors
            $error = null;
            foreach ($errors as $e) {
                $error = new SyntaxError($e, $newSource, $error);
            }

            throw $error;
        }

        return $newSource;
    }

    /**
     * Whether the source code has any edits or appends.
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        return count($this->stringMutator->edits) > 0 || count($this->appendList) > 0;
    }

    /**
     * Get the full class name.
     *
     * @return string
     */
    public function getFullClassName(): string
    {
        /** @var ClassDeclaration $classDeclaration */
        $classDeclaration = $this->sourceFileNode->getFirstDescendantNode(ClassDeclaration::class);

        return $classDeclaration->getNamespacedName()->getFullyQualifiedNameText();
    }
}
