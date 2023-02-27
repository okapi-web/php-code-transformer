<!--suppress HtmlDeprecatedAttribute -->
<h1 align="center">PHP Code Transformer</h1>

<!-- Main Badges -->
<p align="center">
  <!-- License: MIT -->
  <a href="https://opensource.org/licenses/MIT" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-9C0000.svg?labelColor=ebdbb2&style=flat&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNCIgaGVpZ2h0PSIxNCI+PHBhdGggdmVjdG9yLWVmZmVjdD0ibm9uLXNjYWxpbmctc3Ryb2tlIiBkPSJNMCAyLjk5NWgxLjI4djguMDFIMHpNMi41NCAzaDEuMjh2NS4zNEgyLjU0em0yLjU1LS4wMDVoMS4yOHY4LjAxSDUuMDl6bTIuNTQuMDA3aDEuMjh2MS4zMzZINy42M3oiIGZpbGw9IiM5YzAwMDAiLz48cGF0aCB2ZWN0b3ItZWZmZWN0PSJub24tc2NhbGluZy1zdHJva2UiIGQ9Ik03LjYzIDUuNjZoMS4yOFYxMUg3LjYzeiIgZmlsbD0iIzdjN2Q3ZSIvPjxwYXRoIHZlY3Rvci1lZmZlY3Q9Im5vbi1zY2FsaW5nLXN0cm9rZSIgZD0iTTEwLjE3NyAzLjAwMmgzLjgyNnYxLjMzNmgtMy44MjZ6bS4wMDMgMi42NThoMS4yOFYxMWgtMS4yOHoiIGZpbGw9IiM5YzAwMDAiLz48L3N2Zz4="/>
  </a>

  <!-- Twitter: @WalterWoshid -->
  <a href="https://twitter.com/WalterWoshid" target="_blank">
    <img alt="Twitter: @WalterWoshid" src="https://img.shields.io/badge/@WalterWoshid-Twitter?labelColor=ebdbb2&style=flat&logo=twitter&logoColor=458588&color=458588&label=Twitter"/>
  </a>

  <!-- PHP: >=8.1 -->
  <a href="https://www.php.net" target="_blank">
    <img alt="PHP: >=8.1" src="https://img.shields.io/badge/PHP->=8.1-4C5789.svg?labelColor=ebdbb2&style=flat&logo=php&logoColor=4C5789"/>
  </a>

  <!-- Packagist -->
  <a href="https://packagist.org/packages/okapi/code-transformer" target="_blank">
    <img alt="Packagist" src="https://img.shields.io/packagist/v/okapi/code-transformer?label=Packagist&labelColor=ebdbb2&style=flat&color=fe8019&logo=packagist"/>
  </a>

  <!-- Build -->
  <!--suppress HtmlUnknownTarget -->
  <a href="../../actions/workflows/tests.yml" target="_blank">
    <img alt="Build" src="https://img.shields.io/github/actions/workflow/status/okapi-web/php-code-transformer/tests.yml?label=Build&labelColor=ebdbb2&style=flat&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIGhlaWdodD0iMTYiPjxwYXRoIGZpbGw9IiM2YWFiMjAiIGQ9Ik0zMy45MTQgNDEzLjYxMmgxNDkuNTV2MjcuNTk1SDI3LjQ5NGMtMjYuMzQ4IDAtMzQuMTM2LTEzLjE5NC0yMS43MjktMzQuMzFMMTM3LjkxIDE4Ny43NTNWNjEuOTc1aC0yNi4wNzVjLTE5LjUwNCAwLTE5LjUwNC0yNy41OTUgMC0yNy41OTVoMTg5LjkzYzE5LjUwNSAwIDE5LjUwNSAyNy41OTUgMCAyNy41OTVIMjc1LjY5djEzMi44MjhoLTI3Ljk2M1Y2MS45NzVoLTgxLjg1NHYxMzIuODI4TDMzLjkxNCA0MTMuNjEyem0xMzUuNi0xNjkuMTg3TDg0LjY5MiAzODYuNTc0aDcwLjYwMWwxMDQuMzc1LTExMi45MDctMTUuNTgyLTI5LjI0MmgtNzQuNTd6bTE0NS45OTYgOS43ODNMMjA5LjUgMzY3LjUwNmwxMDYuMDEgMTEwLjI4NiAzMy41MzgtMzMuNTM4LTgwLjY1LTc2Ljc0OCA4MC42NS03OS43Ni0zMy41MzgtMzMuNTM4em01Ni45NDMgMzMuNTM3IDgwLjY1IDc5Ljc2LTgwLjY1IDc2Ljc1IDMzLjUzOCAzMy41MzdMNTEyIDM2Ny41MDYgNDA1Ljk5IDI1NC4yMDhsLTMzLjUzNyAzMy41Mzd6Ii8+PC9zdmc+">
  </a>
</p>

<!-- Coverage -->
<p align="center">
  <!-- Coverage - PHP 8.1 -->
  <a href="https://app.codecov.io/gh/okapi-web/php-code-transformer/flags" target="_blank">
    <img alt="Coverage - PHP 8.1" src="https://img.shields.io/codecov/c/github/okapi-web/php-code-transformer?flag=os-ubuntu-latest_php-8.1&label=Coverage - PHP 8.1&labelColor=ebdbb2&style=flat&logo=codecov&logoColor=FFC107&color=FFC107"/>
  </a>

  <!-- Coverage - PHP 8.2 -->
  <a href="https://app.codecov.io/gh/okapi-web/php-code-transformer/flags" target="_blank">
    <img alt="Coverage - PHP 8.2" src="https://img.shields.io/codecov/c/github/okapi-web/php-code-transformer?flag=os-ubuntu-latest_php-8.2&label=Coverage - PHP 8.2&labelColor=ebdbb2&style=flat&logo=codecov&logoColor=FFC107&color=FFC107"/>
  </a>
</p>

<h2 align="center">PHP Code Transformer is a PHP library that allows you to modify and transform the source code of a loaded PHP class.</h2>



## Installation

```shell
composer require okapi/code-transformer
```



# Usage

## 📖 List of contents

- [Create a kernel](#create-a-kernel)
- [Create a transformer](#create-a-transformer)
- [Initialize the kernel](#initialize-the-kernel)
- [Result](#result)



## Create a kernel

```php
<?php

use Okapi\CodeTransformer\CodeTransformerKernel;

// Extend from the "CodeTransformerKernel" class
class Kernel extends CodeTransformerKernel
{
    // Define a list of transformer classes
    protected array $transformers = [
        StringTransformer::class,
        UnPrivateTransformer::class,
    ];
}
```


## Create a transformer

```php
// String Transformer

<?php

use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Transformer;

// Extend from the "Transformer" class
class StringTransformer extends Transformer
{
    // Define the target class(es)
    public function getTargetClass(): string|array
    {
        // You can specify a single class or an array of classes
        // You can also use wildcards, see https://github.com/okapi-web/php-wildcards
        return MyTargetClass::class;
    }
    
    // The "transform" method will be called when the target class is loaded
    // Here you can modify the source code of the target class(es)
    public function transform(Code $code): void
    {
        // I recommend using the Microsoft\PhpParser library to parse the source
        // code. It's already included in the dependencies of this package and
        // the "$code->sourceFileNode" property contains the parsed source code.
        
        // But you can also use any other library or manually parse the source
        // code with basic PHP string functions and "$code->getOriginalSource()"

        $sourceFileNode = $code->sourceFileNode;

        // Iterate over all nodes
        foreach ($sourceFileNode->getDescendantNodes() as $node) {
            // Find 'Hello World!' string
            if ($node instanceof StringLiteral
                && $node->getStringContentsText() === 'Hello World!'
            ) {
                // Replace it with 'Hello from Code Transformer!'
                // Edit method accepts a Token class
                $code->edit(
                    $node->children,
                    "'Hello from Code Transformer!'",
                );
                
                // You can also manually edit the source code
                $code->editAt(
                    $node->getStartPosition() + 1,
                    $node->getWidth() - 2,
                    "Hello from Code Transformer!",
                );

                // Append a new line of code
                $code->append('$iAmAppended = true;');
            }
        }
    }
}
```

```php
// UnPrivate Transformer

<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Microsoft\PhpParser\TokenKind;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Transformer;

// Replace all "private" keywords with "public"
class UnPrivateTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return MyTargetClass::class;
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->sourceFileNode;

        // Iterate over all tokens
        foreach ($sourceFileNode->getDescendantTokens() as $token) {
            // Find "private" keyword
            if ($token->kind === TokenKind::PrivateKeyword) {
                // Replace it with "public"
                $code->edit($token, 'public');
            }
        }
    }
}
```


## Initialize the kernel

```php
// Initialize the kernel early in the application lifecycle

<?php

use MyKernel;

require_once __DIR__ . '/vendor/autoload.php';

$kernel = new MyKernel(
    // The directory where the transformed source code will be stored
    cacheDir: __DIR__ . '/var/cache',
    
    // The cache file mode
    cacheFileMode: 0777,
);
```


## Result

```php
<?php

// Just use your classes as usual
$myTargetClass = new MyTargetClass();

$myTargetClass->myPrivateProperty; // You can't get me!
$myTargetClass->myPrivateMethod(); // Hello from Code Transformer!
```


```php
// MyTargetClass.php

<?php

class MyTargetClass
{
    private string $myPrivateProperty = "You can't get me!";

    private function myPrivateMethod(): void
    {
        echo 'Hello World!';
    }
}
```

```php
// MyTargetClass.php (transformed)

<?php

class MyTargetClass
{
    public string $myPrivateProperty = "You can't get me!";
    
    public function myPrivateMethod(): void
    {
        echo 'Hello from Code Transformer!';
    }
}
$iAmAppended = true;
```


# How it works

- The `Kernel` registers multiple services 

  - The `TransformerContainer` service stores the list of transformers and their configuration

  - The `CacheStateManager` service manages the cache state 

  - The `StreamFilter` service registers a [PHP Stream Filter](https://www.php.net/manual/wrappers.php.php#wrappers.php.filter)
    which allows to modify the source code before it is loaded by PHP 

  - The `AutoloadInterceptor` service overloads the Composer autoloader, which handles the loading of classes


## General workflow when a class is loaded

- The `AutoloadInterceptor` service intercepts the loading of a class
  - It expects a class file path 

- The `TransformerContainer` matches the class name with the list of transformer target classes

- If the class is matched, we query the cache state to see if the transformed source code is already cached
  - Check if the cache is valid: 
    - Modification time of the caching process is less than the modification time of the source file or the transformers
    - Check if the cache file, the source file and the transformers exist
    - Check if the number of transformers is the same as the number of transformers in the cache
  - If the cache is valid, we load the transformed source code from the cache
  - If not, we convert the class file path to a stream filter path

- The `StreamFilter` modifies the source code by applying the matching transformers
  - If the modified source code is different from the original source code, we cache the transformed source code
  - If not, we cache it anyway, but without a cached source file path, so that the transformation process is not repeated



## Testing

- Run `composer run-script test`<br>
  or
- Run `composer run-script test-coverage`



## Show your support

Give a ⭐ if this project helped you!



## 📝 License

Copyright © 2023 [Valentin Wotschel](https://github.com/WalterWoshid).<br>
This project is [MIT](https://opensource.org/licenses/MIT) licensed.
