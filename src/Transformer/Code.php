<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Transformer;

use DI\Attribute\Inject;
use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Node\SourceFileNode;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\Token;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Core\Util\CodeChecker;
use Okapi\CodeTransformer\Core\Util\ReflectionHelper;
use Okapi\CodeTransformer\Core\Util\StringMutator;
use Roave\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;

/**
 * # Code
 *
 * This class is used to store the source code of the stream filter
 * and provide a convenient interface for manipulating it.
 */
class Code
{
    // region DI

    #[Inject]
    private ReflectionHelper $reflectionHelper;

    // endregion

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
    private SourceFileNode $sourceFileNode;

    /**
     * Code constructor.
     *
     * @param string       $source          The source code.
     * @param class-string $namespacedClass The namespaced class name.
     */
    public function __construct(
        private readonly string $source,
        private readonly string $namespacedClass,
    ) {
        // Create the string mutator
        $this->stringMutator = DI::make(StringMutator::class, [
            'string' => $this->source,
        ]);
    }

    // region Mutators

    /**
     * Add an edit to the source code.
     *
     * @param Token|Node $token       The token or node to edit.
     * @param string     $replacement The replacement string.
     *
     * @return void
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function edit(Token|Node $token, string $replacement): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->stringMutator->edit(
            $token->getStartPosition(),
            $token->getWidth(),
            $replacement,
        );
    }

    /**
     * Add an edit to the source code at a specific position.
     *
     * @param int    $startPosition
     * @param int    $width
     * @param string $replacement
     *
     * @return void
     */
    public function editAt(
        int    $startPosition,
        int    $width,
        string $replacement,
    ): void {
        $this->stringMutator->edit(
            $startPosition,
            $width,
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

    // endregion

    // region Getters

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
     * Get source file node.
     *
     * @return SourceFileNode
     */
    public function getSourceFileNode(): SourceFileNode
    {
        if (!isset($this->sourceFileNode)) {
            $this->sourceFileNode = (new Parser)->parseSourceFile($this->source);
        }

        return $this->sourceFileNode;
    }

    /**
     * Get the reflection class.
     *
     * @return BetterReflectionClass
     */
    public function getReflectionClass(): BetterReflectionClass
    {
        return $this->reflectionHelper->getReflectionClass(
            $this->getNamespacedClass(),
        );
    }

    /**
     * Get the new source code with all edits and appends applied.
     *
     * @return string
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
        DI::get(CodeChecker::class)->isValidPhpCode($newSource);

        return $newSource;
    }

    // endregion

    /**
     * Whether the source code has any edits or appends.
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        return count($this->stringMutator->edits) > 0
            || count($this->appendList) > 0;
    }

    /**
     * Get the namespaced class name.
     *
     * @return class-string
     */
    public function getNamespacedClass(): string
    {
        return $this->namespacedClass;
    }

    /**
     * Get the class name.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return substr(
            $this->namespacedClass,
            strrpos($this->namespacedClass, '\\') + 1,
        );
    }
}
