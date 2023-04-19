<?php

namespace Okapi\CodeTransformer\Core\Util;

use Microsoft\PhpParser\DiagnosticsProvider;
use Microsoft\PhpParser\Parser;
use Okapi\CodeTransformer\Core\Exception\Transformer\SyntaxError;

class CodeChecker
{
    /**
     * Check if the given code is valid PHP code.
     *
     * @param string $code
     *
     * @return void
     */
    public function isValidPhpCode(string $code): void
    {
        static $parser;

        if (!isset($parser)) {
            $parser = new Parser;
        }
        $sourceFileNode = $parser->parseSourceFile($code);
        $errors = DiagnosticsProvider::getDiagnostics($sourceFileNode);

        if (count($errors) > 0) {
            $errors = array_reverse($errors);

            // Chain errors
            $error = null;
            foreach ($errors as $e) {
                $error = new SyntaxError($e, $code, $error);
            }

            throw $error;
        }
    }
}
